<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RSLApp extends Model
{
    protected $table = 'RSLApp';

    // Kalau di tabel sana GAK ADA kolom created_at & updated_at, set ini jadi false
    public $timestamps = false; 

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
    ];
}
