<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeasibilityStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $feasibility;
    public $status;
    public $previousStatus;
    public $actionBy;
    public $emailType;
    
    /**
     * Create a new message instance.
     */
    public function __construct($feasibility, $status, $previousStatus = null, $actionBy = null, $emailType = 'status_update')
    {
        $this->feasibility = $feasibility;
        $this->status = $status;
        $this->previousStatus = $previousStatus;
        $this->actionBy = $actionBy;
        $this->emailType = $emailType;
    }

    /**
     * Get the message building.
     */
    public function build()
    {
        $subject = $this->getEmailSubject();
        
        return $this->subject($subject)
                    ->view('emails.feasibility.status')
                    ->with([
                        'feasibility' => $this->feasibility,
                        'status' => $this->status,
                        'previousStatus' => $this->previousStatus,
                        'actionBy' => $this->actionBy,
                        'emailType' => $this->emailType
                    ]);
    }
    
    /**
     * Get email subject based on status change
     */
    private function getEmailSubject()
    {
        switch($this->status) {
            case 'InProgress':
                return 'Feasibility Status Updated - Now In Progress';
            case 'Closed':
                return 'Feasibility Request Completed - ' . $this->feasibility->feasibility_request_id;
            default:
                return 'Feasibility Status Updated - ' . $this->feasibility->feasibility_request_id;
        }
    }
}
