<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function createUser(CreateUserRequest $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->email = $request->email;
        $user->role_id = $request->role;

        $user->save();
        return redirect('/admin/users');
    }

    public function updateUser(StoreUserRequest $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role;

        $user->save();
        return redirect('/admin/users');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return redirect('/admin/users');
    }
}
