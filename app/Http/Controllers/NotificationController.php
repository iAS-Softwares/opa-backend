<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;

use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use NotificationChannels\ExpoPushNotifications\Facades\Expo;

use Validator;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserSession;


class NotificationController extends Controller
{
    
    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {
        return (new ExpoMessage())
            ->to([$notifiable->expoTokens->value])
            ->title('A beautiful title')
            ->body('This is a content')
            ->channelId('default');
    }

    
    public function run_test($notifiable)
    {
		$userSession = UserSession::where('token', $notifiable)->first();
        if($userSession){
        $user= User::where('id', $userSession->user_id)->first();
            $notifiable=$user->notifier_id;
        }
// Usage example
$expoPushToken = $notifiable;
$title = 'Hello';
$body = 'This is a test notification from PHP';

sendExpoPushNotification($expoPushToken, $title, $body);


return response()->json($expoPushToken);

    }
    
    public function update_id(Request $request) {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'id' => 'required|string|max:50',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		$userId = ($userSession)?$userSession->user_id:0;
		
		$user = User::where('id', $userId)->first();
		$user->notifier_id = $request->input('id');
		$user->save();
		
		
			return response()->json(['update' => ['notification_id' => 'success']],500);
		
    }
    
    public function get_last(Request $request){
    
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:100'
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/20))+1;
		}
		
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$notificationSet = Notification::where('user_id', $userSession->user_id)->orderBy('created_at', 'desc')->paginate(20, ['*'], 'page', $start);
		//return $notificationSet->getCollection();
		$output=array();
		foreach($notificationSet->getCollection() as $notification) {
		    
			$output=array_merge($output, [[ 'notification' => [
			    'id'=>$notification["id"],
			    'code'=>$notification["code"],
			    'description'=>$notification["description"],
			    'dismissed'=>$notification["dismissed"],
				]]]);
		}
		
		return response()->json([ 'notifications' => $output,
									'start' => $start,
									'is_end'=>$notificationSet->onLastPage()],200);    
    }
    
    
    public function delete_notif(Request $request){
    
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'id' => 'nullable|integer|min:1'
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/20))+1;
		}
		
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$notification = Notification::where('user_id', $userSession->user_id)->where('id', $request->input('id'))->first();
		//return $notificationSet->getCollection();
		if($notification){
		    $notification->delete();
		}
		
		return response()->json([ 'delete' => 'success'],200);    
    }
    
    
    
    
    public function delete_all_notif(Request $request){
    
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'id' => 'nullable|integer|min:1'
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/20))+1;
		}
		
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$notification = Notification::where('user_id', $userSession->user_id)->delete();
		//return $notificationSet->getCollection();

		
		return response()->json([ 'delete' => 'success'],200);    
    }
}