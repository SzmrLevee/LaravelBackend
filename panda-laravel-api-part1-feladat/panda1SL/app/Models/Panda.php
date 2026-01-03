<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panda extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'sex',
        'birth',
    ];

    /**
     * Az attribútumok típuskonverziója.
     * Ez biztosítja, hogy a 'birth' mező mindig Carbon dátum objektum legyen.
     */
    protected $casts = [
        'birth' => 'date',
    ];
}
