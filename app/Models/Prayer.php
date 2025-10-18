<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prayer extends Model
{

    protected $table = 'prayers';

    protected $fillable = [
        'user_id',
        'fajr_prayer',
        'dhuhr_prayer',
        'asr_prayer',
        'maghrib_prayer',
        'isha_prayer',
        'fajr_prayer_rec',
        'dhuhr_prayer_rec',
        'asr_prayer_rec',
        'maghrib_prayer_rec',
        'isha_prayer_rec',
        'ayat_rec',
        'ayat',
    ];

    public $timestamps = true;

    protected $casts = [
        'user_id' => 'integer',
        'fajr_prayer' => 'integer',
        'dhuhr_prayer' => 'integer',
        'asr_prayer' => 'integer',
        'maghrib_prayer' => 'integer',
        'isha_prayer' => 'integer',
        'fajr_prayer_rec' => 'integer',
        'dhuhr_prayer_rec' => 'integer',
        'asr_prayer_rec' => 'integer',
        'maghrib_prayer_rec' => 'integer',
        'isha_prayer_rec' => 'integer',
        'ayat_rec' => 'integer',
        'ayat' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
