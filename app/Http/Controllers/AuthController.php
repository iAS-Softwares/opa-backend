<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserSession;
use App\Models\LoginRequest;
use App\Models\SignUp;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Laravel\Socialite\Facades\Socialite;
use Exception;


class AuthController extends Controller
{
	/*
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
	*/
	
	public function redirectToGoogle( $event, $ticket, Request $request) {
		$request->session()->push('ticket', $ticket);
		$request->session()->push('event', $event);
        return Socialite::driver('google')
        ->redirectUrl(route('auth.redirectToGoogleCallback'))
		->redirect();
    }
	
	public function redirectToFacebook( $event, $ticket, Request $request) {
		$request->session()->push('ticket', $ticket);
		$request->session()->push('event', $event);
        return Socialite::driver('facebook')
        ->redirectUrl(route('auth.redirectToFacebookCallback'))
		->redirect();
    }
	
	public function redirectToApple( $event, $ticket, Request $request) {
		$request->session()->push('ticket', $ticket);
		$request->session()->push('event', $event);
		
    $cookies = [
        cookie('event', $event, 1, '/', null, false, true, 'none'), 
        cookie('ticket', $ticket, 1, '/', null, false, true, 'none'),
    ];
		
		//return response()->json(['event'=>$request->session()->pull('event', url('/')), 'ticket'=>$request->session()->pull('ticket', url('/'))]);
        $redirectUrl = Socialite::driver('apple')
        ->redirectUrl(route('auth.redirectToAppleCallback'))
        ->with(['ticket' => $ticket, 'event'=> $event])
		->redirect()
		->getTargetUrl();
		
        return redirect()->to($redirectUrl)->withCookies($cookies);
		
    $response = new response('Redirecting...');
    $response->withCookie(cookie('event', $event))
    ->withCookie(cookie('ticket', $ticket));

    // Redirect to a new page
    return $response->header('Location', route('auth.redirectToAppleCallback'))->send();
    return $response->header('Location', $redirectUrl)->send();
		
        return redirect()->to($redirectUrl)->withCookies($cookies);
    }
	
