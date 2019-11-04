<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
  public function maintenanceItems(){
      return $this->belongsTo(MaintenanceItem::class);
  }
  public function featuredImage()
  {
      return $this->belongsTo(Image::class);
  }
}
