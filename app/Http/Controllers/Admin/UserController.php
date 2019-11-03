<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\DrivewayType;
use Custodia\HomeFeature;
use Custodia\HomeType;
use Custodia\Http\Requests\User\CreateUserRequest;
use Custodia\Http\Requests\User\StoreUserRequest;
use Custodia\Interval;
use Custodia\MaintenanceItem;
use Custodia\MobilityIssueType;
use Custodia\MonthlyEvent;
use Custodia\OutdoorSpaceType;
use Custodia\Role;
use Custodia\Section;
use Custodia\User;
use Custodia\UserProfile;
use Custodia\WeatherTriggerType;
use Illuminate\Http\Request;
use Custodia\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{


    public function users() {
        $users = User::orderBy('id', 'desc')->paginate(10);
        return view('admin.users.users', ['users' => $users]);
    }

    public function newUser() {
        return view('admin.users.new');
    }

    public function editUser(User $user) {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function createUser(CreateUserRequest $request)
    {
        $user = $this->saveUser($request);

        return redirect('/admin/users');
    }

    private function saveUser($request){
        $user = new User();
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->google_auth_id = $request->google_auth_id;
        $user->firebase_registration_token = $request->firebase_registration_token;
        $user->role_id = $request->role;

        $user->save();

        $userProfile = new UserProfile();
        $userProfile->user_id = $user->id;
        $userProfile->home_type_id = $request->home_type;

        $userProfile->address = $request->address;
        $userProfile->zip = $request->zip;

        if ($request->has('city')) {
          $userProfile->city = $request->city;
        }


        $userProfile->save();

        if ($request->has('outdoor_spaces')){
            foreach ($request->outdoor_spaces as $outdoor_space){
                $userProfile->outdoorSpaces()->attach($outdoor_space);
            }
        }

        if ($request->has('driveways')){
            foreach ($request->driveways as $driveway){
                $userProfile->drivewayTypes()->attach($driveway);
            }
        }

        if ($request->has('mobility_issues')){
            foreach ($request->mobility_issues as $issue){
                $userProfile->mobilityIssues()->attach($issue);
            }
        }

        if ($request->has('features')){
            foreach ($request->features as $feature){
                $userProfile->homeFeatures()->attach($feature);
            }
        }

        if ($request->has('management_plans')){
            foreach ($request->management_plans as $plan){
                $userProfile->managementPlans()->attach($plan);
            }
        }

        $userProfile->save();
        return $user;
    }

    public function updateUser(StoreUserRequest $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->google_auth_id = $request->google_auth_id;
        $user->firebase_registration_token = $request->firebase_registration_token;
        $user->role_id = $request->role;

        $user->save();

        $userProfile = $user->userProfile;
        $userProfile->user_id = $user->id;
        $userProfile->home_type_id = $request->home_type;

        $userProfile->address = $request->address;
        $userProfile->zip = $request->zip;

        if ($request->has('city')) {
          $userProfile->city = $request->city;
        }


        $userProfile->score = $request->score;
        $userProfile->save();

        $userProfile->outdoorSpaces()->detach();
        if ($request->has('outdoor_spaces')){
            foreach ($request->outdoor_spaces as $outdoor_space){
                $userProfile->outdoorSpaces()->attach($outdoor_space);
            }
        }

        $userProfile->drivewayTypes()->detach();
        if ($request->has('driveways')){
            foreach ($request->driveways as $driveway){
                $userProfile->drivewayTypes()->attach($driveway);
            }
        }

        $userProfile->mobilityIssues()->detach();
        if ($request->has('mobility_issues')){
            foreach ($request->mobility_issues as $issue){
                $userProfile->mobilityIssues()->attach($issue);
            }
        }

        $userProfile->homeFeatures()->detach();
        if ($request->has('features')){
            foreach ($request->features as $feature){
                $userProfile->homeFeatures()->attach($feature);
            }
        }

        // $userProfile->homeFeatures()->detach();
        // if ($request->has('features')){
        //     foreach ($request->features as $feature){
        //         $userProfile->homeFeatures()->attach($feature);
        //     }
        // }

        $userProfile->save();
        return redirect('/admin/users');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect('/admin/users');
    }

    public function apiAuthenticateUser(Request $request) {
        if ($request->has('phone') && strlen($request->phone) > 0){
            $phone = $request->phone;
            $user = User::where('phone', '=', $phone)->firstOrFail();
            return response()->json(['id' => $user->id], 200);
        } else if ($request->has('gauth') && strlen($request->gauth) > 0){
            $gauth = $request->gauth;
            $user = User::where('google_auth_id', '=', $gauth)->firstOrFail();
            return response()->json(['id' => $user->id], 200);
        } else {
            return response()->json(['error' => 'Invalid parameters.'], 400);
        }
    }
    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    public function apiCreateUser(Request $request){

        $validation = Validator::make($request->all(),CreateUserRequest::apiRules());
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                $request->password = $this->generateRandomString();
                $request->role = 2;
                $user = $this->saveUser($request);
                if ($user->id){
                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error"], 400);
                }
            } catch (\Exception $e){
                return response()->json(['message' => $e], 400);
            }

        }

    }

    public function apiGetUserScore(User $user){
        return response()->json(['score' => $user->userProfile->score], 200);
    }

    public function apiSetUserScore(User $user, Request $request){
        $validation = Validator::make($request->all(),['score' => 'required|numeric']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                $profile = $user->userProfile;
                $profile->score = $request->score;
                $profile->save();
                if ($profile->score == $request->score){
                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error"], 400);
                }
            } catch (\Exception $e){
                return response()->json(['message' => $e], 400);
            }
        }

        return response()->json(['score' => $user->userProfile->score], 200);
    }

    public function apiGetUserDoneMaintenanceItems(User $user){
        return response()->json(['maintenance_items' => $user->doneMaintenanceItems], 200);
    }

    public function apiGetUserIgnoredMaintenanceItems(User $user){
        return response()->json(['maintenance_items' => $user->ignoredMaintenanceItems], 200);
    }


    public function apiGetTop3MaintenanceItemsTodayByUser(User $user) {
        $month = date('F');
        $query = $this->getUserItemsJoinQuery($user) . "
            ORDER BY ITEMS.points DESC
            LIMIT 3;
        ";

        $results = DB::select($query);

        $ret = collect();

        foreach ($results as $result){
            $m = MaintenanceItem::with("months")->find($result->id);
            $str = date('F');
            foreach ($m->months as $month){
              if ($month->month == $str) {
                $m->summary = $month->description;
              }
            }
            $ret->push($m);
        }

        return response()->json(['maintenance_items' => $ret], 200);
    }


    public function apiGetTop3MaintenanceItemsTodayByUserAndSection(User $user, Section $section){
        $query = $this->getUserItemsJoinQuery($user) . "
            and ITEMS.section_id = {$section->id}
            ORDER BY ITEMS.points DESC
            LIMIT 3;
        ";

        $results = DB::select($query);

        $ret = collect();

        foreach ($results as $result){
            $m = MaintenanceItem::with("months")->find($result->id);
            $str = date('F');
            foreach ($m->months as $month){
              if ($month->month == $str) {
                $m->summary = $month->description;
              }
            }
            $ret->push($m);
        }

        return response()->json(['maintenance_items' => $ret], 200);
    }

    private function getUserItemsJoinQuery(User $user){
        $month = date('F');
        // $query = "
        //     select distinct(ITEMS.id) from maintenance_items ITEMS
        //     join home_type_maintenance_item ITEM_HOME_TYPE on ITEMS.id = ITEM_HOME_TYPE.maintenance_item_id
        //     join maintenance_item_monthly_event ITEM_MONTHLY_EVENT on ITEMS.id = ITEM_MONTHLY_EVENT.maintenance_item_id
        //     join monthly_events MONTHLY_EVENT on ITEM_MONTHLY_EVENT.monthly_event_id = MONTHLY_EVENT.id
        //     join user_profiles PROFILE on PROFILE.home_type_id = ITEM_HOME_TYPE.home_type_id
        //     join outdoor_space_type_user_profile USER_OUTDOOR_SPACE on PROFILE.id = USER_OUTDOOR_SPACE.user_profile_id
        //     join maintenance_item_outdoor_space_type ITEM_OUTDOOR_SPACE on ITEMS.id = ITEM_OUTDOOR_SPACE.maintenance_item_id and ITEM_OUTDOOR_SPACE.outdoor_space_type_id = USER_OUTDOOR_SPACE.outdoor_space_type_id
        //     join driveway_type_user_profile USER_DRIVEWAY_TYPE on PROFILE.id = USER_DRIVEWAY_TYPE.user_profile_id
        //     join driveway_type_maintenance_item ITEM_DRIVEWAY_TYPE on ITEMS.id = ITEM_DRIVEWAY_TYPE.maintenance_item_id and ITEM_DRIVEWAY_TYPE.driveway_type_id = USER_DRIVEWAY_TYPE.driveway_type_id
        //     join mobility_issue_type_user_profile USER_MOBILITY_ISSUE_TYPE on PROFILE.id = USER_MOBILITY_ISSUE_TYPE.user_profile_id
        //     join maintenance_item_mobility_issue_type ITEM_MOBILITY_ISSUE_TYPE on ITEMS.id = ITEM_MOBILITY_ISSUE_TYPE.maintenance_item_id and ITEM_MOBILITY_ISSUE_TYPE.mobility_issue_type_id = USER_MOBILITY_ISSUE_TYPE.mobility_issue_type_id
        //     join home_feature_user_profile USER_HOME_FEATURE on PROFILE.id = USER_HOME_FEATURE.user_profile_id
        //     join home_feature_maintenance_item ITEM_HOME_FEATURE on ITEMS.id = ITEM_HOME_FEATURE.maintenance_item_id and ITEM_HOME_FEATURE.home_feature_id = USER_HOME_FEATURE.home_feature_id
        //     left outer join maintenance_item_done_user ITEMS_DONE_USER on ITEMS_DONE_USER.maintenance_item_id = ITEMS.id and ITEMS_DONE_USER.user_id = PROFILE.user_id
        //     left outer join maintenance_item_ignored_user ITEMS_IGNORED_USER on ITEMS_IGNORED_USER.maintenance_item_id = ITEMS.id and ITEMS_IGNORED_USER.user_id = PROFILE.user_id
        //     where PROFILE.id = {$user->userProfile->id}
        //     and ITEMS_DONE_USER.id IS NULL
        //     and ITEMS_IGNORED_USER.id IS NULL
        //     and MONTHLY_EVENT.month = \"{$month}\"
        // ";


        $query = "
            select distinct(ITEMS.id) from maintenance_items ITEMS
            join home_type_maintenance_item ITEM_HOME_TYPE on ITEMS.id = ITEM_HOME_TYPE.maintenance_item_id
            join months on ITEMS.id = months.maintenance_item_id
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
            and ITEMS_DONE_USER.id IS NULL
            and ITEMS_IGNORED_USER.id IS NULL
            and months.month = \"{$month}\"
        ";

        return $query;
    }

    public function apiGetOutdoorSpaces(User $user){
        return response()->json(['outdoor_spaces' => $user->userProfile->outdoorSpaces], 200);
    }

    public function apiSetOutdoorSpaces(User $user, Request $request){
        $validation = Validator::make($request->all(),['outdoor_spaces' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('outdoor_spaces')){
                    $profile = $user->userProfile;
                    $profile->outdoorSpaces()->detach();
                    foreach ($request->outdoor_spaces as $outdoorSpaceId){
                        $outdoorSpace = OutdoorSpaceType::findOrFail($outdoorSpaceId);
                        $profile->outdoorSpaces()->attach($outdoorSpace);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e){
                return response()->json(['message' => $e], 400);
            }
        }
    }

    public function apiGetDriveways(User $user){
        return response()->json(['driveways' => $user->userProfile->drivewayTypes], 200);
    }

    public function apiSetDriveways(User $user, Request $request){
        $validation = Validator::make($request->all(),['driveways' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('driveways')){
                    $profile = $user->userProfile;
                    $profile->drivewayTypes()->detach();
                    foreach ($request->driveways as $drivewayId){
                        $driveway = DrivewayType::findOrFail($drivewayId);
                        $profile->drivewayTypes()->attach($driveway);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e){
                return response()->json(['message' => $e], 400);
            }
        }
    }

    public function apiGetHomeFeatures(User $user){
        return response()->json(['home_features' => $user->userProfile->homeFeatures], 200);
    }

    public function apiSetHomeFeatures(User $user, Request $request){
        $validation = Validator::make($request->all(),['home_features' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('home_features')){
                    $profile = $user->userProfile;
                    $profile->homeFeatures()->detach();
                    foreach ($request->home_features as $featureId){
                        $feature = HomeFeature::findOrFail($featureId);
                        $profile->homeFeatures()->attach($feature);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e){
                return response()->json(['message' => $e], 400);
            }
        }
    }

    public function apiGetMobilityIssues(User $user){
        return response()->json(['mobility_issues' => $user->userProfile->mobilityIssues], 200);

    }

    public function getUserDetails(User $user) {
        $user = User::findOrFail($user->id);
        $user->score = $user->userProfile->score;


        //With Mom, Dad, Myself. Mom and Dad = Your Parents | Mom, Dad, Myself = Families | Mom = Moms | Dad = Dad’s | Myself = Your
        $plans = $user->userProfile->managementPlans->pluck('id')->toArray();

        if (count($plans) == 2 && in_array(1, $plans) && in_array(2, $plans)) {
          $user->title = "Your Parents Home Management Plan";
        }

        if (count($plans) == 3 && in_array(1, $plans) && in_array(2, $plans) && in_array(3, $plans)) {
          $user->title = "Your Families Home Management Plan";
        }

        if (count($plans) == 2 && in_array(1, $plans)  && in_array(3, $plans)) {
          $user->title = "Your Families Home Management Plan";
        }

        if (count($plans) == 2 && in_array(1, $plans)  && in_array(2, $plans)) {
          $user->title = "Your Families Home Management Plan";
        }

        if (count($plans) == 1 && in_array(1, $plans)) {
          $user->title = "Your Moms Home Management Plan";
        }

        if (count($plans) == 1 && in_array(2, $plans)) {
          $user->title = "Your Dad’s Home Management Plan";
        }

        if (count($plans) == 1 && in_array(3, $plans)) {
          $user->title = "Your Home Management Plan";
        }

        return response()->json(['message' => "Success", "user" => $user], 200);
    }

    public function apiSetMobilityIssues(User $user, Request $request) {
        $validation = Validator::make($request->all(),['mobility_issues' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('mobility_issues')){
                    $profile = $user->userProfile;
                    $profile->mobilityIssues()->detach();
                    foreach ($request->mobility_issues as $issueId){
                        $issue = MobilityIssueType::findOrFail($issueId);
                        $profile->mobilityIssues()->attach($issue);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e){
                return response()->json(['Error' => $e], 400);
            }
        }
    }

    public function apiGetIsMaintenanceItemDone(User $user, MaintenanceItem $maintenanceItem){
        return response()->json($user->doneMaintenanceItems->contains($maintenanceItem), 200);
    }

    public function apiGetIsMaintenanceItemIgnored(User $user, MaintenanceItem $maintenanceItem){
        return response()->json($user->ignoredMaintenanceItems->contains($maintenanceItem), 200);
    }

    public function apiSetMaintenanceItemDone(User $user, MaintenanceItem $maintenanceItem, Request $request){
            if ($maintenanceItem) {
                $user->doneMaintenanceItems()->attach($maintenanceItem);
                $profile = $user->userProfile;
                $profile->score = $profile->score + $maintenanceItem->points;
                $profile->save();
                $user->save();
                return response()->json(['message' => "Success", "score" => $profile->score], 200);
            } else {
                return response()->json('Error',  400);
            }
    }

    public function apiSetMaintenanceItemIgnored(User $user, MaintenanceItem $maintenanceItem, Request $request){
        if ($maintenanceItem) {
            $user->ignoredMaintenanceItems()->attach($maintenanceItem);
            return response()->json(['message' => "Success"], 200);
        } else {
            return response()->json('Error',  400);
        }
    }
}
