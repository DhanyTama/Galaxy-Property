<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SalaryRequest extends Notification
{
    use Queueable;

    protected $salaryRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($salaryRequest)
    {
        $this->salaryRequest = $salaryRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'salary_request_id' => $this->salaryRequest->id,
            'status' => 'pending',
            'net_salary' => $this->salaryRequest->net_salary,
            'approved_by' => $this->salaryRequest->approvedBy->name ?? 'N/A',
            'rejection_reason' => $this->salaryRequest->rejection_reason,
            'message' => "Permintaan gaji ID #{$this->salaryRequest->id} sebesar Rp" . number_format($this->salaryRequest->net_salary, 0, ',', '.') . " telah diajukan.",
            'link' => route('salary-requests.show', $this->salaryRequest->id),
        ];
    }
}
