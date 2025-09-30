<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $table = 'debts';

    protected $fillable = [
        'title',
        'type',
        'amount',
        'amount_paid',
        'created_date',
        'due_date',
        'status',
        'full_name',
        'national_id'
    ];

    public $timestamps = true;

}
