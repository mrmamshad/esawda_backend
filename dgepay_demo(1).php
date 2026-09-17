<?php
/**
 * DGePay Payment Gateway PHP Demo
 * --------------------------------
 * Single-file demo for merchants.
 * 
 * 1. Authenticate with DGePay (Basic Auth) → JWT token
 * 2. Show HTML form to initiate payment
 * 3. Generate checksum + Signature (HMAC-SHA256) + AES-128-ECB encrypted payload
 * 4. Call /initiate_payment and auto-redirect customer to payment page
 * 5. After customer returns, merchant can click "Check Status" to call /check_transaction_status
 */

session_start();

/* ------------------------------------------------------------------------
 * CONFIGURATION - Load from session or use defaults
 * --------------------------------------------------------------------- */

// Handle configuration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_config') {
    $_SESSION['config'] = [
        'base_url' => rtrim($_POST['base_url'] ?? '', '/') . '/',
        'client_id' => $_POST['client_id'] ?? '',
        'api_key' => $_POST['api_key'] ?? '',
        'secret_key' => $_POST['secret_key'] ?? '',
        'redirect_url' => $_POST['redirect_url'] ?? '',
    ];
    // Clear token when config changes
    unset($_SESSION['dgepay_token']);
    unset($_SESSION['auth_error']);
}

// Load configuration from session (no defaults - merchant must configure)
$config = $_SESSION['config'] ?? [];

$base_url     = $config['base_url'] ?? '';
$client_id    = $config['client_id'] ?? '';
$api_key      = $config['api_key'] ?? '';
$secret_key   = $config['secret_key'] ?? '';
$redirect_url = $config['redirect_url'] ?? (isset($_SERVER['HTTP_HOST']) ? 
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . 
    '://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] : 
    '');

// Check if configuration is complete
$config_complete = !empty($base_url) && !empty($client_id) && 
                   !empty($api_key) && !empty($secret_key) && !empty($redirect_url);

/* ------------------------------------------------------------------------
 * HELPER: Simple CURL function
 * --------------------------------------------------------------------- */
function http_post($url, $headers = [], $body = null) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    
    // SSL Certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    // Try to find CA certificate bundle
    $caBundlePaths = [
        __DIR__ . '/cacert.pem', // Local file
        ini_get('curl.cainfo'), // php.ini setting
        ini_get('openssl.cafile'), // php.ini setting
    ];
    
    $caBundleFound = false;
    foreach ($caBundlePaths as $path) {
        if (!empty($path) && file_exists($path)) {
            curl_setopt($ch, CURLOPT_CAINFO, $path);
            $caBundleFound = true;
            break;
        }
    }
    
    // If no CA bundle found, disable SSL verification (NOT RECOMMENDED for production)
    // This is a fallback for development/testing only
    if (!$caBundleFound) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    }
    
    $responseBody = curl_exec($ch);
    $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError    = curl_error($ch);
    curl_close($ch);

    return [
        'status'   => $httpCode,
        'body'     => $responseBody,
        'curl_err' => $curlError,
    ];
}

/* ------------------------------------------------------------------------
 * HELPER: Authenticate and store Bearer token in session
 * --------------------------------------------------------------------- */
function authenticate_if_needed($base_url, $client_id, $secret_key) {
    if (!empty($_SESSION['dgepay_token'])) {
        return $_SESSION['dgepay_token'];
    }

    $auth_url = $base_url . "authenticate";
    $basic    = base64_encode($client_id . ":" . $secret_key);

    $headers = [
        "Authorization: Basic " . $basic,
    ];

    $resp = http_post($auth_url, $headers);

    if ($resp['curl_err']) {
        $_SESSION['auth_error'] = "Curl Error during authentication: " . $resp['curl_err'];
        return null;
    }

    if ($resp['status'] < 200 || $resp['status'] >= 300) {
        $_SESSION['auth_error'] = "Authentication failed with HTTP " . $resp['status'] . ": " . $resp['body'];
        return null;
    }

    $data = json_decode($resp['body'], true);

    // Try common token field names (including nested data.access_token)
    $token = $data['token'] ?? $data['access_token'] ?? $data['data']['access_token'] ?? $data['data']['token'] ?? null;

    if (!$token) {
        $_SESSION['auth_error'] = "Unable to extract token from response: " . $resp['body'];
        return null;
    }

    $_SESSION['dgepay_token'] = $token;
    return $token;
}

