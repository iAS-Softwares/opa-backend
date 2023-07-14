<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class Email extends Controller
{
    function otp_email($email, $otp) {
		$mailData=array('data'=>array(
            'email' => $email,
            'otp' => $otp
        ));
		Mail::send(['html'=>'emails.otp_email'], $mailData, function($message)use($mailData) {
         $message->to($mailData['data']['email'], 'Avinash Sinha');
		 $message->subject('OTP for account verification - OPA' );
         $message->from('support@slamyo.com','OPA no-reply');
		});
	}
    function subscription_email($email) {
		$mailData=array('data'=>array(
            'email' => $email
        ));
		Mail::send(['html'=>'emails.subscription_alert'], $mailData, function($message)use($mailData) {
         $message->to('admin@the-opa.com', 'Avinash Sinha');
		 $message->subject('Email subscription Alert' );
         $message->from('support@slamyo.com','OPA no-reply');
		});
	}
}
