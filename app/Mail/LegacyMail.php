<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Plain-text mail for the legacy emailq-style notifications (password
 * reset, contact form). Body is pre-built text; no templating.
 */
class LegacyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailBody;
    public string $emailSubject;

    public function __construct(public string $recipient, public string $recipientName, string $subject, string $body)
    {
        $this->emailSubject = $subject;
        $this->emailBody    = $body;
    }

    public function build(): self
    {
        return $this
            ->to($this->recipient, $this->recipientName)
            ->subject($this->emailSubject)
            ->text('mail.legacy-plain', ['body' => $this->emailBody]);
    }
}
