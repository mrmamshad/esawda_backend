<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\ThemeRenderer;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function __construct(private ThemeRenderer $theme) {}

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'message' => 'required|string|max:4000',
            ]);
            AuditLog::create([
                'log_date' => time(),
                'log_summary' => 'Feedback from '.$data['name'],
                'log_details' => json_encode($data),
            ]);
            session()->flash('flash_success', 'Thanks for the feedback!');

            return back();
        }

        return $this->theme->render('feedback');
    }
}
