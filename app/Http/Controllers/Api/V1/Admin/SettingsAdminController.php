<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;

/**
 * Site-wide settings CRUD. Settings live in the legacy `option` table as
 * key/value pairs; we expose them as a flat map for the admin UI.
 */
class SettingsAdminController extends Controller
{
    public function index()
    {
        $rows = Option::pluck('option_value', 'option_name');
        return $this->ok(['settings' => $rows]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'   => ['required', 'array'],
            'settings.*' => ['nullable'],
        ]);
        foreach ($data['settings'] as $k => $v) {
            Option::updateOrCreate(
                ['option_name'  => (string) $k],
                ['option_value' => is_scalar($v) ? (string) $v : json_encode($v)],
            );
        }
        return $this->ok(['settings' => Option::pluck('option_value', 'option_name')]);
    }
}
