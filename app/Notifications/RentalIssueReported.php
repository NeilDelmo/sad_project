<?php

namespace App\Notifications;

use App\Models\RentalIssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentalIssueReported extends Notification
{
    use Queueable;

    public function __construct(public RentalIssueReport $report)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'rental_issue_reported',
            'title' => 'Rental Issue Reported',
            'message' => sprintf('Issue reported for rental #%d: %s', $this->report->rental_id, $this->report->title ?: ucfirst($this->report->issue_type)),
            'rental_id' => $this->report->rental_id,
            'report_id' => $this->report->id,
            'issue_type' => $this->report->issue_type,
            'severity' => $this->report->severity,
            'action_url' => route('rentals.admin.index'),
            'action_text' => 'Open Rentals Admin',
        ];
    }
}
