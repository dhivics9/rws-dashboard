<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    // route auth
    public function loginAuth(){
        return view('login');
    }

}
