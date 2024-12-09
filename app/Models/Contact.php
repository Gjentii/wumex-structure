<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
class Contact extends Model
{
    protected $fillable = ['name','email'];

    public function singlePasswords(): MorphToMany
    {
        return $this->morphedByMany(SinglePassword::class, 'contactable');
    }
}
