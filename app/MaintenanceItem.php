<?php

namespace Custodia;

use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function interval()
    {
        return $this->belongsTo(Interval::class);
    }

    public function featuredImage()
    {
        return $this->belongsTo(Image::class);
    }

    public function monthlyEvents(){
        return $this->belongsToMany(MonthlyEvent::class);
    }
}
