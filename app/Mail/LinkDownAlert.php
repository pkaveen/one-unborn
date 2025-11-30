<?php

namespace App\Mail;

use App\Models\ClientLink;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LinkDownAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $link;
    public $downSince;

    public function __construct(ClientLink $link, Carbon $downSince)
    {
        $this->link = $link;
        $this->downSince = $downSince;
    }

    public function build()
    {
        return $this->subject('🔴 URGENT: Link Down Alert - ' . ($this->link->deliverable->deliverable_id ?? 'Link'))
                    ->view('emails.alerts.link_down')
                    ->with([
                        'link' => $this->link,
                        'downSince' => $this->downSince,
                        'duration' => $this->downSince->diffForHumans(),
                    ]);
    }
}
