<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderSummaryEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->title = $mailData['title']??'Daily Summary Report';
        $this->production_order = $mailData['production_order']??null;
        $this->orders_on_hold = $mailData['orders_on_hold']??null;
        $this->order_with_issues = $mailData['order_with_issues']??null;
        $this->rush_orders = $mailData['rush_orders']??null;
    }

    public function build()
    {
        return $this->subject('Order Summary')
            ->view('admin.email.summary_email',
                ['title' => $this->title,'production_order' => $this->production_order,'orders_on_hold' => $this->orders_on_hold,'order_with_issues' => $this->order_with_issues,'rush_orders' => $this->rush_orders]);
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
