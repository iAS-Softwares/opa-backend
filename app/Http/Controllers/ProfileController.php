<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Profile;
use App\Models\PostBattle;
use App\Models\Follow;
use App\Models\User;
use App\Models\Newsletter;
use App\Models\Image;
use App\Models\UserSession;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    
    public function get_user(Request $request)
    {
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'user_id' => 'required|integer|min:-1'
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		$userId = ($userSession)?$userSession->id:0;
		if(!$userSession && $request->input('token')!='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		$askedProfile = Profile::where('user_id', $request->input('user_id'))->first();
		if($request->input('user_id')==-1 && $request->input('token')!='GUEST'){
			$askedProfile = Profile::firstOrNew(array('user_id' => $userSession->user_id));
		}
		if(!$askedProfile){
			return response()->json(['error' => ['user_id' => 'invalid']],403);
		}
		$askedUser = User::where('id', $askedProfile->user_id)->first();
		$upload_count=PostBattle::where('user_id', $askedProfile->user_id)->where('annonymous', false)->count();
		$followers_count=Follow::where('following_id', $askedProfile->user_id)->count();
		$following_count=Follow::where('follower_id', $askedProfile->user_id)->count();
		$followed = Follow::where('following_id', $askedProfile->user_id)
							->where('follower_id', ($userSession?$userSession->user_id:''))->count();
		if($userSession && $userSession->user_id==$askedProfile->user_id){
		    $isSubscribed=Newsletter::where('email', ($askedUser?$askedUser->email:null))->first();
			return response()->json(['user' => ['name'=>$askedProfile->name,
												'location'=>$askedProfile->country,
												'location_name'=>codeToCountry($askedProfile->country),
												'birth_year'=>$askedProfile->birth_year,
												'gender'=>$askedProfile->sex,
												'email'=>($askedUser?$askedUser->email:null),
												'phone'=>($askedUser?$askedUser->phone:null),
												'phone_code'=>($askedUser?$askedUser->phone_code:null),
												'profile_image'=>($askedProfile->image_id?route('images.show', $askedProfile->image_id):asset('assets/profile/default/image.png')),
												'upload_count'=>$upload_count,
												'followers_count'=>$followers_count,
												'following_count'=>$following_count,
												'is_followed'=>(bool)$followed,
												'is_owner'=>true,
												'is_subscribed'=>(bool)(is_null($isSubscribed)?false:$isSubscribed->isSubscribed),
												'a'=>is_null($isSubscribed),
												]],200);
		}
		if($userSession || $request->input('token')=='GUEST'){
			return response()->json(['user' => ['name'=>$askedProfile->name,
												'location'=>$askedProfile->country,
												'location_name'=>codeToCountry($askedProfile->country),
												'profile_image'=>($askedProfile->image_id?route('images.show', $askedProfile->image_id):asset('assets/profile/default/image.png')),
												'upload_count'=>$upload_count,
												'followers_count'=>$followers_count,
												'following_count'=>$following_count,
												'is_followed'=>(bool)$followed,
												'is_owner'=>false
												]],200);
		}
	}
	
	
	//update
	
    public function update_user(Request $request)
    {
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'name' => 'nullable|string',
			'country' => 'required|string',
			'birth_year' => 'required|integer|min:0|max:'.(date("Y")+1),
			'sex' => 'nullable|string|in:man,woman,non-binary,na',
			'user_image'=> 'nullable|image|mimes:jpeg,png,jpg|max:6000',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		$userId = ($userSession)?$userSession->user_id:0;
		if(!$userSession && $request->input('token')!='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
				
		if(!$userSession){
			return response()->json(['error' => ['user_id' => 'unauthorized']],403);
		}
		
		if($userSession){
			
			$userProfile = Profile::firstOrNew(array('user_id' => $userSession->user_id));
			
			if(!$userProfile->username){
				$username=Str::random(32) ;
				while (Profile::where('username', $request->input('username'))->count()>0) {
					$username=Str::random(32) ;
				}
			}
			else{
				$username =$userProfile->username;
			}
			$userProfile->name = $request->input('name');
			$userProfile->username = $username;
			$userProfile->country = $request->input('country');
			$userProfile->birth_year = $request->input('birth_year');
			$userProfile->sex = $request->input('sex');
			
			
			if($request->file('user_image')){
				$file = $request->file('user_image') ;
				$image1Filename = '1' . time() . str_replace(' ', '', $file->getClientOriginalName() );
				$destinationPath = storage_path().'/profile/image/'.$userSession->user_id .'/';
				$file->move($destinationPath, $image1Filename);
				$image1=Image::makeEntry($image1Filename, '\\profile\\image\\'. $userSession->user_id .'\\', $userSession->user_id);
				
				$userProfile->image_id = $image1->id;
			}
			
			$userProfile->save();
			
			
			$upload_count=PostBattle::where('user_id', $userProfile->user_id)->count();
			$followers_count=Follow::where('following_id', $userProfile->user_id)->count();
			$following_count=Follow::where('follower_id', $userProfile->user_id)->count();
			$followed = Follow::where('following_id', $userProfile->user_id)
							->where('follower_id', $userSession->user_id)->count();
			
			return response()->json(['user' => ['name'=>$userProfile->name,
												'location'=>$userProfile->country,
												'birth_year'=>$userProfile->birth_year,
												'gender'=>$userProfile->sex,
												'profile_image'=>isset($userProfile->image_id)?route('images.show', $userProfile->image_id):asset('assets/profile/default/image.png'),
												'upload_count'=>$upload_count,
												'followers_count'=>$followers_count,
												'following_count'=>$following_count,
												'is_followed'=>(bool)$followed
												]],200);
		}
	}
	
	
    public function create_user(Request $request)
    {
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'name' => 'nullable|string',
			'username' => 'nullable|string',
			'country' => 'required|string',
			'birth_year' => 'required|integer|min:0|max:'.(date("Y")+1),
			'sex' => 'nullable|string|in:man,woman,non-binary,na',
			'user_image'=> 'nullable|image|mimes:jpeg,png,jpg|max:6000',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		$userId = ($userSession)?$userSession->user_id:0;
		if(!$userSession && $request->input('token')!='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
				
		if(!$userSession){
			return response()->json(['error' => ['user_id' => 'unauthorized']],403);
		}
		
		if($userSession){
			
			$userProfile = Profile::firstOrNew(array('user_id' => $userSession->user_id));
			
			if(!$userProfile->username){
				$username=Str::random(32) ;
				while (Profile::where('username', $request->input('username'))->count()>0) {
					$username=Str::random(32) ;
				}
			}
			else{
				$username =$userProfile->username;
				if (Profile::where('username', $request->input('username'))->count()>0) {
					return response()->json(['error' => ['username' => 'duplicate']],403);
				}
			}
			$userProfile->name = $request->input('name');
			$userProfile->username = $username;
			$userProfile->country = $request->input('country');
			$userProfile->birth_year = $request->input('birth_year');
			$userProfile->sex = $request->input('sex');
			
			
			if($request->file('user_image')){
				$file = $request->file('user_image') ;
				$image1Filename = '1' . time() . str_replace(' ', '', $file->getClientOriginalName() );
				$destinationPath = storage_path().'/profile/image/'.$userSession->user_id .'/';
				$file->move($destinationPath, $image1Filename);
				$image1=Image::makeEntry($image1Filename, '\\profile\\image\\'. $userSession->user_id .'\\', $userSession->user_id);
				
				$userProfile->image_id = $image1->id;
			}
			
			$userProfile->save();
			
			
			$upload_count=PostBattle::where('user_id', $userProfile->user_id)->count();
			$followers_count=Follow::where('following_id', $userProfile->user_id)->count();
			$following_count=Follow::where('follower_id', $userProfile->user_id)->count();
			$followed = Follow::where('following_id', $userProfile->user_id)
							->where('follower_id', $userSession->user_id)->count();
			
			return response()->json(['user' => ['name'=>$userProfile->name,
												'location'=>$userProfile->country,
												'birth_year'=>$userProfile->birth_year,
												'gender'=>$userProfile->sex,
												'profile_image'=>isset($userProfile->image_id)?route('images.show', $userProfile->image_id):asset('assets/profile/default/image.png'),
												'upload_count'=>$upload_count,
												'followers_count'=>$followers_count,
												'following_count'=>$following_count,
												'is_followed'=>(bool)$followed
												]],200);
		}
	}
}
