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
        $updaterName = $this->record->editor?->name ?? 'Unknown User';

        $updateTime = $this->record->updated_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

        return new Envelope(
            subject: 'Document ' .  ($this->record->mail_type === "incoming" ? 'Receipt' : 'Sent') . ' - RBR - ' . $updaterName . ' - ' . $updateTime,
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
        $attachments = [];

        // 1. Cek apakah ada key 'attachments' (Array) - Format Baru
        if (!empty($this->newStatus['attachments']) && is_array($this->newStatus['attachments'])) {
            
            foreach ($this->newStatus['attachments'] as $filePath) {
                if (Storage::disk('local')->exists($filePath)) {
                    $filename = basename($filePath);
                    $attachments[] = Attachment::fromStorageDisk('local', $filePath)
                        ->as('Lampiran-' . $filename);
                }
            }

        } 
        // 2. Fallback ke key 'photo' (String) - Jaga-jaga data lama
        elseif (!empty($this->newStatus['photo']) && is_string($this->newStatus['photo'])) {
            
            $filePath = $this->newStatus['photo'];
            if (Storage::disk('local')->exists($filePath)) {
                $filename = basename($filePath);
                $attachments[] = Attachment::fromStorageDisk('local', $filePath)
                    ->as('Lampiran-' . $filename);
            }
        }

        return $attachments;
    }
}
