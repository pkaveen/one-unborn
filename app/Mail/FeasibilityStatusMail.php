<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeasibilityStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feasibility;
    public $status;
    public $previousStatus;
    public $actionBy;
    public $emailType; // created OR status_update

    /**
     * Create a new message instance.
     */
    public function __construct($feasibility, $status, $previousStatus = null, $actionBy = null, $emailType = 'status_update')
    {
        $this->feasibility     = $feasibility;
        $this->status          = $status;
        $this->previousStatus  = $previousStatus;
        $this->actionBy        = $actionBy;
        $this->emailType       = $emailType;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->getEmailSubject())
                    ->view('emails.feasibility.status')
                    ->with([
                        'feasibility'     => $this->feasibility,
                        'status'          => $this->status,
                        'previousStatus'  => $this->previousStatus,
                        'actionBy'        => $this->actionBy,
                        'emailType'       => $this->emailType
                    ]);
    }

    /**
     * Determine subject line dynamically.
     */
    private function getEmailSubject()
    {
        // Case 1: Feasibility Newly Created by Sales → Goes to Team
        if ($this->emailType === 'created') {
            return 'New Feasibility Created - Action Required';
        }

        // Case 2: Operations Updated Status → Goes to Creator
        switch ($this->status) {
            case 'Open':
                return 'Feasibility Updated - Now Open';

            case 'InProgress':
            case 'In Progress':
                return 'Feasibility Updated - Now In Progress';

            case 'Closed':
                return 'Feasibility Completed - ' . ($this->feasibility->feasibility_request_id ?? '');

            default:
                return 'Feasibility Status Updated';
        }
    }
}
