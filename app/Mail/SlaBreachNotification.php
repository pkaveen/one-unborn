<?php

namespace App\Mail;

use App\Models\SlaReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SlaBreachNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $report;
    public $link;
    public $client;

    /**
     * Create a new message instance.
     */
    public function __construct(SlaReport $report)
    {
        $this->report = $report;
        $this->link = $report->clientLink;
        $this->client = $report->clientLink->client;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('⚠️ SLA Breach Alert - ' . $this->link->deliverable->deliverable_id)
                    ->view('emails.sla_breach')
                    ->with([
                        'report' => $this->report,
                        'link' => $this->link,
                        'client' => $this->client,
                    ]);
    }
}
