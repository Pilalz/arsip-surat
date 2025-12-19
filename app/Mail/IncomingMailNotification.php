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
use Illuminate\Support\Facades\Storage;

class IncomingMailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(public RSLApp $record, array $newStatus)
    {
        $this->newStatus = $newStatus;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Surat ' .  ($this->record->mail_type === "incoming" ? 'Masuk' : 'Keluar') . ' - ' . ($this->newStatus['status']) . ' - ' . $this->record->mail_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
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
        $photoPath = $this->newStatus['photo'] ?? null;

        if ($photoPath && Storage::disk('local')->exists($photoPath)) {
            
            $filename = basename($photoPath);
            return [
                Attachment::fromStorageDisk('local', $photoPath)
                    ->as('Bukti-Status-' . $filename)
                    ->withMime('image/jpeg'),
            ];
        }

        return [];
    }
}
