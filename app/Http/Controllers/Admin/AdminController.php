<?php

namespace Custodia\Http\Controllers\Admin;


use Custodia\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }
}
