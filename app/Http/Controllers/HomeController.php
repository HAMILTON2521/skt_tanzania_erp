<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): RedirectResponse
    {
        $user = request()->user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/');
    }
}
