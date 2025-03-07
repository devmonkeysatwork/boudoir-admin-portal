<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExceptionReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function build()
    {
        return $this->subject('Exception Occurred in Boudoir Production Portal')
            ->view('admin.email.exception_report') // You can create this view for the email content
            ->with([
                'exceptionMessage' => $this->exception->getMessage(),
                'exceptionTrace' => $this->exception->getTraceAsString(),
            ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin.email.exception_report',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