/* ------------------------------------------------------------------------
 * HELPER: Normalize amount (JS equivalent)
 * --------------------------------------------------------------------- */
function normalize_amount($value) {
    if (is_numeric($value)) {
        if (intval($value) == $value) {
            // integer → "15.0"
            return $value . ".0";
        }
        return (string)$value;
    }
    return $value;
}

/* ------------------------------------------------------------------------
 * HELPER: Build checksum string (replicates Postman pre-script logic)
 * --------------------------------------------------------------------- */
function build_checksum_string($payload) {
    $checksumParts = [];

    $build = function($obj) use (&$checksumParts, &$build) {
        if (!is_array($obj)) {
            return;
        }

        // Sort keys alphabetically
        $keys = array_keys($obj);
        sort($keys, SORT_STRING);

        foreach ($keys as $key) {
            $value = $obj[$key];
            $checksumParts[] = $key;

            if (is_null($value)) {
                $checksumParts[] = "null";
            } elseif (is_array($value) && array_keys($value) !== range(0, count($value) - 1)) {
                // Associative array → nested object
                $build($value);
            } else {
                // Primitive or list – treat as string
                if ($key === 'amount') {
                    $stringValue = normalize_amount($value);
                } else {
                    $stringValue = (string)$value;
                }

                // Remove spaces and colons
                $stringValue = preg_replace('/\s+/', '', $stringValue);
                $stringValue = str_replace(':', '', $stringValue);

                $checksumParts[] = $stringValue;
            }
        }
    };

    $build($payload);

    return implode('', $checksumParts);
}

/* ------------------------------------------------------------------------
 * HELPER: Generate HMAC-SHA256 signature and Base64 encode
 * --------------------------------------------------------------------- */
function generate_signature($api_key, $checksumString) {
    $raw = hash_hmac('sha256', $checksumString, $api_key, true);
    return base64_encode($raw);
}

/* ------------------------------------------------------------------------
 * HELPER: AES-128-ECB encrypt + Base64
 * --------------------------------------------------------------------- */
function encrypt_payload($secret_key, $jsonBody) {
    $key16 = substr($secret_key, 0, 16); // Use first 16 chars
    // Use default PKCS7 padding (matches Postman CryptoJS behavior)
    $encrypted = openssl_encrypt(
        $jsonBody,
        'AES-128-ECB',
        $key16,
        OPENSSL_RAW_DATA
    );

    return base64_encode($encrypted);
}

/* ------------------------------------------------------------------------
 * HELPER: AES-128-ECB decrypt (reverse of encrypt_payload)
 * --------------------------------------------------------------------- */
function decrypt_payload($secret_key, $encryptedBase64) {
    if (empty($secret_key) || empty($encryptedBase64)) {
        return false;
    }
    
    $key16 = substr($secret_key, 0, 16); // Use first 16 chars
    $encrypted = base64_decode($encryptedBase64, true); // strict mode
    
    if ($encrypted === false) {
        return false; // Base64 decode failed
    }
    
    // Decrypt using AES-128-ECB with default PKCS7 padding
    $decrypted = openssl_decrypt(
        $encrypted,
        'AES-128-ECB',
        $key16,
        OPENSSL_RAW_DATA
    );
    
    return $decrypted;
}

/* ------------------------------------------------------------------------
 * HANDLE: Initiate Payment
 * --------------------------------------------------------------------- */
$initiate_response = null;
$status_response   = null;
$auth_error        = $_SESSION['auth_error'] ?? null;

