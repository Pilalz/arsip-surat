<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailMaster extends Model
{
    protected $table = 'mailMaster';

    public $timestamps = false; 

    protected $primaryKey = 'rowid';

    protected $fillable = [
        'owner',
        'item_name',
        'seq',
    ];
}
