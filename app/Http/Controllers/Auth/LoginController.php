<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function redirectTo(): string
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Admin')) {
            return route('admin.dashboard');
        }

        return url('/');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showAdminLoginForm(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user) {
            return redirect('/');
        }

        return view('admin.auth.login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => __('These credentials do not match our records.')])
                ->onlyInput('email', 'remember');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasRole('Admin')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Your account does not have admin access.'])
                ->onlyInput('email', 'remember');
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
