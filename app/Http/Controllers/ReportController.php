<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

/** Legacy: php/report.php — visitors can flag an ad for review. */
class ReportController extends Controller
{
    public function __construct(private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if ($request->isMethod('post') && $request->filled('post_id')) {
            $data = $request->validate([
                'post_id' => 'required|integer',
                'email'   => 'required|email',
                'reason'  => 'required|string',
                'details' => 'required|string|max:2000',
            ]);
            AuditLog::create([
                'log_date'    => time(),
                'log_summary' => 'Report ad #' . $data['post_id'] . ' [' . $data['reason'] . ']',
                'log_details' => json_encode($data),
            ]);
            session()->flash('flash_success', 'Thank you — the report has been received.');
            return back();
        }
        return $this->theme->render('report');
    }
}
