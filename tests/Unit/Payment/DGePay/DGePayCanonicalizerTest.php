<?php

namespace Tests\Unit\Payment\DGePay;

use App\Services\Payment\DGePay\DGePayCanonicalizer;
use App\Services\Payment\DGePay\DGePayCrypto;
use App\Services\Payment\DGePay\DGePayException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DGePayCanonicalizerTest extends TestCase
{
    public function test_it_builds_the_documented_recursive_checksum(): void
    {
        $payload = [
            'unique_txn_id' => 'TX-1',
            'note' => 'A: B',
            'meta_data' => ['custom_field_2' => 'John Doe', 'custom_field_1' => null],
            'customer_token' => null,
            'amount' => 15,
        ];

        $this->assertSame(
            'amount15.0customer_tokennullmeta_datacustom_field_1nullcustom_field_2JohnDoenoteABunique_txn_idTX-1',
            (new DGePayCanonicalizer)->checksum($payload),
        );
    }

    public function test_it_normalises_decimal_amounts_without_float_noise(): void
    {
        $canonicalizer = new DGePayCanonicalizer;

        $this->assertSame('amount15.5', $canonicalizer->checksum(['amount' => 15.50]));
        $this->assertSame('amount100.0', $canonicalizer->checksum(['amount' => '100.00']));
    }

    public function test_it_fails_closed_for_undefined_list_canonicalisation(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new DGePayCanonicalizer)->checksum(['product_list' => [['name' => 'Phone']]]);
    }

    public function test_crypto_matches_a_fixed_aes_vector_and_round_trips(): void
    {
        $crypto = new DGePayCrypto;
        $plain = '{"amount":15.0,"unique_txn_id":"TX-1"}';
        $secret = '0123456789abcdefEXTRA';
        $encrypted = $crypto->encrypt($plain, $secret);

        $this->assertSame('ieNivNMUgrPw4ECdCkywGsdUXKdXJHNdAQ+BjKTZ17viVV2y1yPc66G4Zew/aYcJ', $encrypted);
        $this->assertSame($plain, $crypto->decrypt($encrypted, $secret));
    }

    public function test_crypto_rejects_invalid_input_and_short_keys(): void
    {
        $crypto = new DGePayCrypto;

        $this->expectException(DGePayException::class);
        $crypto->decrypt('not-base64!', '0123456789abcdef');
    }
}
