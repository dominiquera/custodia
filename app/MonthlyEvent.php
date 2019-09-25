<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class MonthlyEvent extends Model
{
    public function maintenanceItems(){
        return $this->belongsToMany(MaintenanceItem::class);
    }
}
