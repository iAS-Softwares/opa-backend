<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Notification;
use App\Models\UserSession;
use App\Models\Profile;
use App\Models\Follow;
use App\Models\Brand;
use App\Models\PostSingle;
use App\Models\Tag;


class FollowController extends Controller
{
    public function follow_user(Request $request)	{
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'user_id' => 'required|numeric|min:0',
			'follow' => 'required|boolean'
		]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$requestedUser = User::where('id', $request->input('user_id'))->first();		
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		if(!$userSession || $request->input('token')=='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		if(!$requestedUser){
			return response()->json(['error' => ['user_id' => 'invalid']],403);
		}
		
		$follow = Follow::where('follower_id', $userSession->user_id)
		                    ->where('following_id', $requestedUser->id)->first();
		if($follow && $request->input('follow')==false){
		    $follow->delete();
		}
		if(!$follow && $request->input('follow')==true){
			$followNew = Follow::firstOrNew(array('follower_id' => $userSession->user_id, 'following_id'=>$requestedUser->id));
			$followNew->save();
			
			/* Notification */
			$userProfile=Profile::where('user_id', $userSession->user_id)->first();
            $user = User::where('id', $requestedUser->id)->first();
			sendExpoPushNotification($user->notifier_id, 'You have a new follower!', $userProfile->name.' has become your follower.');
			$notification = Notification::firstOrNew(array(
							'user_id' => $requestedUser->id, 
							'description'=>$userProfile->name.' has become your follower.'));
			$notification->save();
			
				
		}
		    return response()->json([ 'user_id' => $requestedUser->id,
									'follow'=>$request->input('follow'), ],200);
	}
	
	
	
    
	public function followers_following(Request $request)
	{
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:1000',
			'type' => 'required|in:followers,following',
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/10))+1;
		}
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		if(!$userSession || $request->input('token')=='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		if($request->input('type')=='followers'){
		    $searchedItems = Follow::where('following_id', $userSession->user_id)->paginate(10, ['*'], 'page', $start);
		}
		elseif($request->input('type')=='following'){
		    $searchedItems = Follow::where('follower_id', $userSession->user_id)->paginate(10, ['*'], 'page', $start);
		}
		
		
		$output=array();
		
		foreach($searchedItems->getCollection() as $searchItem) {
		    
		$followed = Follow::where('following_id', ($searchItem?$searchItem->following_id:-1))
		                    ->where('follower_id', ($userSession?$userSession->user_id:''))->count();
		if($request->input('type')=='following'){
		    $profile = Profile::where('user_id', $searchItem->following_id)->first();
		}
		elseif($request->input('type')=='followers'){
		    $profile = Profile::where('user_id', $searchItem->follower_id)->first();
		}
		if($profile && $profile->user_id!=$userSession->user_id){
		    $output=array_merge($output, [[ 'item' => [
					'id'=>$profile->user_id,
					'image_url'=>((int)$profile->image_id?route('images.show', (int)$profile->image_id):asset('assets/profile/annonymous/image.png')),
					'name'=>$profile->name,
					'is_followed'=>$followed,
					]]]);
		}
		}
		
		return response()->json([ 'result' => $output,
									'type'=>$request->input('type'),
									'is_end'=>$searchedItems->onLastPage()],200);
	}
	
}
