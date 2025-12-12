<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\RSLApp;

class Contact extends Model
{
    protected $table = 'contact';

    public $timestamps = false; 

    protected $primaryKey = 'contact_id';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'upper_contact_id',
        'type',
    ];

    public function sentMails()
    {
        return $this->hasMany(RSLApp::class, 'sender_id', 'contact_id');
    }

    public function receivedMails()
    {
        return $this->hasMany(RSLApp::class, 'recipient_id', 'contact_id');
    }

}
