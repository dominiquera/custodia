<?php

namespace Custodia\Http\Controllers\Adminpanel;

use Custodia\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use Custodia\Http\Controllers\Controller;
use Custodia\User;
use Custodia\Http\Requests\StoreUserRequest;

class Dashboard extends Controller
{
  public function index() {
      return view('admin.dashboard');
  }
}
