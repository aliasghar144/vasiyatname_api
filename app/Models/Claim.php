<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $table = 'claims';

    protected $fillable = [
        'user_id',
        'from',
        'relation',
        'due_date',
        'subject',
        'amount',
        'check_number',
        'status',
        'description',
    ];

    // ارتباط با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
