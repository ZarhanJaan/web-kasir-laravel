<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /** Refuse inactive accounts while keeping a clear, login-page error message. */
    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        if ($user && !$user->status && Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['User ini tidak aktif.'],
            ]);
        }

        return $this->guard()->attempt(
            $this->credentials($request) + ['status' => true],
            $request->filled('remember')
        );
    }
}
