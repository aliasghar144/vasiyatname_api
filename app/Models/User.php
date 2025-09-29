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
        'first_name',
        'last_name',
        'birth_date',
        'national_code',
        'is_married',
        'children_count',
        'wife_count',
        'province',
        'city',
        'address'
    ];

    public $timestamps = true;

    protected $casts = [
        'mobile' => 'string',
        'first_name' => 'string',
        'last_name' => 'string',
        'province' => 'string',
        'address' => 'string',
        'city' => 'string',
        'national_code' => 'string',
        'children_count' => 'integer',
        'is_married' => 'bool',
        'wife_count' => 'integer',
        'birth_date' => 'date',
    ];
}
