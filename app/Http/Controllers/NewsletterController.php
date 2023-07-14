<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSession;
use App\Models\Newsletter;
use App\Models\Profile;
use Validator;

class NewsletterController extends Controller
{
    public function subscriptionPage()
    {
        return view('subscriptionPage');
    }
	
	
    public function subscription_request(Request $request){
		
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'wants' => 'nullable|boolean',
			'token' => 'nullable|string|max:64',
        ]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		
		if(null === ($request->input('wants'))){
				$wants=1;
		}
		else{
			$wants=($request->input('wants'));
		    $userSession = UserSession::where('token', $request->input('token'))->first();
		    $userId = ($userSession)?$userSession->id:0;
	    	$askedProfile = Profile::where('user_id', $userId)->first();
	    	
		}
		    if($wants == -1){
	    	    if($askedProfile->email != $email){
		            return response()->json(['error' => ['access' => 'denied']],200);
	    	        
	    	    }
		    }
		
		
		
		
		$newsletter=Newsletter::where('email', $request->input('email'))->first();
		    $prevSubs='unsubscribed';
			
		if($newsletter){
			if($newsletter->hasDisabled){
				return response()->json(['error' => ['message' => 'You have disabled, kindly reactivate from app.']],403);
			}
			if($newsletter->isSubscribed){
				$prevSubs='subscribed';
			}
			$newsletter->isSubscribed=$wants;
			$newsletter->save();
		}
		else{
			$newsletter = Newsletter::create([
				'email'=>$request->input('email'),
				'isSubscribed' => $wants,
			]);
		}
		if($prevSubs=='unsubscribed' && $wants){
    		app('App\Http\Controllers\Email')->subscription_email($request->input('email'));
		}
		return response()->json(['success' => ['subscribed' => $wants, 'previously'=>$prevSubs]],200);
	}
}
