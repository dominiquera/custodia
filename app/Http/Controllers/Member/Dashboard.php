<?php

namespace Custodia\Http\Controllers\Member;

use Illuminate\Http\Request;
use Custodia\Http\Controllers\Controller;

class Dashboard extends Controller
{
  public function index() {
      return 'This is the member dashboard';
  }
}
