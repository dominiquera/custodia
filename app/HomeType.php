<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class HomeType extends Model
{
    public function userProfiles()
    {
        return $this->hasMany(UserProfile::class);
    }

    public function maintenanceItems(){
        return $this->belongsToMany(MaintenanceItem::class);
    }
}
