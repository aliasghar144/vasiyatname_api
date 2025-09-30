<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prayer extends  Model{

    protected $table = 'prayers';

    protected $fillable = [
        'type',
        'rakats',
        'status',
        'date',
        'description',
    ];

    public $timestamps = true;


}
