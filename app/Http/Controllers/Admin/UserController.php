<?php

namespace Custodia\Http\Controllers\Admin;

use Custodia\Http\Requests\User\CreateUserRequest;
use Custodia\Http\Requests\User\StoreUserRequest;
use Custodia\MaintenanceItem;
use Custodia\User;
use Custodia\UserProfile;
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
        $user->role_id = $request->role;

        $user->save();

        $userProfile = new UserProfile();
        $userProfile->user_id = $user->id;
        $userProfile->home_type_id = $request->home_type;
        $userProfile->score = $request->score;
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

        $userProfile->save();
        return $user;
    }

    public function updateUser(StoreUserRequest $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role;

        $user->save();

        $userProfile = $user->userProfile;
        $userProfile->user_id = $user->id;
        $userProfile->home_type_id = $request->home_type;
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

        $userProfile->save();
        return redirect('/admin/users');
    }

    public function apiCreateUser(Request $request){

        $validation = Validator::make($request->all(),CreateUserRequest::rules());
        $errors = $validation->errors();

        if (sizeof($errors) > 0){
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
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

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect('/admin/users');
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


    public function apiGetTop3MaintenanceItemsTodayByUser(User $user){
        $month = date('F');
        $query = "
            select ITEMS.id from maintenance_items ITEMS
            join home_type_maintenance_item ITEM_HOME_TYPE on ITEMS.id = ITEM_HOME_TYPE.maintenance_item_id
            join maintenance_item_monthly_event ITEM_MONTHLY_EVENT on ITEMS.id = ITEM_MONTHLY_EVENT.maintenance_item_id
            join monthly_events MONTHLY_EVENT on ITEM_MONTHLY_EVENT.monthly_event_id = MONTHLY_EVENT.id
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
            and MONTHLY_EVENT.month = \"{$month}\"
            ORDER BY ITEMS.points DESC
            LIMIT 5;
        ";

        $results = DB::select($query);

        $ret = collect();

        foreach ($results as $result){
            $ret->push(MaintenanceItem::find($result->id));
        }

        return response()->json(['maintenance_items' => $ret], 200);
    }
}
