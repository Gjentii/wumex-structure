<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Reminder extends Model
{
    protected $fillable = ['name','reminder_type','status','remind_at','expires_on'];

    protected $casts = [
        'expires_on' => 'datetime',
    ];
    public function singlePasswords(): MorphToMany
    {
        return $this->morphedByMany(SinglePassword::class,'remindable');
    }
}
