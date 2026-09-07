<?php

namespace App\Services\Payment\DGePay;

use InvalidArgumentException;

/** Reproduces DGePay's documented recursive checksum algorithm. */
class DGePayCanonicalizer
{
    public function checksum(array $payload): string
    {
        $parts = [];
        $this->appendObject($payload, $parts);

        return implode('', $parts);
    }

    private function appendObject(array $object, array &$parts): void
    {
        if (array_is_list($object)) {
            throw new InvalidArgumentException('DGePay checksum does not define top-level list handling.');
        }

        ksort($object, SORT_STRING);
        foreach ($object as $key => $value) {
            $parts[] = (string) $key;

            if ($value === null) {
                $parts[] = 'null';

                continue;
            }

            if (is_array($value)) {
                if (array_is_list($value)) {
                    throw new InvalidArgumentException("DGePay checksum does not define list handling for {$key}.");
                }
                $this->appendObject($value, $parts);

                continue;
            }

            $string = $key === 'amount'
                ? $this->normaliseAmount($value)
                : $this->stringify($value);
            $parts[] = str_replace(':', '', preg_replace('/\s+/u', '', $string) ?? '');
        }
    }

    private function normaliseAmount(mixed $value): string
    {
        if (!is_int($value) && !is_float($value) && !is_string($value)) {
            throw new InvalidArgumentException('DGePay amount must be numeric.');
        }
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('DGePay amount must be numeric.');
        }

        $amount = (float) $value;
        if (!is_finite($amount) || $amount <= 0) {
            throw new InvalidArgumentException('DGePay amount must be a positive finite number.');
        }

        $normalised = rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.');

        return str_contains($normalised, '.') ? $normalised : $normalised.'.0';
    }

    private function stringify(mixed $value): string
    {
        return match (true) {
            is_bool($value) => $value ? 'true' : 'false',
            is_scalar($value) => (string) $value,
            default => throw new InvalidArgumentException('Unsupported DGePay checksum value.'),
        };
    }
}
