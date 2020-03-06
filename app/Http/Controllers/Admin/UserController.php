<?php

namespace Custodia\Http\Controllers\Admin;

use Carbon\Carbon;
use Custodia\DrivewayType;
use Custodia\HomeFeature;
use Custodia\HomeType;
use Custodia\Http\Requests\UpdatePasswordRequest;
use Custodia\Http\Requests\User\CreateUserRequest;
use Custodia\Http\Requests\User\StoreUserRequest;
use Custodia\Interval;
use Custodia\MaintenanceItem;
use Custodia\MobilityIssueType;
use Custodia\MonthlyEvent;
use Custodia\OutdoorSpaceType;
use Custodia\Role;
use Custodia\Section;
use Custodia\Services\UserService;
use Custodia\User;
use Custodia\UserProfile;
use Custodia\UserToken;
use Custodia\WeatherTriggerType;
use Illuminate\Http\Request;
use Custodia\Http\Controllers\Controller;
use Custodia\Services\WeatherTriggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mail;


class UserController extends Controller
{


    public function users()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('admin.users.users', ['users' => $users]);
    }

    public function newUser()
    {
        return view('admin.users.new');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function createUser(CreateUserRequest $request)
    {
        $user = $this->saveUser($request);

        return redirect('/admin/users');
    }

    private function saveUser($request)
    {
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

        if ($request->has('outdoor_spaces')) {
            foreach ($request->outdoor_spaces as $outdoor_space) {
                $userProfile->outdoorSpaces()->attach($outdoor_space);
            }
        }

        if ($request->has('driveways')) {
            foreach ($request->driveways as $driveway) {
                $userProfile->drivewayTypes()->attach($driveway);
            }
        }

        if ($request->has('mobility_issues')) {
            foreach ($request->mobility_issues as $issue) {
                $userProfile->mobilityIssues()->attach($issue);
            }
        }

        if ($request->has('features')) {
            foreach ($request->features as $feature) {
                $userProfile->homeFeatures()->attach($feature);
            }
        }

        if ($request->has('management_plans')) {
            foreach ($request->management_plans as $plan) {
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
        if ($request->has('outdoor_spaces')) {
            foreach ($request->outdoor_spaces as $outdoor_space) {
                $userProfile->outdoorSpaces()->attach($outdoor_space);
            }
        }

        $userProfile->drivewayTypes()->detach();
        if ($request->has('driveways')) {
            foreach ($request->driveways as $driveway) {
                $userProfile->drivewayTypes()->attach($driveway);
            }
        }

        $userProfile->mobilityIssues()->detach();
        if ($request->has('mobility_issues')) {
            foreach ($request->mobility_issues as $issue) {
                $userProfile->mobilityIssues()->attach($issue);
            }
        }

        $userProfile->homeFeatures()->detach();
        if ($request->has('features')) {
            foreach ($request->features as $feature) {
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

    public function apiAuthenticateUser(Request $request)
    {
        if ($request->has('phone') && strlen($request->phone) > 0) {
            $phone = $request->phone;
            $user = User::where('phone', '=', $phone)->firstOrFail();
            return response()->json(['id' => $user->id], 200);
        } else if ($request->has('gauth') && strlen($request->gauth) > 0) {
            $gauth = $request->gauth;
            $user = User::where('google_auth_id', '=', $gauth)->firstOrFail();
            return response()->json(['id' => $user->id], 200);
        } else {
            return response()->json(['error' => 'Invalid parameters.'], 400);
        }
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function apiCreateUser(Request $request)
    {

        $validation = Validator::make($request->all(), CreateUserRequest::apiRules());
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                $request->password = $this->generateRandomString();
                $request->role = 2;
                $user = $this->saveUser($request);
                if ($user->id) {
                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error"], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e], 400);
            }

        }
    }

    public function apiGetUserScore(User $user, UserService $userService)
    {
        $score = $userService->getScoreByUser($user);
        return response()->json(['score' => $score], 200);
    }

    public function apiGetUserPotentialScore(User $user, UserService $userService)
    {
        $potential = $userService->getPotentialScoreByUser($user);
        return response()->json([ 'score' => $potential ], 200);
    }

    public function apiGetUserTopScore(User $user)
    {
        return response()->json([ 'score' => $user->userProfile->score ], 200);
    }

    public function apiSetUserScore(User $user, Request $request)
    {
        $validation = Validator::make($request->all(), ['score' => 'required|numeric']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                $profile = $user->userProfile;
                $profile->score = $request->score;
                $profile->save();
                if ($profile->score == $request->score) {
                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error"], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e], 400);
            }
        }

        return response()->json(['score' => $user->userProfile->score], 200);
    }

    public function apiUpdateUserToken(User $user, Request $request)
    {
        $validation = Validator::make($request->all(), [
            'token' => 'required',
            'scope' => 'required'
        ]);
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                $token_exists = $user->tokens()->where('token', $request->token)
                                               ->where('scope', $request->scope)
                                               ->exists();

                if (!$token_exists) {
                    $userToken = new UserToken();
                    $userToken->user_id = $user->id;
                    $userToken->token = $request->token;
                    $userToken->hash = hash('sha256', $request->token);
                    $userToken->scope = $request->scope;
                    $userToken->save();
                }

                return response()->json(['message' => "Success"], 200);
            } catch (\Exception $e) {
                return response()->json(['message' => $e], 400);
            }
        }

        return response()->json(['score' => $user->userProfile->score], 200);
    }

    public function apiGetUserDoneMaintenanceItems(User $user)
    {
        return response()->json(['maintenance_items' => $user->doneMaintenanceItems], 200);
    }

    public function apiGetUserIgnoredMaintenanceItems(User $user)
    {
        return response()->json(['maintenance_items' => $user->ignoredMaintenanceItems], 200);
    }


    public function apiGetTop3MaintenanceItemsTodayByUser(User $user, UserService $userService, WeatherTriggerService $weatherTriggerService)
    {
        $ret = $userService->getTop3MaintenanceItemsTodayByUser($user, $weatherTriggerService);
        return response()->json(['maintenance_items' => $ret], 200);
    }


    public function apiGetTop3MaintenanceItemsTodayByUserAndSection(User $user, Section $section,  UserService $userService, WeatherTriggerService $weatherTriggerService)
    {
        $ret = $userService->getTop3MaintenanceItemsTodayByUserAndSection($user, $section, $weatherTriggerService);
        return response()->json(['maintenance_items' => $ret], 200);
    }


    public function apiGetAllMaintenanceItemsTodayByUserAndSection(User $user, Section $section,  UserService $userService, WeatherTriggerService $weatherTriggerService)
    {
        $ret = $userService->getAllMaintenanceItemsTodayByUserAndSection($user, $section, $weatherTriggerService);
        return response()->json(['maintenance_items' => $ret], 200);
    }

    public function apiAutomate(User $user, MaintenanceItem $maintenanceItem)
    {

        $data = array("user" => $user->id, "item" => $maintenanceItem->title);

        $from_email = 'noreply@custodia.com';
        $to_name = "Test User";
        $to_email = "dorademacher@gmail.com";

        $user->ignoredMaintenanceItems()->attach($maintenanceItem);

        Mail::send('emails.automate', $data, function ($message) use ($to_name, $to_email, $from_email) {
            $message->to($to_email, $to_name)->subject('Automation Request');
            $message->from($from_email, 'Custodia');
        });

        return response()->json(['status' => "success"], 200);
    }

    public function apiGetOutdoorSpaces(User $user)
    {
        return response()->json(['outdoor_spaces' => $user->userProfile->outdoorSpaces], 200);
    }

    public function apiSetOutdoorSpaces(User $user, Request $request)
    {
        $validation = Validator::make($request->all(), ['outdoor_spaces' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('outdoor_spaces')) {
                    $profile = $user->userProfile;
                    $profile->outdoorSpaces()->detach();
                    foreach ($request->outdoor_spaces as $outdoorSpaceId) {
                        $outdoorSpace = OutdoorSpaceType::findOrFail($outdoorSpaceId);
                        $profile->outdoorSpaces()->attach($outdoorSpace);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e], 400);
            }
        }
    }

    public function apiGetDriveways(User $user)
    {
        return response()->json(['driveways' => $user->userProfile->drivewayTypes], 200);
    }

    public function apiSetDriveways(User $user, Request $request)
    {
        $validation = Validator::make($request->all(), ['driveways' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('driveways')) {
                    $profile = $user->userProfile;
                    $profile->drivewayTypes()->detach();
                    foreach ($request->driveways as $drivewayId) {
                        $driveway = DrivewayType::findOrFail($drivewayId);
                        $profile->drivewayTypes()->attach($driveway);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e], 400);
            }
        }
    }

    public function apiGetHomeFeatures(User $user)
    {
        return response()->json(['home_features' => $user->userProfile->homeFeatures], 200);
    }

    public function apiSetHomeFeatures(User $user, Request $request)
    {
        $validation = Validator::make($request->all(), ['home_features' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('home_features')) {
                    $profile = $user->userProfile;
                    $profile->homeFeatures()->detach();
                    foreach ($request->home_features as $featureId) {
                        $feature = HomeFeature::findOrFail($featureId);
                        $profile->homeFeatures()->attach($feature);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['message' => $e], 400);
            }
        }
    }

    public function apiGetMobilityIssues(User $user)
    {
        return response()->json(['mobility_issues' => $user->userProfile->mobilityIssues], 200);

    }

    public function getUserDetails(User $user)
    {
        $user = User::findOrFail($user->id);
        $user->score = $user->userProfile->score;


        //With Mom, Dad, Myself. Mom and Dad = Your Parents | Mom, Dad, Myself = Families | Mom = Moms | Dad = Dad’s | Myself = Your
        $plans = $user->userProfile->managementPlans->pluck('id')->toArray();

        //MOM AND DAD
        if (count($plans) == 2 && in_array(1, $plans) && in_array(2, $plans)) {
            $user->title = "Your Parents Home Management Plan";
            $user->subtitle = "Your objective is to complete these items and keep your parents happy and safe at home.";
            $user->outside = "Suggestions for outside your parents's home.";
            $user->inside = "Suggestions for inside your parents's home.";
        }
        //MOM DAD MYSELF
        elseif (count($plans) == 3 && in_array(1, $plans) && in_array(2, $plans) && in_array(3, $plans)) {
            $user->title = "Your Families Home Management Plan";
            $user->subtitle = "Your objective is to complete these items and keep your family happy and safe at home.";
            $user->outside = "Suggestions for outside your family's home.";
            $user->inside = "Suggestions for inside your family's home.";
        }
        //MOM AND MYSELF
        elseif (count($plans) == 2 && in_array(1, $plans) && in_array(3, $plans)) {
            $user->title = "Your Families Home Management Plan";
            $user->subtitle = "Your objective is to complete these items and keep your family happy and safe at home.";
            $user->outside = "Suggestions for outside your family's home.";
            $user->inside = "Suggestions for inside your family's home.";
        }
        //DAD AND MYSELF
        elseif (count($plans) == 2 && in_array(1, $plans) && in_array(2, $plans)) {
            $user->title = "Your Families Home Management Plan";
            $user->subtitle = "Your objective is to complete these items and keep your family happy and safe at home.";
            $user->outside = "Suggestions for outside your family's home.";
            $user->inside = "Suggestions for inside your family's home.";
        }
        //MOM
        elseif (count($plans) == 1 && in_array(1, $plans)) {
            $user->title = "Your Moms Home Management Plan";
            $user->subtitle = "Your objective is to complete these items and keep her happy and safe at home.";
            $user->outside = "Suggestions for outside your mom's home.";
            $user->inside = "Suggestions for inside your mom's home.";
        }
        //DAD
        elseif (count($plans) == 1 && in_array(2, $plans)) {
            $user->title = "Your Dad’s Home Management Plan";
            $user->subtitle = "Your objective is to complete these items and keep him happy and safe at home.";
            $user->outside = "Suggestions for outside your dad's home.";
            $user->inside = "Suggestions for inside your dad's home.";
        }
        //MYSELF
        elseif (count($plans) == 1 && in_array(3, $plans)) {
            $user->title = "Welcome ".$user->name.". Here is your current home score.";
            $user->subtitle = "Your objective is to complete these items and keep you happy and safe at home.";
            $user->outside = "Suggestions for outside your home.";
            $user->inside = "Suggestions for inside your home.";
        }
        //OTHER
        else {
            $user->title = "Welcome ".$user->name.". Here is your current home score.";
            $user->subtitle = "Your objective is to complete these items and keep everyone happy and safe at home.";
            $user->outside = "Suggestions for outside everyone's home.";
            $user->inside = "Suggestions for inside everyone's home.";
        }



        return response()->json(['message' => "Success", "user" => $user], 200);
    }

    public function apiSetMobilityIssues(User $user, Request $request)
    {
        $validation = Validator::make($request->all(), ['mobility_issues' => 'required|array']);
        $errors = $validation->errors();

        if (sizeof($errors) > 0) {
            return response()->json(["Errors" => $errors], 400);
        } else {
            try {
                if ($request->has('mobility_issues')) {
                    $profile = $user->userProfile;
                    $profile->mobilityIssues()->detach();
                    foreach ($request->mobility_issues as $issueId) {
                        $issue = MobilityIssueType::findOrFail($issueId);
                        $profile->mobilityIssues()->attach($issue);
                    }
                    $profile->save();

                    return response()->json(['message' => "Success"], 200);
                } else {
                    return response()->json(['message' => "Unknown error."], 400);
                }
            } catch (\Exception $e) {
                return response()->json(['Error' => $e], 400);
            }
        }
    }

    public function apiGetIsMaintenanceItemDone(User $user, MaintenanceItem $maintenanceItem)
    {
        return response()->json($user->doneMaintenanceItems->contains($maintenanceItem), 200);
    }

    public function apiGetIsMaintenanceItemIgnored(User $user, MaintenanceItem $maintenanceItem)
    {
        return response()->json($user->ignoredMaintenanceItems->contains($maintenanceItem), 200);
    }

    public function apiSetMaintenanceItemDone(User $user, MaintenanceItem $maintenanceItem, Request $request)
    {
        if ($maintenanceItem) {
            $user->doneMaintenanceItems()->attach($maintenanceItem);
            $profile = $user->userProfile;
            $profile->score = $profile->score + $maintenanceItem->points;
            $profile->save();
            $user->save();
            return response()->json(['message' => "Success", "score" => $profile->score], 200);
        } else {
            return response()->json('Error', 400);
        }
    }

    public function apiSetMaintenanceItemIgnored(User $user, MaintenanceItem $maintenanceItem, Request $request)
    {
        if ($maintenanceItem) {

            $date = new \DateTime();
            $date->modify("+1 year");

            $user->ignoredMaintenanceItems()->attach($maintenanceItem, ['ignore_until' => $date->format("Y-m-d")]);
            return response()->json(['message' => "Success"], 200);
        } else {
            return response()->json('Error', 400);
        }
    }

    public function apiSetMaintenanceItemIgnoreOnce(User $user, MaintenanceItem $maintenanceItem, Request $request)
    {
        if ($maintenanceItem) {

            $date = new \DateTime();
            $date->modify("+7 day");

            $user->ignoredMaintenanceItems()->attach($maintenanceItem, ['ignore_until' => $date->format("Y-m-d")]);
            return response()->json(['message' => "Success"], 200);
        } else {
            return response()->json('Error', 400);
        }
    }

    public function intervalAlgorithm($results, $user)
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
                    }
                    $m_ar['months']['interval'] = $month->interval->name;
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
            }else{
                $ret->push($m_ar);
            }
        }

        return $ret;
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changePassword(User $user)
    {
        return view('admin.users.change_password', compact('user'));
    }

    /**
     * @param UpdatePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = User::find($request->id);
        $user->password = Hash::make($request['password']);
        $user->save();

        return redirect()->route('manage-users');
    }
}
