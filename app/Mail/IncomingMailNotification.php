<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\RSLApp;

class IncomingMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public RSLApp $record)
    {
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Judul Email
            subject: 'Pemberitahuan Surat Masuk Baru - ' . $this->record->mail_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Nama file tampilan (View) yang akan kita buat di langkah 3
            view: 'mail.incoming-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->record->photo) {
            return [
                Attachment::fromStorageDisk('local', $this->record->photo)
                    ->as('Lampiran-Surat.jpg')
                    ->withMime('image/jpeg'),
            ];
        }

        return [];
    }
}
