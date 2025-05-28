<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class OrderSummaryEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;
    public $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData,$filePath)
    {
        $this->title = $mailData['title']??'Daily Summary Report';
        $this->mailData = $mailData;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Daily Summary Report - ' . $this->mailData['date'])
            ->view('admin.email.summary_email',['data'=>$this->mailData['summary']??[],'title' => $mailData['title']??'Daily Summary Report','date' => Carbon::now()->format('Y-m-d')])
            ->attach($this->filePath, [
                'as' => 'daily_summary_' . $this->mailData['date'] . '.xlsx',
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin.email.summary_email',
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
