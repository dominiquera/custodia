<?php

namespace Barebone\Http\Controllers\Adminpanel;

use Barebone\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use Barebone\Http\Controllers\Controller;
use Barebone\User;
use Barebone\Http\Requests\StoreUserRequest;

class Dashboard extends Controller
{
  public function index() {
      return view('admin.dashboard');
  }
}
