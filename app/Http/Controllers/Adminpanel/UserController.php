<?php

namespace Custodia\Http\Controllers\Adminpanel;

use Custodia\Http\Controllers\Controller;
use Custodia\Http\Requests\CreateUserRequest;
use Custodia\Http\Requests\StoreUserRequest;
use Custodia\User;

class UserController extends Controller
{
    public function users() {
        $users = User::paginate(10);
        return view('admin.users.users', ['users' => $users]);
    }

    public function editUser(User $user) {
        return view('admin.users.edit', ['user' => $user]);
    }

    public function updateUser(StoreUserRequest $request)
    {
        $user = User::find($request->id);
        $user->name = $request->username;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        return redirect('/admin/users');
    }

    public function newUser(){
        return view('admin.users.create');
    }

    public function createUser(CreateUserRequest $request)
    {
        $user = new User();
        $user->name = $request->username;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->role = $request->role;
        $user->save();

        return redirect('/admin/users');
    }

    public function deleteUser(User $user) {
        $user->delete();
        return redirect('/admin/users');
    }

}
