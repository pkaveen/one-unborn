<?php

namespace App\Mail;

use App\Models\ClientLink;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HighPacketLossAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $avgPacketLoss;
    public $threshold;

    public function __construct(ClientLink $link, $avgPacketLoss, $threshold)
    {
        $this->link = $link;
        $this->avgPacketLoss = $avgPacketLoss;
        $this->threshold = $threshold;
    }

    public function build()
    {
        return $this->subject('⚠️ High Packet Loss Alert - ' . ($this->link->deliverable->deliverable_id ?? 'Link'))
                    ->view('emails.alerts.high_packet_loss')
                    ->with([
                        'link' => $this->link,
                        'avgPacketLoss' => $this->avgPacketLoss,
                        'threshold' => $this->threshold,
                    ]);
    }
}
