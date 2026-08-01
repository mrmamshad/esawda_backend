<?php

namespace App\Http\Resources\V1;

/**
 * One filter field for the dynamic "Advance Filter" panel on the
 * Browse page (Vehicles → Year, Engine, Fuel, Transmission, etc.).
 *
 * The frontend picks a widget by `type`:
 *   - range   → two number inputs "From / Into" (2 col_span)
 *   - number  → single number input
 *   - text    → single text input
 *   - enum    → select dropdown fed by `options`
 *   - bool    → toggle
 */
class FilterFieldResource extends BaseResource
{
    public function toArray($request): array
    {
        $type    = $this->normalizeType($this->custom_type);
        $options = $this->parseOptions();

        return [
            'id'          => (int) $this->custom_id,
            'name'        => $this->custom_name ?: 'field_' . $this->custom_id,
            'label'       => $this->custom_title ?: $this->custom_name,
            'type'        => $type,
            'widget'      => $this->widgetFor($type),
            'required'    => $this->bool($this->custom_required),
            'default'     => $this->custom_default ?: null,
            'min'         => (int) $this->custom_min ?: null,
            'max'         => (int) $this->custom_max ?: null,
            'col_span'    => $type === 'range' ? 2 : 1,
            'options'     => $options,
            'icon'        => $this->icon,
            'order'       => $this->custom_order !== null ? (int) $this->custom_order : null,
        ];
    }

    private function normalizeType(?string $raw): string
    {
        $raw = strtolower(trim((string) $raw));
        return match ($raw) {
            'range', 'number-range', 'int-range' => 'range',
            'number', 'int', 'integer', 'float'  => 'number',
            'select', 'dropdown', 'enum'         => 'enum',
            'checkbox', 'switch', 'bool', 'boolean' => 'bool',
            'radio'                              => 'enum',
            default                              => 'text',
        };
    }

    private function widgetFor(string $type): string
    {
        return match ($type) {
            'range' => 'range',
            'enum'  => 'select',
            'bool'  => 'switch',
            'number'=> 'number',
            default => 'text',
        };
    }

    /**
     * `custom_options` may be a JSON array, newline-separated list,
     * or comma list of option strings. Normalise to [{value,label}].
     */
    private function parseOptions(): array
    {
        $raw = $this->custom_options;
        if (empty($raw)) return [];

        // JSON?
        if (is_string($raw) && (str_starts_with(trim($raw), '[') || str_starts_with(trim($raw), '{'))) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) return array_values(array_map(function ($o) {
                if (is_array($o)) {
                    $val = $o['value'] ?? $o['id'] ?? $o['label'] ?? '';
                    return ['value' => (string) $val, 'label' => (string) ($o['label'] ?? $val)];
                }
                return ['value' => (string) $o, 'label' => (string) $o];
            }, $decoded));
        }

        // Newlines or commas
        $parts = preg_split('/[\r\n,;]+/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_map(fn ($p) => ['value' => trim($p), 'label' => trim($p)], $parts));
    }
}
