<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Generic branded HTML transactional email.
 *
 * Renders a Blade view inside the shared `emails.layouts.email` shell and
 * falls back to a plain-text sibling (`<view>.text.blade.php`) when the
 * client cannot render HTML. Subject and per-view payload are injected by
 * the MailService call sites.
 */
class Transactional extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $viewName,
        public array $data = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(
            view: $this->viewName,
            text: $this->viewName.'-text',
            with: $this->data,
        );
    }
}
