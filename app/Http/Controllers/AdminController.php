<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\User;
use App\UserProfile;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

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

    public function createUser(CreateUserRequest $request, $api = false)
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
}
