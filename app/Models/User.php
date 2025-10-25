<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Authenticatable; // اضافه کن
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens;

    protected $table = 'users';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'mobile',
        'email',
        'home_phone',
        'first_name',
        'last_name',
        'father_name',
        'birth_date',
        'birth_loc',
        'national_code',
        'is_married',
        'children_count',
        'wife_count',
        'province',
        'city',
        'address',
        'last_seen_at',
        'fcmToken',
        'show_notif',
        'app_version',
    ];

    public $timestamps = true;

    protected $casts = [
        'mobile' => 'string',
        'app_version' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'province' => 'string',
        'address' => 'string',
        'city' => 'string',
        'national_code' => 'string',
        'children_count' => 'integer',
        'is_married' => 'bool',
        'show_notif' => 'bool',
        'wife_count' => 'integer',
        'birth_date' => 'date',
    ];
}
