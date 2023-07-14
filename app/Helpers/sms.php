<?php 


if (! function_exists('send_otp_email')) {
    function send_otp_email($email, $otp) {
		$success=true;
		app('App\Http\Controllers\Email')->otp_email($email, $otp);
		if($success){
			return true;
		}
		else{
			return 'error';
		}
    }
}

if (! function_exists('send_otp_phone')) {
    function send_otp_phone($phone, $otp) {
		$success=true;

		send_message($phone,
					'Your Account Verification OTP is '. $otp .'. - OPA Team');
	
		if($success){
			return true;
		}
		else{
			return 'error';
		}
    }
}

/**
 * Sends sms to user using Twilio's programmable sms client
 * @param String $message Body of sms
 * @param Number $recipients string or array of phone number of recepient
 */
if( !function_exists('send_message')){
	function send_message($recipients, $message){
		app('App\Http\Controllers\SMS')->send_message($message, $recipients);
    }
}