// Handle clear session action
if (isset($_GET['action']) && $_GET['action'] === 'clear_session') {
    session_destroy();
    session_start();
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle new transaction action (clear transaction data but keep config)
if (isset($_GET['action']) && $_GET['action'] === 'new_transaction') {
    unset($_SESSION['unique_txn_id']);
    unset($_SESSION['payment_webview_url']);
    unset($_SESSION['debug_info']);
    unset($_SESSION['status_debug_info']);
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Handle manual authentication
    if ($action === 'authenticate') {
        unset($_SESSION['dgepay_token']);
        unset($_SESSION['auth_error']);
    }

    // Validate configuration before API calls (except save_config)
    if ($action !== 'save_config' && !$config_complete) {
        $_SESSION['config_error'] = "Please configure all credentials in the Configuration section above before using the APIs.";
    } else {
        unset($_SESSION['config_error']);
        
        // Ensure we have token before any action
        $token = authenticate_if_needed($base_url, $client_id, $secret_key);

        if (!$token) {
            $auth_error = $_SESSION['auth_error'] ?? "Unknown auth error.";
        } else {
        if ($action === 'initiate_payment') {
            // Collect fields from form
            $amount        = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
            $phone_number  = $_POST['phone_number'] ?? '';
            $note          = $_POST['note'] ?? '';
            $custom1       = $_POST['custom_field_1'] ?? '';
            $custom2       = $_POST['custom_field_2'] ?? '';
            $custom3       = $_POST['custom_field_3'] ?? '';
            $unique_user_reference = trim($_POST['unique_user_reference'] ?? '');

            // Generate unique transaction id
            $unique_txn_id = "TXN-" . uniqid();

            // Save for later status check
            $_SESSION['unique_txn_id'] = $unique_txn_id;

            // Build original JSON payload
            $payload = [
                "amount"          => $amount,
                "customer_token"  => null,
                "note"            => $note,
                "payee_information" => [
                    "dial_code"    => "+88",
                    "phone_number" => $phone_number,
                ],
                "payment_method"  => null,
                "redirect_url"    => $redirect_url,
                "unique_txn_id"   => $unique_txn_id,
                "meta_data"       => [
                    "custom_field_1" => $custom1,
                    "custom_field_2" => $custom2,
                    "custom_field_3" => $custom3,
                ],
            ];
            
            // Add unique_user_reference only if provided (optional parameter)
            if (!empty($unique_user_reference)) {
                $payload["unique_user_reference"] = $unique_user_reference;
            }

            $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);

            // Build checksum string
            $checksumString = build_checksum_string($payload);

            // Generate signature
            $signature = generate_signature($api_key, $checksumString);

            // Encrypt payload
            $encryptedBody = encrypt_payload($secret_key, $jsonBody);

            // Store debug info for display
            $_SESSION['debug_info'] = [
                'original_json' => $jsonBody,
                'checksum_string' => $checksumString,
                'signature' => $signature,
                'encrypted_payload' => $encryptedBody,
                'unique_txn_id' => $unique_txn_id
            ];

            // Call initiate_payment
            $url = $base_url . "initiate_payment";
            $headers = [
                "Authorization: Bearer " . $token,
                "Signature: " . $signature,
                "Content-Type: text/plain",
            ];

            $resp = http_post($url, $headers, $encryptedBody);
            $initiate_response = $resp;

            // Try to parse redirect URL from response
            $respData = json_decode($resp['body'], true);
            $redirectPaymentUrl = $respData['redirect_url'] 
                ?? $respData['payment_url'] 
                ?? $respData['data']['webview_url'] 
                ?? $respData['data']['redirect_url'] 
                ?? null;

            // Store webview_url in session for manual redirect button
            if ($redirectPaymentUrl) {
                $_SESSION['payment_webview_url'] = $redirectPaymentUrl;
            }

            // Auto-redirect only if enabled (commented out for manual control)
            // if ($resp['status'] >= 200 && $resp['status'] < 300 && $redirectPaymentUrl) {
            //     // Auto-redirect to payment page
            //     echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>DGePay Redirecting...</title></head><body>";
            //     echo "<p>Redirecting to DGePay Payment Page...</p>";
            //     echo "<p>If you are not redirected automatically, <a href='" . htmlspecialchars($redirectPaymentUrl, ENT_QUOTES, 'UTF-8') . "'>click here</a>.</p>";
            //     echo "<script>window.location.href = " . json_encode($redirectPaymentUrl) . ";</script>";
            //     echo "</body></html>";
            //     exit;
            // }
        }

        /* ----------------------------------------------------------------
         * HANDLE: Check Transaction Status
         * ------------------------------------------------------------- */
        if ($action === 'check_status') {
            $unique_txn_id = $_SESSION['unique_txn_id'] ?? ($_POST['unique_txn_id'] ?? null);

            if ($unique_txn_id) {
                $statusPayload = [
                    "unique_txn_id" => $unique_txn_id,
                ];

                $jsonStatus = json_encode($statusPayload, JSON_UNESCAPED_SLASHES);

                // Build checksum string
                $checksumString = build_checksum_string($statusPayload);

                // Generate signature
                $signature = generate_signature($api_key, $checksumString);

                // Encrypt payload
                $encryptedBody = encrypt_payload($secret_key, $jsonStatus);

                // Store debug info for status check
                $_SESSION['status_debug_info'] = [
                    'original_json' => $jsonStatus,
                    'checksum_string' => $checksumString,
                    'signature' => $signature,
                    'encrypted_payload' => $encryptedBody,
                    'unique_txn_id' => $unique_txn_id
                ];

                $url = $base_url . "check_transaction_status";
                $headers = [
                    "Authorization: Bearer " . $token,
                    "Signature: " . $signature,
                    "Content-Type: text/plain",
                ];

                $status_response = http_post($url, $headers, $encryptedBody);
            } else {
                $status_response = [
                    'status'   => 0,
                    'body'     => 'No unique_txn_id found in session. Please initiate a payment first.',
                    'curl_err' => '',
                ];
            }
        }
        }
    }
}

