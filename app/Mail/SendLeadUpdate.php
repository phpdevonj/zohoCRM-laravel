<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendLeadUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $leadId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $leadId)
    {
        $this->leadId = $leadId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Leader Assigned')
            ->markdown('emails.leads_update');
    }
}
