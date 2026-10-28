<?php

namespace App\Mail;

use App\Models\RideBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerBookingConfirmation extends Mailable
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
            subject: 'Your Ride is Confirmed — ' . $this->booking->reference . ' | Denver Limo Cars',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_customer_confirmation',
            with: [
                'booking' => $this->booking,
            ],
        );
    }
}
