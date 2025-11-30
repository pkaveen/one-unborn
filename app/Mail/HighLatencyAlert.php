<?php

namespace App\Mail;

use App\Models\ClientLink;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HighLatencyAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $avgLatency;
    public $threshold;

    public function __construct(ClientLink $link, $avgLatency, $threshold)
    {
        $this->link = $link;
        $this->avgLatency = $avgLatency;
        $this->threshold = $threshold;
    }

    public function build()
    {
        return $this->subject('⚠️ High Latency Alert - ' . ($this->link->deliverable->deliverable_id ?? 'Link'))
                    ->view('emails.alerts.high_latency')
                    ->with([
                        'link' => $this->link,
                        'avgLatency' => $this->avgLatency,
                        'threshold' => $this->threshold,
                    ]);
    }
}
