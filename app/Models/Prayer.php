<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prayer extends  Model{

    protected $table = 'prayers';

    protected $fillable = [
        'type',
        'user_id',
        'rakats',
        'status',
        'date',
        'description',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
