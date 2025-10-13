<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasting extends Model
{
    protected $table = 'fasting';

    protected $fillable = [
        'user_id',
        'fasting',
        'fasting_rec',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}