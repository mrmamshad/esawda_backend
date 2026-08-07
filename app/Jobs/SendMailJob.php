<?php

namespace App\Jobs;

use App\Mail\LegacyMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Sends one legacy plain-text notification. Dispatched via
 * EmailQueue::enqueue() so the sync queue driver delivers inline in dev
 * and the database driver defers in production.
 */
class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $to,
        public string $toName,
        public string $subject,
        public string $body,
    ) {}

    public function handle(): void
    {
        Mail::send(new LegacyMail($this->to, $this->toName, $this->subject, $this->body));
    }
}