// Handle redirect data parameter (decrypt callback data)
$decrypted_data = null;
$decrypt_error = null;
if (isset($_GET['data']) && !empty($_GET['data'])) {
    if (!$config_complete) {
        $decrypt_error = "Configuration incomplete. Please configure all credentials above.";
    } elseif (empty($secret_key)) {
        $decrypt_error = "Secret Key is not configured. Please set it in the configuration above.";
    } else {
        try {
            // URL decode the data parameter (Base64 data in URLs may be URL-encoded)
            $rawData = $_GET['data'];
            $encryptedData = urldecode($rawData);
            
            // Clean the Base64 string:
            // 1. Trim whitespace
            // 2. Replace all spaces with + (some servers convert + to space in URLs)
            // 3. Remove any newlines or other whitespace
            $encryptedData = trim($encryptedData);
            $encryptedData = str_replace(' ', '+', $encryptedData);
            $encryptedData = preg_replace('/\s+/', '', $encryptedData); // Remove any remaining whitespace
            
            // Fix Base64 padding if needed (Base64 length must be multiple of 4)
            $padding = strlen($encryptedData) % 4;
            if ($padding > 0) {
                $encryptedData .= str_repeat('=', 4 - $padding);
            }
            
            // Validate Base64 format
            if (empty($encryptedData)) {
                $decrypt_error = "Empty data after cleaning. The encrypted data may be corrupted.";
                $decrypted_data = null;
            } elseif (!preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $encryptedData)) {
                $decrypt_error = "Invalid Base64 format detected. Found invalid characters. The data may be corrupted or incorrectly encoded.";
                $decrypted_data = null;
            } else {
                $decrypted_data = decrypt_payload($secret_key, $encryptedData);
                
                if ($decrypted_data === false) {
                    $decrypt_error = "Decryption failed. Possible reasons: Invalid Base64 data, incorrect Secret Key, or decryption error. Please verify your Secret Key matches the one used during payment initiation.";
                    $decrypted_data = null;
                } elseif (empty($decrypted_data)) {
                    $decrypt_error = "Decryption returned empty result. Please verify your Secret Key matches the one used during payment initiation.";
                    $decrypted_data = null;
                } else {
                    // Try to parse as JSON
                    $decrypted_json = json_decode($decrypted_data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $decrypted_data = $decrypted_json; // Store as array for better display
                    }
                }
            }
        } catch (Exception $e) {
            $decrypt_error = "Decryption failed: " . $e->getMessage();
            $decrypted_data = null;
        } catch (Error $e) {
            $decrypt_error = "Decryption error: " . $e->getMessage();
            $decrypted_data = null;
        }
    }
}

