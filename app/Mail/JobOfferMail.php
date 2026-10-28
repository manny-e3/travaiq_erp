<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobOfferMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Offer: ' . $this->application->jobPosting->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hr.recruitment.job-offer',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        if ($this->application->offer && $this->application->offer->document_path) {
            $attachments[] = Attachment::fromPath(public_path('offer_letter/' . $this->application->offer->document_path))
                    ->as('Job_Offer_' . $this->application->first_name . '.pdf')
                    ->withMime('application/pdf');
        }
        return $attachments;
    }
}
