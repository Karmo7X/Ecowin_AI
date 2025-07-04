<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\DonationThankYouMail;
use Illuminate\Support\Facades\Mail;


class SendDonationThankYouEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $donation;
    public $userName;
    public $userEmail;
    /**
     * Create a new job instance.
     *
     * @param $donation
     * @param $userName
     * @param $userEmail
     */
    /**
     * Create a new job instance.
     */
    public function __construct($donation, $userName, $userEmail)
    {
        $this->donation = $donation;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
    }
    /**
     * Execute the job.
     *
     * @return void
     */

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         Mail::to($this->userEmail)->send(new DonationThankYouMail($this->donation, $this->userName));
    }
}
