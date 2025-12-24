<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoneFinancial extends Model
{
    protected $table = 'none_financial';

    protected $fillable = [
        'user_id',
        'subject',
        'type',
        'description',
        'payed'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'payed' => 'boolean',
    ];

    // ارتباط با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