    public function handleGoogleCallback(Request $request)
    {
		try {
      
			$ticket = $request->session()->pull('ticket', url('/'));
			$event = $request->session()->pull('event', url('/'));
            
			$user = Socialite::driver('google')
					->redirectUrl(route('auth.redirectToGoogleCallback'))
					->user();
			//return response()->json(['user'=>$user, 'ticket'=>$ticket], 500);
       
        } catch (Exception $e) {
            dd($e->getMessage());
        }
            $finduser = User::where('email', $user->email)->first();
       
            if($finduser){
				//auth user in
				if($finduser->google_id != $user->id){
                    $finduser->google_id = $user->id;
                    $finduser->save();
				}
       
            }else{
                $finduser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => encrypt('123456dummy')
                ]);
            }
			if($event[0]=='login'){
			//removing old LoginRequest events
			$deleted = LoginRequest::where('email', $user->email)->where('ticket', '!=' , $ticket)->delete();
				    $loginRequest = LoginRequest::where('ticket', $ticket)->first();
				    $loginRequest->status="success";
				    $loginRequest->email=$user->email;
				    $loginRequest->save();
				    
		return view('social-login.confirm');
            return response()->json(['status'=>'success', 'action'=>'Close this page if it does not closes on its own'], 200);
				}
				elseif($event[0]=='signup'){
			//removing old LoginRequest events
			$deleted = SignUp::where('email', $user->email)->where('ticket', '!=' , $ticket)->delete();
				    $signupRequest = SignUp::where('ticket', $ticket)->first();
				    $signupRequest->email=$user->email;
				    $signupRequest->status="success";
				    $signupRequest->save();
				    
		return view('social-login.confirm');
            return response()->json(['status'=>'success', 'action'=>'Close this page if it does not closes on its own'], 200);
				}
        return response()->json(['status'=>'failed', 'event'=>$event], 200);
            
      
    }
	
    public function handleAppleCallback(Request $request)
    {
		try {
      
			$ticket = $request->cookie('ticket');
			$event =  $request->cookie('event');
			//return response()->json(['ticket'=>$ticket, 'event'=>$event, 't'=>$request->input('ticket'),'e'=>$request->cookies->all()], 500);
            
			$user = Socialite::driver('apple')
					->redirectUrl(route('auth.redirectToAppleCallback'))
					->user();
			//return response()->json(['user'=>$user, 'ticket'=>$ticket, 'event'=>$event, 't'=>$request->input('ticket'),'e'=>$request->cookies->all()], 500);
            
            
            
       
       
        } catch (Exception $e) {
            dd($e->getMessage());
        }
            $finduser = User::where('email', $user->email)->first();
       
            if($finduser){
				//auth user in
				if($finduser->apple_id != $user->id){
                    $finduser->apple_id = $user->id;
                    $finduser->save();
				}
       
            }else{
                $finduser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'apple_id'=> $user->id,
                    'password' => encrypt('123456dummy')
                ]);
            }
            
            
            if($event=='login'){
                $eventF='login';
            }elseif($event=='signup'){
                $eventF='signup';
            }else{
                
                
            }
            
            
			if($eventF=='login'){
			//removing old LoginRequest events
			$deleted = LoginRequest::where('email', $user->email)->where('ticket', '!=' , $ticket)->delete();
				    $loginRequest = LoginRequest::where('ticket', $ticket)->first();
				    $loginRequest->status="success";
				    $loginRequest->email=$user->email;
				    $loginRequest->save();
				    
		    return view('social-login.confirm');
            return response()->json(['status'=>'success', 'action'=>'Close this page if it does not closes on its own'], 200);
				}
				elseif($eventF=='signup'){
			//removing old LoginRequest events
			$deleted = SignUp::where('email', $user->email)->where('ticket', '!=' , $ticket)->delete();
				    $signupRequest = SignUp::where('ticket', $ticket)->first();
				    $signupRequest->email=$user->email;
				    $signupRequest->status="success";
				    $signupRequest->save();
				    
		    return view('social-login.confirm');
            return response()->json(['status'=>'success', 'action'=>'Close this page if it does not closes on its own'], 200);
				}
        return response()->json(['status'=>'failed', 'event'=>$event], 200);
            
      
    }
	
    public function handleFacebookCallback(Request $request)
    {
		try {
      
			$ticket = $request->session()->pull('ticket', url('/'));
			$event = $request->session()->pull('event', url('/'));
            
			$user = Socialite::driver('facebook')
					->redirectUrl(route('auth.redirectToFacebookCallback'))
					->user();
			//return response()->json(['user'=>$user, 'ticket'=>$ticket], 500);
       
        } catch (Exception $e) {
            dd($e->getMessage());
        }
            $finduser = User::where('email', $user->email)->first();
       
            if($finduser){
				//auth user in
				if($finduser->facebook_id != $user->id){
                    $finduser->facebook_id = $user->id;
                    $finduser->save();
				}
       
            }else{
                $finduser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'facebook_id'=> $user->id,
                    'password' => encrypt('123456dummy')
                ]);
            }
			if($event[0]=='login'){
			//removing old LoginRequest events
			$deleted = LoginRequest::where('email', $user->email)->where('ticket', '!=' , $ticket)->delete();
				    $loginRequest = LoginRequest::where('ticket', $ticket)->first();
				    $loginRequest->status="success";
				    $loginRequest->email=$user->email;
				    $loginRequest->save();
				    
		return view('social-login.confirm');
            return response()->json(['status'=>'success', 'action'=>'Close this page if it does not closes on its own'], 200);
				}
				elseif($event[0]=='signup'){
			//removing old LoginRequest events
			$deleted = SignUp::where('email', $user->email)->where('ticket', '!=' , $ticket)->delete();
				    $signupRequest = SignUp::where('ticket', $ticket)->first();
				    $signupRequest->email=$user->email;
				    $signupRequest->status="success";
				    $signupRequest->save();
				    
		return view('social-login.confirm');
            return response()->json(['status'=>'success', 'action'=>'Close this page if it does not closes on its own'], 200);
				}
        return response()->json(['status'=>'failed', 'event'=>$event], 200);
            
      
    }
	/**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
    	$validator = Validator::make($request->all(), [
            'event' => 'required|in:login,signup',
            'ticket' => 'required|string',
        ]);
        
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
        if($request->input('event')=='login'){
            $myRequest = LoginRequest::where('ticket', $request->input('ticket'))->first();
        }
        elseif($request->input('event')=='signup'){
            $myRequest = SignUpRequest::where('ticket', $request->input('ticket'))->first();
        }
        
        
		if(!$myRequest){
			return response()->json(['error' => ['ticket' => 'unauthorized']],403);
		}
        
        if($myRequest->status=='success'){
        
		//check time
		$now = Carbon::now();
		$newUser=User::where('email', $myRequest->email)->first();
		$token=Str::random(64) ;
		while (UserSession::where('token', $token)->count()>0) {
			$token=Str::random(64) ;
		}
		$newSession = UserSession::create([
					'token'=>$token,
					'user_id'=>$newUser->id,
					]);
		
		$myRequest->delete();
		
		return response()->json([
		                'status'=>'success',
						'email'=>$newUser->email,
						'phone'=>$newUser->phone,
						'last_used_at'=>$now,
						'token'=>$token
						], 200);
        }
        else{
            return response()->json(['status'=>'processing', 'event'=>$event], 200);
            
        }
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}
