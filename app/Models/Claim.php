<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $table = 'claims';

    protected $fillable = [
        'user_id',
        'from',
        'claim_type',
        'amount',
        'description',
        'status',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    // ارتباط با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
