<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RSLApp;

class MailStatus extends Model
{
    protected $table = 'mailStatus';

    public $timestamps = false; 

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
    ];

    public function rslApp()
    {
        return $this->belongsTo(RSLApp::class, 'mail_id', 'mail_id');
    }
}
