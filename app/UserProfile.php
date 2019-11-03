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

    public function managementPlans(){
        return $this->belongsToMany(ManagementPlan::class);
    }

}
