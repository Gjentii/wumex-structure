<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityQuestion extends Model
{
    protected $fillable = [
        'single_password_id',
        'question',
        'answer',
        'hint',
    ];

    public function singlePasswords(): BelongsTo
    {
        return $this->belongsTo(SinglePassword::class);
    }
}
