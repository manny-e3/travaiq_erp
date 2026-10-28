<?php

namespace App\Mail;

use App\Models\Interview;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InterviewerAssignedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $interview;
    public $interviewer;

    public function __construct(Interview $interview, User $interviewer)
    {
        $this->interview = $interview;
        $this->interviewer = $interviewer;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Interview Assignment: ' . $this->interview->application->jobPosting->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hr.recruitment.interviewer-assigned',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $resumePath = $this->interview->application->resume_path;
        
        if ($resumePath) {
            if (str_starts_with($resumePath, 'resumes/')) {
                // Old storage path format
                $filePath = storage_path('app/public/' . $resumePath);
            } else {
                // New public_path format
                $filePath = public_path('resumes/' . $resumePath);
            }

            if (file_exists($filePath)) {
                $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromPath($filePath)
                    ->as('Candidate_CV_' . $this->interview->application->first_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
            }
        }

        return $attachments;
    }
}
