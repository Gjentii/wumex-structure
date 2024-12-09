<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class SinglePassword extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    protected $fillable = ['password_type','title','username','password','hint','notes','website','image'];

    public function securityQuestions(): hasMany
    {
        return $this->hasMany(SecurityQuestion::class);
    }

    public function contacts(): MorphToMany
    {
        return $this->morphToMany(Contact::class, 'contactable');
    }

    public function reminders(): MorphToMany
    {
        return $this->morphToMany(Reminder::class,'remindable');
    }

    protected static function booted()
    {
        static::saving(function (SinglePassword $password) {
            if ($password->password_type === 'Passcode') {
                $password->username = null;
                $password->website = null;
            }
        });
    }

}
