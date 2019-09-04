<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeType extends Model
{
    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class);
    }
}
