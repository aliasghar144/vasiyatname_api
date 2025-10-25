<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_setting';

    protected $fillable = ['app_version', 'force_version'];

    public $timestamps = false;

    protected $casts = [
        'app_version' => 'integer',
        'force_version' => 'integer',
    ];

}
