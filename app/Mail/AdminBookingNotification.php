<?php

namespace App\Mail;

use App\Models\RideBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public RideBooking $booking;

    public function __construct(RideBooking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚗 NEW BOOKING ' . $this->booking->reference . ' — Denver Limo Cars',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_admin_notification',
            with: [
                'booking' => $this->booking,
            ],
        );
    }
}
