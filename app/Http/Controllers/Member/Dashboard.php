<?php

namespace Barebone\Http\Controllers\Member;

use Illuminate\Http\Request;
use Barebone\Http\Controllers\Controller;

class Dashboard extends Controller
{
  public function index() {
      return 'This is the member dashboard';
  }
}
