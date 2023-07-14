<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserSession;
use App\Models\SignUp;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class SignUpController extends Controller
{
	// Returns json with sign up links
    public function get_form(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
		$ticket=Str::random(32) ;
		while (SignUp::where('ticket', $ticket)->count()>0) {
			$ticket=Str::random(32) ;
		}
		
		$newSignUpEventArray=array(	'ticket'=>$ticket );
		$signUpEvent = SignUp::create($newSignUpEventArray);
		
		
		//filter ips
		if( true ) {
		$links = array(
					'google' => route('auth.redirectToGoogle', ['ticket'=>$ticket, 'event'=>'signup']),
					'facebook' => route('auth.redirectToFacebook', ['ticket'=>$ticket, 'event'=>'signup']),
					'apple' => route('auth.redirectToApple', ['ticket'=>$ticket, 'event'=>'signup']));
		
		$token = 'authorized';
		$status = 200;			
		}
		
		return response()->json(['token' => $token,'ticket'=> $signUpEvent->ticket,
									'links'=>$links],$status);
	}
	
	//send otps to email and phone
    public function submit_form(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
    	$validator = Validator::make($request->all(), [
            'ticket' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string|min:5',
            'phone_code' => 'nullable|string|max:6',
        ]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
				
		//checking if entry already exists
		$oldEntry=User::where('email', $request->input('email'))->count();
		$oldEntry=$oldEntry+User::where('phone', $request->input('phone'))
		                            ->whereNotNull('phone')
		                            ->where('phone_code', $request->input('phone_code'))
		                            ->whereNotNull('phone_code')->count();
		if($oldEntry>0){
			return response()->json(['error' => ['user'=>'Email or Phone is already been used.']],200);
		}
		
		//removing old signup events
		$deleted = SignUp::where('email', $request->input('email'))->where('ticket', '!=' , $request->input('ticket'))->delete();
		$deleted = SignUp::where('phone', $request->input('phone'))->where('phone_code', $request->input('phone_code'))->where('ticket', '!=' , $request->input('ticket'))->delete();
		
		$repeatCheck = SignUp::where('ticket', $request->input('ticket'))->count();
		$repeatCheck1 = SignUp::where('email', '!=', $request->input('email'))->where('ticket', $request->input('ticket'))->count();
		$repeatCheck2 = SignUp::where('phone', '!=', $request->input('phone'))->where('phone_code', $request->input('phone_code'))->where('ticket', $request->input('ticket'))->count();
		$repeatCheck2 = $repeatCheck2 + SignUp::where('phone', $request->input('phone'))->where('phone_code', '!=', $request->input('phone_code'))->where('ticket', $request->input('ticket'))->count();

		if(($repeatCheck>1)||(
			$repeatCheck1>0||$repeatCheck2>0)){
			return response()->json(['error' => ['reuse'=>'Ticket has already been used.']],200);
		}
		
		//check time
		$now = Carbon::now();
		$newSignUpEventArray=array(
						'email'=>$request->input('email'),
						'email_otp'=>rand(1000,9999),
						'email_otp_count'=>1,
						'email_otp_start_at'=>$now
						);
		if($request->input('phone')!=''){
		$newSignUpEventArray=array_merge($newSignUpEventArray, array(
						'phone'=>$request->input('phone'),
						'phone_code'=>$request->input('phone_code'),
						'phone_otp_count'=>1,
						'phone_otp_start_at'=>$now,
						'phone_otp'=>rand(1000,9999),
						));
		}
		$updateStatus = SignUp::where('ticket', $request->input('ticket'))->update($newSignUpEventArray);
		if(!$updateStatus){
			return response()->json(['error' => ['reuse'=>'Ticket has already been used.']],200);
		}
		$signUpEvent=SignUp::where('ticket', $request->input('ticket'))->first();
		$ticket = $signUpEvent->ticket;
		
		//send otp function for email
		send_otp_email($signUpEvent->email, $signUpEvent->email_otp);
		
		//send otp function for phone
		send_otp_phone($signUpEvent->phone_code . $signUpEvent->phone, $signUpEvent->phone_otp);
				
		$token = 'authorized';
		$status = 200;
		
		return response()->json(['ticket' => $ticket,
						'email'=>$signUpEvent->email,
						'phone'=>$signUpEvent->phone,
						'phone_code'=>$signUpEvent->phone_code,
						'phone_otp_start_at'=>$signUpEvent->phone_otp_start_at,
						'email_otp_start_at'=>$signUpEvent->email_otp_start_at,
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
            'phone' => 'nullable|string|min:5',
            'phone_code' => 'nullable|string|max:6',
        ]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$timeLimit=120;
		$returnArray=array();
		
		$signUpEvent=SignUp::where('ticket', $request->input('ticket'))->first();
		//ticket verification
		if(!$signUpEvent){
				return response()->json(['error' => ['ticket'=> 'unauthorized']],403);
		}
		
		//email and phone verification
		if($request->input('email')!=''&&$request->input('email')!=$signUpEvent->email){
			$returnArray=array_merge($returnArray, [
				'email'=>'Invalid email'
					]);
		}
		if($request->input('phone')!=''&&$request->input('phone')!=$signUpEvent->phone){
			$returnArray=array_merge($returnArray, [
				'phone'=>'Invalid phone'
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
		
		//check time
		$now = Carbon::now();
		
		//$created_at = Carbon::parse($calls['created_at']);
		$emailOtpPast = Carbon::parse($signUpEvent->email_otp_start_at)->diffInSeconds($now); 
		$phoneOtpPast = Carbon::parse($signUpEvent->phone_otp_start_at)->diffInSeconds($now);
		
		$EmailTimeRemains=($timeLimit - $emailOtpPast);
		$PhoneTimeRemains=($timeLimit - $phoneOtpPast);
		
		$askEmail=($request->input('email')==$signUpEvent->email);
		$askPhone=($request->input('phone')!='')&&($request->input('phone')==$signUpEvent->phone)&&($request->input('phone_code')==$signUpEvent->phone_code);
		
			//return response()->json([$phoneOtpPast,$timeLimit,($phoneOtpPast<=$timeLimit)]);
			
		//Phone Time Remaining Error Message
		if($askPhone && ($phoneOtpPast<=$timeLimit)){
			$returnArray=array_merge($returnArray, [
				'phone_time_remain'=> $PhoneTimeRemains,
				'phone_resend'=>false,
				'phone_message'=>'Please try again after '.$PhoneTimeRemains. ' seconds'
					]);
		}
		//Email Time Remaining Error Message
		if($askEmail && ($emailOtpPast<=$timeLimit)){
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
		
		
		if($askEmail&&($emailOtpPast>$timeLimit)){
			$signUpEvent->email_otp=rand(1000,9999);
			send_otp_email($signUpEvent->email, $signUpEvent->email_otp);
			
			$now = Carbon::now();
			$signUpEvent->email_otp_start_at=$now;
			$signUpEvent->save();
		}
		
		if($askPhone&&($phoneOtpPast>$timeLimit)){
			$signUpEvent->phone_otp=rand(1000,9999);
			send_otp_phone($signUpEvent->phone_code . $signUpEvent->phone, $signUpEvent->phone_otp);
			
			$now = Carbon::now();
			$signUpEvent->phone_otp_start_at=$now;
			$signUpEvent->save();
		}
		
		return response()->json([
						'email'=>$signUpEvent->email,
						'email_resend'=>$askEmail,
						'phone'=>$signUpEvent->phone,
						'phone_resend'=>$askPhone,
						'phone_otp_start_at'=>$signUpEvent->phone_otp_start_at,
						'email_otp_start_at'=>$signUpEvent->email_otp_start_at,
						], 200);
		
	}
	
	//verify otps
    public function verify_otp(Request $request){
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
		
    	$validator = Validator::make($request->all(), [
            'ticket' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string|min:5',
            'phone_code' => 'nullable|string|max:6',
			'phone_otp'=>'nullable|numeric|min:1000|max:9999',
			'email_otp'=>'required|numeric|min:1000|max:9999'
        ]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$returnArray=array();
		
		$signUpEvent=SignUp::where('ticket', $request->input('ticket'))->first();
		//ticket verification
		if(!$signUpEvent){
				return response()->json(['error' => ['ticket'=> 'unauthorized']],403);
		}
		
		//email and phone verification
		if($request->input('email')!=$signUpEvent->email){
			$returnArray=array_merge($returnArray, [
				'email'=>'Invalid email'
					]);
		}
		if($request->input('phone')!=$signUpEvent->phone || $request->input('phone_code')!=$signUpEvent->phone_code){
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
		if($request->input('email_otp')!=$signUpEvent->email_otp){
			$returnArray=array_merge($returnArray, [
				'email_otp'=>'Invalid email OTP'
					]);
		}
		if($request->input('phone_otp')!=$signUpEvent->phone_otp){
			$returnArray=array_merge($returnArray, [
				'phone_otp'=>'Invalid phone OTP'
					]);
		}
		if( ($request->input('email_otp')!=$signUpEvent->email_otp) 
			|| ($request->input('phone_otp')!=$signUpEvent->phone_otp) ){
			return response()->json([
				'error'=> $returnArray
					],403);
		}
		else{
			$returnArray=array();
		}
		
		
		//check time
		$now = Carbon::now();
		
		if($request->input('phone')!=''){
			$newUser = User::create([
						'email'=>$request->input('email'),
						'phone'=>$request->input('phone'),
						'phone_code'=>$request->input('phone_code'),
						'phone_verified_at'=>$now,
						'email_verified_at'=>$now,
						'access_level'=>'unverified'
						]);
		}
		else{
			$newUser = User::create([
						'email'=>$request->input('email'),
						'email_verified_at'=>$now,
						'access_level'=>'unverified'
						]);
		}
		
		$token=Str::random(64) ;
		while (UserSession::where('token', $token)->count()>0) {
			$token=Str::random(64) ;
		}
		$newSession = UserSession::create([
					'token'=>$token,
					'user_id'=>$newUser->id,
					]);
		
		$signUpEvent->delete();
		
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
		$signUpEvent=SignUp::where('ticket', $ticket)->first();
		if($signUpEvent){
			return response()->json(['phone_otp' => $signUpEvent->phone_otp,'email_otp' => $signUpEvent->email_otp],200);
		}
		else{
			return response()->json(['error'=>['ticket' => 'unauthorized']],403);
		}
	}
}
