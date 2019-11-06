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

    public function weatherTriggerType()
    {
        return $this->belongsTo(WeatherTriggerType::class);
    }

    public function monthlyEvents(){
        return $this->belongsToMany(MonthlyEvent::class);
    }

    public function months(){
        return $this->hasMany(Month::class);
    }

    public function homeTypes(){
        return $this->belongsToMany(HomeType::class);
    }

    public function outdoorSpaces(){
        return $this->belongsToMany(OutdoorSpaceType::class);
    }

    public function mobilityIssues(){
        return $this->belongsToMany(MobilityIssueType::class);
    }

    public function homeFeatures(){
        return $this->belongsToMany(HomeFeature::class);
    }

    public function drivewayTypes(){
        return $this->belongsToMany(DrivewayType::class);
    }

    public function tools()
    {
      return  $this->hasMany(Tool::class, 'maintenance_items_id');
    }

}
