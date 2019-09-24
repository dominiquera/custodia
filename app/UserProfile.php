<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homeType()
    {
        return $this->belongsTo(HomeType::class);
    }
}
