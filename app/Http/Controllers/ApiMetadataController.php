<?php

namespace Custodia\Http\Controllers;

use Custodia\DrivewayType;
use Custodia\HomeFeature;
use Custodia\HomeType;
use Custodia\Interval;
use Custodia\MobilityIssueType;
use Custodia\MonthlyEvent;
use Custodia\ManagementPlan;
use Custodia\OutdoorSpaceType;
use Custodia\Role;
use Custodia\Section;
use Custodia\WeatherTriggerType;
use Illuminate\Http\Request;

class ApiMetadataController extends Controller
{
    public function apiGetAllHomeTypes(){
        return HomeType::all();
    }

    public function apiGetAllManagementPlans(){
        return ManagementPlan::all();
    }

    public function apiGetAllOutdoorSpaces(){
        return OutdoorSpaceType::all();
    }

    public function apiGetAllHomeFeatures(){
        return HomeFeature::all();
    }

    public function apiGetAllDrivewayTypes(){
        return DrivewayType::all();
    }

    public function apiGetAllMobilityIssues(){
        return MobilityIssueType::all();
    }

    public function apiGetAllRoles(){
        return Role::all();
    }

    public function apiGetAllIntervals(){
        return Interval::all();
    }

    public function apiGetAllNewsfeedSections(){
        return Section::all();
    }

    public function apiGetAllWeatherTriggers(){
        return WeatherTriggerType::all();
    }

    public function apiGetAllMonthlyEvents(){
        return MonthlyEvent::all();
    }
}
