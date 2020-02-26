<?php

namespace Custodia\Services;

use Carbon\Carbon;
use Custodia\MaintenanceItem;
use Custodia\Section;
use Custodia\User;
use Custodia\UserProfile;
use Custodia\WeatherTriggerType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * Get the location data for all users (grouped by [city, state])
     *
     * @return UserProfile[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getUserLocations()
    {
        $locations = UserProfile::all(['city','state','longitude','latitude'])
            ->unique('city', 'state')
            ->where('city', '!=', null)
            ->where('state', '!=', null)
            ->where('longitude', '!=', null)
            ->where('latitude', '!=', null);

        return $locations;
    }

    /**
     * Get all users that have location data
     *
     * @return User[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getUsersWithLocations()
    {
        $users = User::whereHas('userProfile', function ($q) {
            $q->where('city', '!=', null)
              ->where('state', '!=', null)
              ->where('longitude', '!=', null)
              ->where('latitude', '!=', null);
        })->get();

        return $users;
    }

    public function getTriggeredMaintenanceItemsTodayByUser(User $user, WeatherTriggerService $weatherTriggerService) {
        $query = $this->getUserItemsJoinQuery($user, true);
        $results = DB::select($query);
        return $this->intervalAlgorithm($results, $user, $weatherTriggerService);
    }

    public function getTop3MaintenanceItemsTodayByUser(User $user, WeatherTriggerService $weatherTriggerService)
    {
        $query = $this->getUserItemsJoinQuery($user) . "
            ORDER BY ITEMS.points DESC
        ";

        $results = DB::select($query);

        return $this->intervalAlgorithm($results, $user, $weatherTriggerService, 3);
    }

    public function getTop3MaintenanceItemsTodayByUserAndSection(User $user, Section $section, WeatherTriggerService $weatherTriggerService)
    {
        $query = $this->getUserItemsJoinQuery($user) . "
            and ITEMS.section_id = {$section->id}
            ORDER BY ITEMS.points DESC
        ";

        $results = DB::select($query);

        return $this->intervalAlgorithm($results, $user, $weatherTriggerService, 3);
    }

    public function getAllMaintenanceItemsTodayByUserAndSection(User $user, Section $section, WeatherTriggerService $weatherTriggerService)
    {
        $query = $this->getUserItemsJoinQuery($user) . "
            and ITEMS.section_id = {$section->id}
            ORDER BY ITEMS.points DESC;
        ";

        $results = DB::select($query);

        return $this->intervalAlgorithm($results, $user, $weatherTriggerService);
    }

    public function getMaintenanceItemScoreFactors(User $user, int $maintenance_item_id) {
        $query = $this->getUserItemScoreFactorsJoinQuery($user, $maintenance_item_id);
        $results = DB::select($query);
        return array_shift($results);
    }

    private function getUserItemsJoinQuery(User $user, $only_triggers = false)
    {
        $month = date('F');

        if ($user->userProfile->home_type_id == 8) {
            $query = "
              select distinct(ITEMS.id) from maintenance_items ITEMS
              join months on ITEMS.id = months.maintenance_item_id
              join intervals on months.interval_id = intervals.id

              join user_profiles PROFILE on PROFILE.id = {$user->userProfile->id}

              join outdoor_space_type_user_profile USER_OUTDOOR_SPACE on PROFILE.id = USER_OUTDOOR_SPACE.user_profile_id
              join maintenance_item_outdoor_space_type ITEM_OUTDOOR_SPACE on ITEMS.id = ITEM_OUTDOOR_SPACE.maintenance_item_id and ITEM_OUTDOOR_SPACE.outdoor_space_type_id = USER_OUTDOOR_SPACE.outdoor_space_type_id
              join driveway_type_user_profile USER_DRIVEWAY_TYPE on PROFILE.id = USER_DRIVEWAY_TYPE.user_profile_id
              join driveway_type_maintenance_item ITEM_DRIVEWAY_TYPE on ITEMS.id = ITEM_DRIVEWAY_TYPE.maintenance_item_id and ITEM_DRIVEWAY_TYPE.driveway_type_id = USER_DRIVEWAY_TYPE.driveway_type_id
              join mobility_issue_type_user_profile USER_MOBILITY_ISSUE_TYPE on PROFILE.id = USER_MOBILITY_ISSUE_TYPE.user_profile_id
              join maintenance_item_mobility_issue_type ITEM_MOBILITY_ISSUE_TYPE on ITEMS.id = ITEM_MOBILITY_ISSUE_TYPE.maintenance_item_id and ITEM_MOBILITY_ISSUE_TYPE.mobility_issue_type_id = USER_MOBILITY_ISSUE_TYPE.mobility_issue_type_id
              join home_feature_user_profile USER_HOME_FEATURE on PROFILE.id = USER_HOME_FEATURE.user_profile_id
              join home_feature_maintenance_item ITEM_HOME_FEATURE on ITEMS.id = ITEM_HOME_FEATURE.maintenance_item_id and ITEM_HOME_FEATURE.home_feature_id = USER_HOME_FEATURE.home_feature_id
              left outer join maintenance_item_done_user ITEMS_DONE_USER on ITEMS_DONE_USER.maintenance_item_id = ITEMS.id and ITEMS_DONE_USER.user_id = PROFILE.user_id
              left outer join maintenance_item_ignored_user ITEMS_IGNORED_USER on ITEMS_IGNORED_USER.maintenance_item_id = ITEMS.id and ITEMS_IGNORED_USER.user_id = PROFILE.user_id
              WHERE
              ( ITEMS_IGNORED_USER.id IS NULL OR now() > ITEMS_IGNORED_USER.ignore_until )
              and months.month = \"{$month}\"
          ";
        } else {
            $query = "
              select distinct(ITEMS.id) from maintenance_items ITEMS
              join home_type_maintenance_item ITEM_HOME_TYPE on ITEMS.id = ITEM_HOME_TYPE.maintenance_item_id
              join months on ITEMS.id = months.maintenance_item_id
              join intervals on months.interval_id = intervals.id
              join user_profiles PROFILE on PROFILE.home_type_id = ITEM_HOME_TYPE.home_type_id
              join outdoor_space_type_user_profile USER_OUTDOOR_SPACE on PROFILE.id = USER_OUTDOOR_SPACE.user_profile_id
              join maintenance_item_outdoor_space_type ITEM_OUTDOOR_SPACE on ITEMS.id = ITEM_OUTDOOR_SPACE.maintenance_item_id and ITEM_OUTDOOR_SPACE.outdoor_space_type_id = USER_OUTDOOR_SPACE.outdoor_space_type_id
              join driveway_type_user_profile USER_DRIVEWAY_TYPE on PROFILE.id = USER_DRIVEWAY_TYPE.user_profile_id
              join driveway_type_maintenance_item ITEM_DRIVEWAY_TYPE on ITEMS.id = ITEM_DRIVEWAY_TYPE.maintenance_item_id and ITEM_DRIVEWAY_TYPE.driveway_type_id = USER_DRIVEWAY_TYPE.driveway_type_id
              join mobility_issue_type_user_profile USER_MOBILITY_ISSUE_TYPE on PROFILE.id = USER_MOBILITY_ISSUE_TYPE.user_profile_id
              join maintenance_item_mobility_issue_type ITEM_MOBILITY_ISSUE_TYPE on ITEMS.id = ITEM_MOBILITY_ISSUE_TYPE.maintenance_item_id and ITEM_MOBILITY_ISSUE_TYPE.mobility_issue_type_id = USER_MOBILITY_ISSUE_TYPE.mobility_issue_type_id
              join home_feature_user_profile USER_HOME_FEATURE on PROFILE.id = USER_HOME_FEATURE.user_profile_id
              join home_feature_maintenance_item ITEM_HOME_FEATURE on ITEMS.id = ITEM_HOME_FEATURE.maintenance_item_id and ITEM_HOME_FEATURE.home_feature_id = USER_HOME_FEATURE.home_feature_id
              left outer join maintenance_item_done_user ITEMS_DONE_USER on ITEMS_DONE_USER.maintenance_item_id = ITEMS.id and ITEMS_DONE_USER.user_id = PROFILE.user_id
              left outer join maintenance_item_ignored_user ITEMS_IGNORED_USER on ITEMS_IGNORED_USER.maintenance_item_id = ITEMS.id and ITEMS_IGNORED_USER.user_id = PROFILE.user_id
              where PROFILE.id = {$user->userProfile->id}
              and ( ITEMS_IGNORED_USER.id IS NULL OR now() > ITEMS_IGNORED_USER.ignore_until )
              and months.month = \"{$month}\"
          ";
        }

        if ($only_triggers) {
            $query .= " AND intervals.name = 'Weather Trigger' ";
        }

        return $query;
    }

    private function getUserItemScoreFactorsJoinQuery(User $user, int $maintenance_item_id)
    {
        if ($user->userProfile->home_type_id == 8) {
            $query = "
              select distinct(ITEMS.id),
                     1 as home_type_score_factor as home_type_score_factor,
                     ITEM_OUTDOOR_SPACE.score_factor as outdoor_space_score_factor,
                     ITEM_DRIVEWAY_TYPE.score_factor as driveway_score_factor,
                     ITEM_HOME_FEATURE.score_factor as feature_score_factor,
                     ITEM_MOBILITY_ISSUE_TYPE.score_factor as mobility_issue_score_factor
              from maintenance_items ITEMS
              
              join months on ITEMS.id = months.maintenance_item_id
              join intervals on months.interval_id = intervals.id

              join user_profiles PROFILE on PROFILE.id = {$user->userProfile->id}

              join outdoor_space_type_user_profile USER_OUTDOOR_SPACE on PROFILE.id = USER_OUTDOOR_SPACE.user_profile_id
              join maintenance_item_outdoor_space_type ITEM_OUTDOOR_SPACE on ITEMS.id = ITEM_OUTDOOR_SPACE.maintenance_item_id and ITEM_OUTDOOR_SPACE.outdoor_space_type_id = USER_OUTDOOR_SPACE.outdoor_space_type_id
              join driveway_type_user_profile USER_DRIVEWAY_TYPE on PROFILE.id = USER_DRIVEWAY_TYPE.user_profile_id
              join driveway_type_maintenance_item ITEM_DRIVEWAY_TYPE on ITEMS.id = ITEM_DRIVEWAY_TYPE.maintenance_item_id and ITEM_DRIVEWAY_TYPE.driveway_type_id = USER_DRIVEWAY_TYPE.driveway_type_id
              join mobility_issue_type_user_profile USER_MOBILITY_ISSUE_TYPE on PROFILE.id = USER_MOBILITY_ISSUE_TYPE.user_profile_id
              join maintenance_item_mobility_issue_type ITEM_MOBILITY_ISSUE_TYPE on ITEMS.id = ITEM_MOBILITY_ISSUE_TYPE.maintenance_item_id and ITEM_MOBILITY_ISSUE_TYPE.mobility_issue_type_id = USER_MOBILITY_ISSUE_TYPE.mobility_issue_type_id
              join home_feature_user_profile USER_HOME_FEATURE on PROFILE.id = USER_HOME_FEATURE.user_profile_id
              join home_feature_maintenance_item ITEM_HOME_FEATURE on ITEMS.id = ITEM_HOME_FEATURE.maintenance_item_id and ITEM_HOME_FEATURE.home_feature_id = USER_HOME_FEATURE.home_feature_id
              left outer join maintenance_item_done_user ITEMS_DONE_USER on ITEMS_DONE_USER.maintenance_item_id = ITEMS.id and ITEMS_DONE_USER.user_id = PROFILE.user_id
              left outer join maintenance_item_ignored_user ITEMS_IGNORED_USER on ITEMS_IGNORED_USER.maintenance_item_id = ITEMS.id and ITEMS_IGNORED_USER.user_id = PROFILE.user_id
              WHERE
              ITEMS.id = {$maintenance_item_id}
              UNION SELECT {$maintenance_item_id},1,1,1,1,1
          ";
        } else {
            $query = "
              select distinct(ITEMS.id),
                     ITEM_HOME_TYPE.score_factor as home_type_score_factor,
                     ITEM_OUTDOOR_SPACE.score_factor as outdoor_space_score_factor,
                     ITEM_DRIVEWAY_TYPE.score_factor as driveway_score_factor,
                     ITEM_HOME_FEATURE.score_factor as feature_score_factor,
                     ITEM_MOBILITY_ISSUE_TYPE.score_factor as mobility_issue_score_factor
              from maintenance_items ITEMS
              
              join months on ITEMS.id = months.maintenance_item_id
              join intervals on months.interval_id = intervals.id
              
              join user_profiles PROFILE on PROFILE.id = {$user->userProfile->id}
              
              join home_type_maintenance_item ITEM_HOME_TYPE on ITEMS.id = ITEM_HOME_TYPE.maintenance_item_id
              join outdoor_space_type_user_profile USER_OUTDOOR_SPACE on PROFILE.id = USER_OUTDOOR_SPACE.user_profile_id
              join maintenance_item_outdoor_space_type ITEM_OUTDOOR_SPACE on ITEMS.id = ITEM_OUTDOOR_SPACE.maintenance_item_id and ITEM_OUTDOOR_SPACE.outdoor_space_type_id = USER_OUTDOOR_SPACE.outdoor_space_type_id
              join driveway_type_user_profile USER_DRIVEWAY_TYPE on PROFILE.id = USER_DRIVEWAY_TYPE.user_profile_id
              join driveway_type_maintenance_item ITEM_DRIVEWAY_TYPE on ITEMS.id = ITEM_DRIVEWAY_TYPE.maintenance_item_id and ITEM_DRIVEWAY_TYPE.driveway_type_id = USER_DRIVEWAY_TYPE.driveway_type_id
              join mobility_issue_type_user_profile USER_MOBILITY_ISSUE_TYPE on PROFILE.id = USER_MOBILITY_ISSUE_TYPE.user_profile_id
              join maintenance_item_mobility_issue_type ITEM_MOBILITY_ISSUE_TYPE on ITEMS.id = ITEM_MOBILITY_ISSUE_TYPE.maintenance_item_id and ITEM_MOBILITY_ISSUE_TYPE.mobility_issue_type_id = USER_MOBILITY_ISSUE_TYPE.mobility_issue_type_id
              join home_feature_user_profile USER_HOME_FEATURE on PROFILE.id = USER_HOME_FEATURE.user_profile_id
              join home_feature_maintenance_item ITEM_HOME_FEATURE on ITEMS.id = ITEM_HOME_FEATURE.maintenance_item_id and ITEM_HOME_FEATURE.home_feature_id = USER_HOME_FEATURE.home_feature_id
              left outer join maintenance_item_done_user ITEMS_DONE_USER on ITEMS_DONE_USER.maintenance_item_id = ITEMS.id and ITEMS_DONE_USER.user_id = PROFILE.user_id
              left outer join maintenance_item_ignored_user ITEMS_IGNORED_USER on ITEMS_IGNORED_USER.maintenance_item_id = ITEMS.id and ITEMS_IGNORED_USER.user_id = PROFILE.user_id
              where 
              ITEMS.id = {$maintenance_item_id}
              UNION SELECT {$maintenance_item_id},1,1,1,1,1
          ";
        }

        return $query;
    }

    public function intervalAlgorithm($results, User $user, WeatherTriggerService $weatherTriggerService, $limit = 0)
    {
        $ret = collect();
        $day = date('d');
        $week = intval ($day / 7);
        $biWeek = intval ($day / 14);

        foreach ($results as $result) {
            $done = DB::table('maintenance_item_done_user')->where('maintenance_item_id', $result->id)->where('user_id', $user->id)->whereMonth('created_at', date('m'))->orderBy('created_at', 'desc')->first();
            $now = Carbon::now();
            $difference = -1;
            if(!is_null($done)){
                $difference = $now->diff($done->created_at)->days;
            }
            $str = date('F');

            $m = MaintenanceItem::with(["months" => function ($query) use ($str){
                $query->where('month', $str);
            }])->find($result->id);

            $m_ar = MaintenanceItem::with(["months" => function ($query) use ($str){
                $query->where('month', $str);
            }])->find($result->id)->toArray();

            $scoreFactors = $this->getMaintenanceItemScoreFactors($user, (int)$result->id);

            foreach ($m['months'] as $k => $month) {
                if ($month['month'] == $str) {
                    if($month->interval->name == 'Weekly') {
                        if($week > 3){
                            $m_ar['months']['description'] = $month->monthsDescription[3]->description;
                            $m_ar['months']['image'] = $month->monthsDescription[3]->img_name;
                        } else{
                            $m_ar['months']['description'] = $month->monthsDescription[$week]->description;
                            $m_ar['months']['image'] = $month->monthsDescription[$week]->img_name;
                        }
                    }elseif($month->interval->name == 'Biweekly') {
                        if($week > 1){
                            $m_ar['months']['description'] = $month->monthsDescription[1]->description;
                            $m_ar['months']['image'] = $month->monthsDescription[1]->img_name;
                        } else {
                            $m_ar['months']['description'] = $month->monthsDescription[$biWeek]->description;
                            $m_ar['months']['image'] = $month->monthsDescription[$biWeek]->img_name;
                        }
                    } elseif($month->interval->name == 'Monthly') {
                        $m_ar['months']['description'] = $month->monthsDescription[0]->description;
                        $m_ar['months']['image'] = $month->monthsDescription[0]->img_name;
                    } elseif($month->interval->name == 'Weather Trigger') {
                        $m_ar['months']['description'] = $month->monthsDescription[0]->description;
                        $m_ar['months']['image'] = $month->monthsDescription[0]->img_name;
                    }
                    $m_ar['months']['interval'] = $month->interval->name;

                    // user_item_score_factor = item_importance_score_factor x f1 x f2 x ...
                    $m_ar['points'] = (int)$m_ar['points']
                                    * $scoreFactors->home_type_score_factor
                                    * $scoreFactors->outdoor_space_score_factor
                                    * $scoreFactors->driveway_score_factor
                                    * $scoreFactors->feature_score_factor
                                    * $scoreFactors->mobility_issue_score_factor;
                }
            }
            if($m_ar['months']['interval'] == 'Monthly'){
                if(is_null($done)){
                    $ret->push($m_ar);
                }
            }elseif($m_ar['months']['interval'] == 'Biweekly'){
                if(is_null($done) || ($difference != -1 && $difference > 14)){
                    $ret->push($m_ar);
                }
            }elseif($m_ar['months']['interval'] == 'Weekly'){
                if(is_null($done) || ($difference != -1 && $difference > 7)){
                    $ret->push($m_ar);
                }
            }elseif($m_ar['months']['interval'] == 'Weather Trigger') {
                if (empty($m_ar['weather_trigger_type_id'])) {
                    Log::error("weather trigger type not set", $m_ar);
                    continue;
                }

                $wtt = WeatherTriggerType::find((int)$m_ar['weather_trigger_type_id']);

                if (empty($wtt)) {
                    Log::error("unable to resolve weather trigger type", $m_ar);
                    continue;
                }

                if ($weatherTriggerService->checkWeatherTrigger($wtt, $user))
                    $ret->push($m_ar);
            }else{
                $ret->push($m_ar);
            }

            if ($limit && count($ret) == $limit)
                break;
        }

        return $ret;
    }
}