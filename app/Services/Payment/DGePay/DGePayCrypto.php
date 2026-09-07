<?php

namespace App\Services\Payment\DGePay;

/** AES-128-ECB/PKCS7 compatibility required by DGePay's merchant API. */
class DGePayCrypto
{
    public function encrypt(string $plaintext, string $secret): string
    {
        $key = $this->key($secret);
        $encrypted = openssl_encrypt($plaintext, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        if ($encrypted === false) {
            throw new DGePayException('DGePay payload encryption failed.');
        }

        return base64_encode($encrypted);
    }

    public function decrypt(string $encoded, string $secret): string
    {
        $ciphertext = base64_decode($encoded, true);
        if ($ciphertext === false || $ciphertext === '') {
            throw new DGePayException('DGePay return data is not valid Base64.');
        }

        $decrypted = openssl_decrypt($ciphertext, 'AES-128-ECB', $this->key($secret), OPENSSL_RAW_DATA);
        if ($decrypted === false || $decrypted === '') {
            throw new DGePayException('DGePay return data could not be decrypted.');
        }

        return $decrypted;
    }

    private function key(string $secret): string
    {
        if (strlen($secret) < 16) {
            throw new DGePayException('DGePay client secret must contain at least 16 bytes.');
        }

        return substr($secret, 0, 16);
    }
}
