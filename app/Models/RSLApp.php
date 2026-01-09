<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Contact;
use App\Models\MailStatus;
use Illuminate\Support\Facades\Auth;

class RSLApp extends Model
{
    protected $table = 'RSLApp';

    // Kalau di tabel sana GAK ADA kolom created_at & updated_at, set ini jadi false
    public $timestamps = true; 

    protected $primaryKey = 'mail_id';

    // Daftar kolom yang boleh diisi (sesuaikan dengan kolom di DB)
    protected $fillable = [
        'mail_number',
        'mail_type',
        'date',
        'subject1',
        'subject2',
        'sender_id',
        'sender',
        'recipient_id',
        'recipient',
        'sender_date',
        'photo',
        'kurir',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        // Event saat data LAMA akan diupdate (Updating)
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function (RSLApp $rslApp) {
            
            // 1. Loop semua status anak (mailStatuses)
            foreach ($rslApp->mailStatuses as $status) {
                // Hapus File Foto Status jika ada
                if ($status->photo) {
                    Storage::disk('local')->delete($status->photo);
                }
                
                // Hapus Row Status (Optional jika DB tidak cascade)
                $status->delete(); 
            }

            // 2. Hapus Foto Utama (RSLApp)
            if ($rslApp->photo) {
                Storage::disk('local')->delete($rslApp->photo);
            }
        });
    }

    public function senderContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'sender_id', 'contact_id');
    }

    public function recipientContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'recipient_id', 'contact_id');
    }

    public function mailStatuses(): HasMany
    {
        return $this->hasMany(MailStatus::class, 'mail_id', 'mail_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke User pengedit (Ini yang kita butuhkan)
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
