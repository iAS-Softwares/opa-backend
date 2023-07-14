<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// for send message function
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;


class SMS extends Controller
{
    
/**
 * Sends sms to user using Twilio's programmable sms client
 * @param String $message Body of sms
 * @param Number $recipients string or array of phone number of recepient
 */
function send_message($message, $recipients){
  //$recipients='+918507540096';
    try {
  
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
  
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, [
            'from' => $twilio_number, 
            'body' => $message]);
  
        return true;
  
    } catch (Exception $e) {
		return false;
        //dd("Error: ". $e->getMessage());
    }
}


}
