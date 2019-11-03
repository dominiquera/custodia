<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class ManagementPlan extends Model
{
  public function maintenanceItems(){
      return $this->belongsToMany(MaintenanceItem::class);
  }

  public function userProfiles(){
      return $this->belongsToMany(UserProfile::class);
  }
}
