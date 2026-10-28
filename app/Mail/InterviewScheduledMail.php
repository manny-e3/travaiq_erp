<?php

namespace App\Mail;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewScheduledMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $interview;

    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Interview Scheduled: ' . $this->interview->application->jobPosting->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hr.recruitment.interview-scheduled',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->interview->interview_stage === 'Assessment' && $this->interview->assessment_file) {
            $filePath = public_path('assessments/' . $this->interview->assessment_file);
            if (file_exists($filePath)) {
                $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath)
                    ->as('Assessment_' . $this->interview->application->first_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
            }
        }

        return $attachments;
    }
}
