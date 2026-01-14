<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RSLApp;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MailStatus extends Model
{
    protected $table = 'mailStatus';

    public $timestamps = true; 

    protected $primaryKey = 'rowid';

    protected $guarded = [];

    // protected $fillable = [
    //     'mail_id',
    //     'date',
    //     'time',
    //     'status',
    //     'photo',
    //     'recipient',
    //     'attachments',
    // ];

    protected $casts = [
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Saat data BARU dibuat
        static::creating(function ($model) {
            // Isi created_by & updated_by otomatis dengan ID user yg login
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        // Saat data LAMA diedit
        static::updating(function ($model) {
            // Update updated_by saja
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function rslApp()
    {
        return $this->belongsTo(RSLApp::class, 'mail_id', 'mail_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
