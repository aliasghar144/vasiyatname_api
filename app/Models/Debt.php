<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $table = 'debts';

    protected $fillable = [
        'user_id',
        'subject',
        'person',
        'person_phone',
        'debt_type',
        'due_date',
        'bank_name',
        'amount',
        'description',
    ];

    public $timestamps = true;


    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
