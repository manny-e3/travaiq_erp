<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $contractPath;

    public function __construct(User $user, $password, $contractPath = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->contractPath = $contractPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Administrative Access: ' . config('app.name', 'Business process automation'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user_welcome',
        );
    }

    public function attachments(): array
    {
        if ($this->contractPath) {
            $fullPath = storage_path('app/public/' . $this->contractPath);
            if (file_exists($fullPath)) {
                return [
                    Attachment::fromPath($fullPath)
                        ->as('Employment_Contract.pdf')
                        ->withMime('application/pdf'),
                ];
            }
        }

        return [];
    }
}
