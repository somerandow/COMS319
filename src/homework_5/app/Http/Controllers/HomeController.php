<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        session([
            'username' => $user->username,
            'librarian' => $user->is_librarian
        ]);
        return \View::make('userhome');
    }
}
