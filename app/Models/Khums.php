<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Khums extends  Model{

    protected $table = 'khums';

    protected $fillable = [
        'user_id',
        'date',
        'amount',
        'description',
        'payed',
    ];

    protected $casts = [
        'amount' => 'integer',
        'date' => 'date',
        'user_id' => 'integer',
        'payed' => 'boolean',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
