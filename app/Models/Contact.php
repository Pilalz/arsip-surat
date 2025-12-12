<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';

    public $timestamps = false; 

    protected $primaryKey = 'contact_id';

    // Daftar kolom yang boleh diisi (sesuaikan dengan kolom di DB)
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'upper_contact_id',
        'type',
    ];
}
