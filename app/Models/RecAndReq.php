<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecAndReq extends Model
{
    protected $table = 'rec_and_req';

    protected $fillable = [
        'user_id',
        'req_description',
        'type_ceremony_des',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
