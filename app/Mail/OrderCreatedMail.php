<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $userName;
    /**
     * Create a new message instance.
     */
    public function __construct($order, $userName)
    {
        $this->order = $order;
        $this->userName = $userName;
    }
    public function build()
    {
        return $this->subject('confirm your order from Ecowin')
                    ->view('emails.order_created')
                    ->with([
                        'order' => $this->order,
                        'userName' => $this->userName
                    ]);
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
