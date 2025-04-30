<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public $donation;
    public $userName;
    /**
     * Create a new message instance.
     *
     * @param $donation
     * @param $userName
     */
    /**
     * Create a new message instance.
     */
    public function __construct($donation, $userName)
    {
        $this->donation = $donation;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Thanks for your donation')
                    ->view('emails.donation_thank_you')
                    ->with([
                        'donation' => $this->donation,
                        'userName' => $this->userName,
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
