<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserSession;
use App\Models\LoginRequest;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class LoginRequestController extends Controller
{
    // Returns json with sign up links
    public function get_form(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
		$ticket=Str::random(32) ;
		while (LoginRequest::where('ticket', $ticket)->count()>0) {
			$ticket=Str::random(32) ;
		}
		
		$newLoginRequestEventArray=array(	'ticket'=>$ticket );
		$LoginRequestEvent = LoginRequest::create($newLoginRequestEventArray);
		
		
		//filter ips
		if( true ) {
		$links = array(
					'google' => route('auth.redirectToGoogle', ['ticket'=>$ticket, 'event'=>'login']),
					'facebook' => route('auth.redirectToFacebook', ['ticket'=>$ticket, 'event'=>'login']),
					'apple' => route('auth.redirectToApple', ['ticket'=>$ticket, 'event'=>'login']));
		
		$token = 'authorized';
		$status = 200;			
		}
		
		return response()->json(['token' => $token,'ticket'=> $LoginRequestEvent->ticket,
									'links'=>$links],$status);
	}
	
	//send otps to email and phone
    public function submit_form(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
    	$validator = Validator::make($request->all(), [
            'ticket' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|min:10',
            'phone_code' => 'nullable|string|max:4',
        ]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		
		$LoginRequestEvent=LoginRequest::where('ticket', $request->input('ticket'))->first();
		//ticket verification
		if(!$LoginRequestEvent){
				return response()->json(['error' => ['ticket'=> 'unauthorized']],403);
		}
		
		$testForEmail=true;
		if(null===$request->input('email')){
			if($request->input('phone')!='' && $request->input('phone_code') != ''){
				$testForEmail=false;
			}
			else{
				return response()->json(['error' => ['input'=>'invalid']],200);
			}
		}

		//check time
		$now = Carbon::now();			
		
		if($testForEmail){
			//checking if entry already exists
			$oldEntry=User::where('email', $request->input('email'))->count();
			if($oldEntry==0){
				return response()->json(['error' => ['user'=>'unregistered']],200);
			}
			//removing old LoginRequest events
			$deleted = LoginRequest::where('email', $request->input('email'))->where('ticket', '!=' , $request->input('ticket'))->delete();
			//used ticket check
			$repeatCheck = LoginRequest::where('email', '!=', '')->where('ticket', $request->input('ticket'))->count();
			if($repeatCheck>0){
				return response()->json(['error' => ['reuse'=>'Ticket has already been used.']],200);
			}			
		
			$newLoginRequestEventArray=array(
				'email'=>$request->input('email'),
				'email_otp'=>rand(1000,9999),
				'email_otp_count'=>1,
				'email_otp_start_at'=>$now
				);
						
		}
		else{
			//checking if entry already exists
			$oldEntry=User::where('phone', $request->input('phone'))->where('phone_code', $request->input('phone_code'))->count();
			if($oldEntry==0){
				return response()->json(['error' => ['user'=>'User Not Found!']],200);
			}
			//removing old LoginRequest events
			$deleted = LoginRequest::where('phone', $request->input('phone'))->where('phone_code', $request->input('phone_code'))->where('ticket', '!=' , $request->input('ticket'))->delete();
			//used ticket check
			$repeatCheck = LoginRequest::where('phone', '!=', '')->where('phone_code', '!=', '')->where('ticket', $request->input('ticket'))->count();
			if($repeatCheck>0){
				return response()->json(['error' => ['reuse'=>'Ticket has already been used.']],200);
			}
			$newLoginRequestEventArray=array(
				'phone'=>$request->input('phone'),
				'phone_code'=>$request->input('phone_code'),
				'phone_otp_count'=>1,
				'phone_otp_start_at'=>$now,
				'phone_otp'=>rand(1000,9999),
				);
		}
		

		$updateStatus = LoginRequest::where('ticket', $request->input('ticket'))->update($newLoginRequestEventArray);
		if(!$updateStatus){
			return response()->json(['error' => ['reuse'=>'Ticket has already been used.']],200);
		}
		$LoginRequestEvent=LoginRequest::where('ticket', $request->input('ticket'))->first();
		$ticket = $LoginRequestEvent->ticket;
		
		if($testForEmail){
    		//send otp function for email
	    	send_otp_email($LoginRequestEvent->email, $LoginRequestEvent->email_otp);
		}else {
		    //send otp function for phone
		    send_otp_phone($LoginRequestEvent->phone_code . $LoginRequestEvent->phone, $LoginRequestEvent->phone_otp);
		}
				
		$token = 'authorized';
		$status = 200;
		
		return response()->json(['ticket' => $ticket,
						'email'=>$LoginRequestEvent->email,
						'phone'=>$LoginRequestEvent->phone,
						'phone_code'=>$LoginRequestEvent->phone_code,
						'phone_otp_start_at'=>$LoginRequestEvent->phone_otp_start_at,
						'email_otp_start_at'=>$LoginRequestEvent->email_otp_start_at,
						],$status);
		
	}
	
	//resend otps to email and phone
    public function resend_otps(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
    	$validator = Validator::make($request->all(), [
            'ticket' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|min:10',
            'phone_code' => 'nullable|string|max:4',
        ]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		
		$LoginRequestEvent=LoginRequest::where('ticket', $request->input('ticket'))->first();
		//ticket verification
		if(!$LoginRequestEvent){
				return response()->json(['error' => ['ticket'=> 'unauthorized']],403);
		}
		
		$testForEmail=true;
		if(null===$request->input('email')){
			if($request->input('phone')!='' && $request->input('phone_code') != ''){
				$testForEmail=false;
			}
			else{
				return response()->json(['error' => ['input'=>'invalid']],200);
			}
		}

		//check time
		$now = Carbon::now();		
		$timeLimit=120;	
				$returnArray=array();
		
		if($testForEmail){
			$emailOtpPast = Carbon::parse($LoginRequestEvent->email_otp_start_at)->diffInSeconds($now); 
			$EmailTimeRemains=($timeLimit - $emailOtpPast);
			
			//Email Time Remaining Error Message
			if($emailOtpPast<=$timeLimit){
				$returnArray=array_merge($returnArray, [
					'email_time_remain'=> $EmailTimeRemains,
					'email_resend'=>false,
					'email_message'=>'Please try again after '.$EmailTimeRemains. ' seconds'
						]);
			}
			
			if( !empty($returnArray) ){
				return response()->json([
					'error'=> $returnArray
						],403);
			}
			else{
				$returnArray=array();
			}
		
			if(($emailOtpPast>$timeLimit)){
				$LoginRequestEvent->email_otp=rand(1000,9999);
				send_otp_email($LoginRequestEvent->email, $LoginRequestEvent->email_otp);
				
				$now = Carbon::now();
				$LoginRequestEvent->email_otp_start_at=$now;
				$LoginRequestEvent->save();
			}	
			
		}
		else{
			
			$phoneOtpPast = Carbon::parse($LoginRequestEvent->phone_otp_start_at)->diffInSeconds($now);
			$PhoneTimeRemains=($timeLimit - $phoneOtpPast);
			
			//Phone Time Remaining Error Message
			if($phoneOtpPast<=$timeLimit){
				$returnArray=array_merge($returnArray, [
					'phone_time_remain'=> $PhoneTimeRemains,
					'phone_resend'=>false,
					'phone_message'=>'Please try again after '.$PhoneTimeRemains. ' seconds'
						]);
			}
			
			if( !empty($returnArray) ){
				return response()->json([
					'error'=> $returnArray
						],403);
			}
			else{
				$returnArray=array();
			}
			
			if($phoneOtpPast>$timeLimit){
				$LoginRequestEvent->phone_otp=rand(1000,9999);
				send_otp_phone($LoginRequestEvent->phone_code . $LoginRequestEvent->phone, $LoginRequestEvent->phone_otp);
				
				$now = Carbon::now();
				$LoginRequestEvent->phone_otp_start_at=$now;
				$LoginRequestEvent->save();
			}
		
		}
					
		return response()->json([
						'email'=>$LoginRequestEvent->email,
						'email_resend'=>$testForEmail,
						'phone'=>$LoginRequestEvent->phone,
						'phone_resend'=>!$testForEmail,
						'phone_otp_start_at'=>$LoginRequestEvent->phone_otp_start_at,
						'email_otp_start_at'=>$LoginRequestEvent->email_otp_start_at,
						], 200);
		
	}
	
	//verify otps
    public function verify_otp(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
    	$validator = Validator::make($request->all(), [
            'ticket' => 'required|string',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|min:10',
            'phone_code' => 'nullable|string|max:4',
			'phone_otp'=>'nullable|numeric|min:1000|max:9999',
			'email_otp'=>'nullable|numeric|min:1000|max:9999'
        ]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		
		$LoginRequestEvent=LoginRequest::where('ticket', $request->input('ticket'))->first();
		//ticket verification
		if(!$LoginRequestEvent){
				return response()->json(['error' => ['ticket'=> 'unauthorized']],403);
		}
		
		
		$testForEmail=true;
		if(null===$request->input('email')){
			if($request->input('phone')!='' && $request->input('phone_code') != ''){
				$testForEmail=false;
			}
			else{
				return response()->json(['error' => ['input'=>'invalid']],200);
			}
		}
		
		$returnArray=array();
		
		if($testForEmail){
			$oldEntry=User::where('email', $request->input('email'))->count();
			if($oldEntry==0){
				return response()->json(['error' => ['user'=>'unregistered']],200);
			}
			$newUser=User::where('email', $request->input('email'))->first();
			if($request->input('email')!=$LoginRequestEvent->email){
				$returnArray=array_merge($returnArray, [
					'email'=>'Invalid email'
						]);
			}
			if( !empty($returnArray) ){
				return response()->json([
					'error'=> $returnArray
						],403);
			}
			//otp verification
			if($request->input('email_otp')!=$LoginRequestEvent->email_otp){
				$returnArray=array_merge($returnArray, [
					'email_otp'=>'Invalid email OTP'
						]);
			}
			if( !empty($returnArray) ){
				return response()->json([
					'error'=> $returnArray
						],403);
			}
		
		} else {
			$oldEntry=User::where('phone', $request->input('phone'))->where('phone_code', $request->input('phone_code'))->count();
			if($oldEntry==0){
				return response()->json(['error' => ['user'=>'unregistered']],200);
			}
			$newUser=User::where('phone', $request->input('phone'))->where('phone_code', $request->input('phone_code'))->first();
			if($request->input('phone')!=$LoginRequestEvent->phone || $request->input('phone_code')!=$LoginRequestEvent->phone_code){
				$returnArray=array_merge($returnArray, [
					'phone'=>'Invalid phone'
						]);
			}
			if( !empty($returnArray) ){
				return response()->json([
					'error'=> $returnArray
						],403);
			}
			//otp verification
			if($request->input('phone_otp')!=$LoginRequestEvent->phone_otp){
				$returnArray=array_merge($returnArray, [
					'phone_otp'=>'Invalid phone OTP'
						]);
			}
			if( !empty($returnArray) ){
				return response()->json([
					'error'=> $returnArray
						],403);
			}
			
		}
		
		//check time
		$now = Carbon::now();
				
		$token=Str::random(64) ;
		while (UserSession::where('token', $token)->count()>0) {
			$token=Str::random(64) ;
		}
		$newSession = UserSession::create([
					'token'=>$token,
					'user_id'=>$newUser->id,
					]);
		
		$LoginRequestEvent->delete();
		
		return response()->json([
						'email'=>$newUser->email,
						'phone'=>$newUser->phone,
						'last_used_at'=>$now,
						'token'=>$token
						], 200);
		
	}
	
	//return otp for testing REMEMBER TO DELETE
    public function return_otp(Request $request){
		$ticket=$request->ticket;
		$LoginRequestEvent=LoginRequest::where('ticket', $ticket)->first();
		if($LoginRequestEvent){
			return response()->json(['phone_otp' => $LoginRequestEvent->phone_otp,'email_otp' => $LoginRequestEvent->email_otp],200);
		}
		else{
			return response()->json(['error'=>['ticket' => 'unauthorized']],403);
		}
	}
	
	public function send_sms_test($phone_code, $phone){
				$phone_otp=rand(1000,9999);
				//send_otp_phone($phone_code . $phone, $phone_otp);
				send_message($phone_code . $phone, json_encode(['phone_code' => $phone_code,
			                                    'phone'=>$phone,
			                                    'otp'=>$phone_otp
			                                    ]));
			return response()->json(['error'=>['phone_code' => $phone_code,
			                                    'phone'=>$phone,
			                                    'otp'=>$phone_otp
			                                    ]],200);
	}
	
	
	public function send_email_test($email){
				$phone_otp=rand(1000,9999);
				//send_otp_phone($phone_code . $phone, $phone_otp);
				send_otp_email($email, $phone_otp);
				
				
			return response()->json(['error'=>['email' => $email,
			                                    'otp'=>$phone_otp
			                                    ]],200);
	}
		
}
