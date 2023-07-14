<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
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
	
	public function login()
    {
        if(Auth::attempt(['email'=> request('email'),'password'=> request('password')])){
            $user = Auth::user();
            $token = $user->createToken('HMI App')->accessToken;
            $status = 200;
        }else{
            $token = 'unauthorize';
            $status = 401;
        }
        return response()->json(['token' => $token],$status);
    }
	
	public function api_login(Request $request)
    {
		switch ($request->input('protocol')) {
			case 'email':
				if(Auth::attempt(['email'=> request('email'),'password'=> request('password')])){
					$user = Auth::user();
					$token = $user->createToken('HMI App')->accessToken;
					$status = 200;
				}else{
					$token = 'unauthorize';
					$status = 401;
				}
				return response()->json(['token' => $token],$status);
				break;
			
			case 'facebook':
			
				break;
			
			default:
				$token = 'unauthorize';
				$status = 401;
				return response()->json(['token' => $token],$status);
		}
	}
}
