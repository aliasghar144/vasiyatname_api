<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Zakat extends Model
{

    protected $table = 'zakat';


    protected $fillable = [
        'user_id',
        'date',
        'type',
        'amount',
        'description',
        'payed',
    ];

    public $timestamps = true;

    protected $casts = [
        'date' => 'date',
        'user_id' => 'integer',
        'payed' => 'boolean',
    ];
}