// For displaying token or errors
$token_for_view = $_SESSION['dgepay_token'] ?? null;
$unique_txn_id_view = $_SESSION['unique_txn_id'] ?? null;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>DGePay PHP Payment Gateway Demo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { margin-bottom: 5px; }
        .section { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 6px; }
        label { display: block; margin-top: 8px; }
        input[type="text"], input[type="number"] { width: 300px; padding: 5px; }
        textarea { width: 100%; height: 120px; }
        .btn { margin-top: 10px; padding: 6px 12px; cursor: pointer; background: #007bff; color: white; border: none; border-radius: 4px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .error { color: red; }
        pre { background: #f6f6f6; padding: 10px; border-radius: 4px; overflow-x: auto; }
        pre.json-response { 
            background: #f6f6f6; 
            padding: 15px; 
            border-radius: 4px; 
            overflow-x: auto; 
            white-space: pre-wrap; 
            word-wrap: break-word; 
            max-width: 100%; 
            font-size: 13px; 
            line-height: 1.5;
        }
        small { color: #555; }
    </style>
</head>
<body>

<h1>DGePay Payment Gateway – PHP Demo</h1>

<div class="section" style="background: #f0f8ff; border: 2px solid #007bff;">
    <h2>⚙️ Configuration <span style="color: red;">*</span></h2>
    <p><em><strong>Required:</strong> Please configure all credentials below before using any APIs. Settings are saved in session and can be used for both UAT and Production.</em></p>
    
    <?php if (isset($_SESSION['config_error'])): ?>
        <p class="error" style="background: #ffe6e6; padding: 10px; border-radius: 4px; border: 1px solid #ff9999;">
            <strong>⚠️ Configuration Required:</strong> <?php echo htmlspecialchars($_SESSION['config_error'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php endif; ?>
    
    <?php if (!$config_complete): ?>
        <p style="background: #fff3cd; padding: 10px; border-radius: 4px; border: 1px solid #ffc107; color: #856404;">
            <strong>⚠️ Incomplete Configuration:</strong> Please fill in all fields below to use the APIs.
        </p>
    <?php endif; ?>
    
    <form method="post">
        <input type="hidden" name="action" value="save_config">
        
        <label><strong>Base URL: <span style="color: red;">*</span></strong> <small>(e.g., https://api-uat.dgepay.net/dipon/v3/payment_gateway/ or https://api.dgepay.net/dipon/v3/payment_gateway/)</small></label>
        <input type="text" name="base_url" value="<?php echo htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8'); ?>" required style="width: 100%; max-width: 600px; padding: 8px;" placeholder="https://api-uat.dgepay.net/dipon/v3/payment_gateway/">
        
        <label><strong>Client ID: <span style="color: red;">*</span></strong> <small>(For Basic Auth - used in /authenticate)</small></label>
        <input type="text" name="client_id" value="<?php echo htmlspecialchars($client_id, ENT_QUOTES, 'UTF-8'); ?>" required style="width: 100%; max-width: 600px; padding: 8px;" placeholder="Your client ID">
        
        <label><strong>API Key: <span style="color: red;">*</span></strong> <small>(For HMAC signature generation)</small></label>
        <input type="text" name="api_key" value="<?php echo htmlspecialchars($api_key, ENT_QUOTES, 'UTF-8'); ?>" required style="width: 100%; max-width: 600px; padding: 8px;" placeholder="Your API key">
        
        <label><strong>Secret Key: <span style="color: red;">*</span></strong> <small>(For Basic Auth and AES encryption - first 16 chars used for encryption)</small></label>
        <input type="text" name="secret_key" value="<?php echo htmlspecialchars($secret_key, ENT_QUOTES, 'UTF-8'); ?>" required style="width: 100%; max-width: 600px; padding: 8px;" placeholder="Your secret key">
        
        <label><strong>Redirect URL: <span style="color: red;">*</span></strong> <small>(Must be registered with DGePay - where customer returns after payment)</small></label>
        <input type="text" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8'); ?>" required style="width: 100%; max-width: 600px; padding: 8px;" placeholder="http://yourdomain.com/dgepay_demo.php">
        
        <button type="submit" class="btn" style="margin-top: 15px;">💾 Save Configuration</button>
    </form>
    <?php if ($config_complete): ?>
        <p style="margin-top: 10px; color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; border: 1px solid #c3e6cb;">
            <strong>✓ Configuration Complete!</strong> You can now use the APIs below.
        </p>
    <?php endif; ?>
</div>

<?php if (isset($_GET['data']) && !empty($_GET['data'])): ?>
<div class="section" style="background: #fff3cd; border: 2px solid #ffc107;">
    <h2>🔄 Payment Callback Data</h2>
    <p><em>This data was received from DGePay after payment completion.</em></p>
    
    <?php if ($decrypt_error): ?>
        <p class="error" style="background: #ffe6e6; padding: 10px; border-radius: 4px; border: 1px solid #ff9999;">
            <strong>⚠️ Decryption Error:</strong> <?php echo htmlspecialchars($decrypt_error, ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <p><small>Make sure your Secret Key in configuration matches the one used during payment initiation.</small></p>
        <details style="margin-top: 10px;">
            <summary style="cursor: pointer; color: #007bff;">Show Encrypted Data (for debugging)</summary>
            <pre style="margin-top: 10px; font-size: 11px; word-break: break-all;"><?php echo htmlspecialchars($_GET['data'], ENT_QUOTES, 'UTF-8'); ?></pre>
        </details>
    <?php elseif ($decrypted_data): ?>
        <h3>Decrypted Callback Data:</h3>
        <?php if (is_array($decrypted_data)): ?>
            <pre class="json-response"><?php echo htmlspecialchars(json_encode($decrypted_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?></pre>
        <?php else: ?>
            <pre><?php echo htmlspecialchars($decrypted_data, ENT_QUOTES, 'UTF-8'); ?></pre>
        <?php endif; ?>
        
        <p style="margin-top: 15px;">
            <a href="?action=new_transaction" class="btn" style="background: #28a745;">
                🆕 Start New Transaction
            </a>
        </p>
    <?php else: ?>
        <p class="error" style="background: #ffe6e6; padding: 10px; border-radius: 4px; border: 1px solid #ff9999;">
            <strong>⚠️ Unable to decrypt data.</strong> Please check your configuration and ensure Secret Key is correct.
        </p>
        <details style="margin-top: 10px;">
            <summary style="cursor: pointer; color: #007bff;">Show Encrypted Data (for debugging)</summary>
            <pre style="margin-top: 10px; font-size: 11px; word-break: break-all;"><?php echo htmlspecialchars($_GET['data'], ENT_QUOTES, 'UTF-8'); ?></pre>
        </details>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="section">
    <h2>Authentication Status</h2>
    <?php if ($auth_error): ?>
        <p class="error"><strong>Authentication Error:</strong> <?php echo htmlspecialchars($auth_error, ENT_QUOTES, 'UTF-8'); ?></p>
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="authenticate">
            <button type="submit" class="btn">Retry Authentication</button>
        </form>
    <?php elseif ($token_for_view): ?>
        <p><strong>Token:</strong> <small><?php echo htmlspecialchars(substr($token_for_view, 0, 40), ENT_QUOTES, 'UTF-8'); ?>...</small></p>
        <p><em>Token generated via /authenticate using Basic Auth. Token is cached in session.</em></p>
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="authenticate">
            <button type="submit" class="btn">Refresh Token</button>
        </form>
    <?php else: ?>
        <p>No token yet. Click below to authenticate.</p>
        <form method="post" style="display: inline;">
            <input type="hidden" name="action" value="authenticate">
            <button type="submit" class="btn">Authenticate Now</button>
        </form>
    <?php endif; ?>
    <a href="?action=clear_session" class="btn btn-danger" style="margin-left: 10px;">Clear Session</a>
</div>

<div class="section">
    <h2>Initiate Payment</h2>
    <form method="post">
        <input type="hidden" name="action" value="initiate_payment">

        <label>Amount</label>
        <input type="number" step="0.01" name="amount" placeholder="Enter amount" required>

        <label>Customer Phone Number</label>
        <input type="text" name="phone_number" placeholder="Enter customer phone number" required>

        <label>Note</label>
        <input type="text" name="note" placeholder="Enter payment note/description">

        <label>Unique User Reference <small>(Optional)</small></label>
        <input type="text" name="unique_user_reference" placeholder="Optional unique user reference">

        <label>Custom Field 1</label>
        <input type="text" name="custom_field_1" placeholder="Optional custom field 1">

        <label>Custom Field 2</label>
        <input type="text" name="custom_field_2" placeholder="Optional custom field 2">

        <label>Custom Field 3</label>
        <input type="text" name="custom_field_3" placeholder="Optional custom field 3">

        <p><strong>Redirect URL (fixed):</strong> <?php echo htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8'); ?></p>

        <button type="submit" class="btn">Initiate Payment &amp; Redirect</button>
    </form>

    <?php if (isset($_SESSION['debug_info'])): ?>
        <h3>Debug Information (for Integration Reference)</h3>
        <p><strong>Original JSON Payload:</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['debug_info']['original_json'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Checksum String (used for signature):</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['debug_info']['checksum_string'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Generated Signature (HMAC-SHA256 + Base64):</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['debug_info']['signature'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Encrypted Payload (AES-128-ECB + Base64):</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['debug_info']['encrypted_payload'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Unique Transaction ID:</strong> <?php echo htmlspecialchars($_SESSION['debug_info']['unique_txn_id'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if ($initiate_response): ?>
        <h3>API Response</h3>
        <p><strong>HTTP Status:</strong> <?php echo (int)$initiate_response['status']; ?></p>
        <?php if ($initiate_response['curl_err']): ?>
            <p class="error"><strong>CURL Error:</strong> <?php echo htmlspecialchars($initiate_response['curl_err'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php
        // Try to format JSON response
        $responseBody = $initiate_response['body'];
        $jsonData = json_decode($responseBody, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Valid JSON - format it nicely
            $formattedJson = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            echo '<pre class="json-response">' . htmlspecialchars($formattedJson, ENT_QUOTES, 'UTF-8') . '</pre>';
        } else {
            // Not valid JSON - show raw response
            echo '<pre>' . htmlspecialchars($responseBody, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
        ?>
        
        <?php 
        // Check if we have a webview_url for redirect
        $webviewUrl = $_SESSION['payment_webview_url'] ?? null;
        if ($webviewUrl && $initiate_response['status'] >= 200 && $initiate_response['status'] < 300): 
        ?>
            <p style="margin-top: 15px;">
                <a href="<?php echo htmlspecialchars($webviewUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn" style="background: #28a745;">
                    🚀 Go to Payment Page
                </a>
            </p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="section">
    <h2>Check Transaction Status (Manual)</h2>
    <p>After the customer completes payment and returns to this URL, click the button below to query status.</p>

    <form method="post">
        <input type="hidden" name="action" value="check_status">
        <label>Unique Transaction ID (auto-stored from last initiate)</label>
        <input type="text" name="unique_txn_id" value="<?php echo htmlspecialchars($unique_txn_id_view ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        <button type="submit" class="btn">Check Status</button>
    </form>

    <?php if (isset($_SESSION['status_debug_info'])): ?>
        <h3>Debug Information (for Integration Reference)</h3>
        <p><strong>Original JSON Payload:</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['status_debug_info']['original_json'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Checksum String (used for signature):</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['status_debug_info']['checksum_string'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Generated Signature (HMAC-SHA256 + Base64):</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['status_debug_info']['signature'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Encrypted Payload (AES-128-ECB + Base64):</strong></p>
        <pre><?php echo htmlspecialchars($_SESSION['status_debug_info']['encrypted_payload'], ENT_QUOTES, 'UTF-8'); ?></pre>
        
        <p><strong>Unique Transaction ID:</strong> <?php echo htmlspecialchars($_SESSION['status_debug_info']['unique_txn_id'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <?php if ($status_response): ?>
        <h3>API Response</h3>
        <p><strong>HTTP Status:</strong> <?php echo (int)$status_response['status']; ?></p>
        <?php if ($status_response['curl_err']): ?>
            <p class="error"><strong>CURL Error:</strong> <?php echo htmlspecialchars($status_response['curl_err'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <?php
        // Try to format JSON response
        $responseBody = $status_response['body'];
        $jsonData = json_decode($responseBody, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Valid JSON - format it nicely
            $formattedJson = json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            echo '<pre class="json-response">' . htmlspecialchars($formattedJson, ENT_QUOTES, 'UTF-8') . '</pre>';
        } else {
            // Not valid JSON - show raw response
            echo '<pre>' . htmlspecialchars($responseBody, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
        ?>
    <?php endif; ?>
</div>

</body>
</html>
