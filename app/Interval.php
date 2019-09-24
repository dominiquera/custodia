<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class Interval extends Model
{
    public function maintenanceItems()
    {
        return $this->hasMany(MaintenanceItem::class);
    }
}
