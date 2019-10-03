<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class HomeFeature extends Model
{
    public function maintenanceItems(){
        return $this->belongsToMany(MaintenanceItem::class);
    }

    public function userProfiles(){
        return $this->belongsToMany(UserProfile::class);
    }
}
