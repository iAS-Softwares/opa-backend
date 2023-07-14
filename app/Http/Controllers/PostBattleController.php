<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostBattleRequest;
use Illuminate\Http\Request;
use App\Http\Requests\UpdatePostBattleRequest;
use Validator;
use App\Models\User;
use App\Models\Wardrobe;
use App\Models\Notification;
use App\Models\Profile;
use App\Models\Follow;
use App\Models\Like;
use App\Models\DemographicLike;
use App\Models\Image;
use App\Models\UserSession;
use App\Models\PostSingle;
use App\Models\PostBattle;

class PostBattleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postBattles = PostBattle::all();
		return json_encode($postBattles);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePostBattleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'image1' => 'required|image|mimes:jpeg,png,jpg|max:6000',
			'brands1' => 'nullable|json',
			'caption1' => 'nullable|string|max:160',
			'tags1' => 'nullable|json',
			'image2' => 'required|image|mimes:jpeg,png,jpg|max:6000',
			'brands2' => 'nullable|json',
			'caption2' => 'nullable|string|max:160',
			'tags2' => 'nullable|json',
			'visiblity' => 'nullable|boolean',
		]);
		
		
		if(null === ($request->input('visiblity'))){
				$visiblity=true;
		}
		else{
			$visiblity=$request->input('visiblity');
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		$userId = ($userSession)?$userSession->user_id:0;
		
		if(!$userSession && $request->input('token')!='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		//Handling Post 1
				
		//Image1
            $file = $request->file('image1') ;
            $image1Filename = '1' . time() . str_replace(' ', '', $file->getClientOriginalName() );
            $destinationPath = storage_path().'/post/singles/'.$userId .'/';
            $file->move($destinationPath, $image1Filename);
			$image1=Image::makeEntry($image1Filename, '\\post\\singles\\'.$userId .'\\', $userId);
		//Post1
			$post1 = PostSingle::create([
				'user_id'=>$userId,
				'image_id' => $image1->id,
				'brands'=>json_encode(json_decode($request->input('brands1'))),
				'visiblity' => $visiblity,
				'computed_preference'=> json_encode([]),
				'caption' => ($request->input('caption1')?$request->input('caption1'):""),
				'tags' => json_encode(json_decode($request->input('tags1'))),
			]);
		
		//Handling Post 2
				
		//Image2
            $file = $request->file('image2') ;
            $image2Filename = '2' . (time()+1) . str_replace(' ', '', $file->getClientOriginalName() );
            $destinationPath = storage_path().'/post/singles/'.$userId .'/';
            $file->move($destinationPath, $image2Filename);
			$image2=Image::makeEntry($image2Filename, '\\post\\singles\\'.$userId .'\\', $userId);
		//Post1
			$post2 = PostSingle::create([
				'user_id'=>$userId,
				'image_id' => $image2->id,
				'brands'=>json_encode(json_decode($request->input('brands2'))),
				'visiblity' => $visiblity,
				'computed_preference'=> json_encode([]),
				'caption' => ($request->input('caption2')?$request->input('caption2'):""),
				'tags' => json_encode(json_decode($request->input('tags2'))),
			]);
			
		//Handling battle
			$battle = PostBattle::create([
				'user_id'=>$userId,
				'post_id1' => $post1->id,
				'post_id2' => $post2->id,
				'brands'=>json_encode(array('brands1'=>	json_decode($request->input('brands1')),
									        'brands2'=>json_decode($request->input('brands2'))
										)),
				'visiblity' => $visiblity,
				'computed_preference'=> json_encode([]),
				'caption' => $request->input('caption1') . ', ' . $request->input('caption2'),
				'tags' => json_encode(array_merge(
									json_decode($request->input('tags1'))->tags,
									json_decode($request->input('tags2'))->tags
										))
			]);
			
			
			
			/* Notification */
            $user = User::where('id', $userId)->first();
			sendExpoPushNotification($user->notifier_id, 'Post successful!', 'Your battle is live.');
			$notification = Notification::firstOrNew(array(
							'user_id' => $user->id, 
							'description'=>'Your battle is live.'));
			$notification->save();
			
		
		return response()->json([
						'status'=>'success',
						'battle_id'=>$battle->id,
						], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PostBattle  $postBattle
     * @return \Illuminate\Http\Response
     */
    public function show(PostBattle $postBattle)
    {
        
		$output='<div style="display:flex;">';
		$ps = PostSingle::where('id', $postBattle->post_id1)->first();
			$output.='<div style="width:50%">';
			$output.='<h2>'.$ps->caption.'</h2>';
			$output.='<img src="'.route('images.show', $ps->image_id).'">';
			$output.='<h4>'.$ps->brands.'</h4>';
			$output.='<h4>'.$ps->tags.'</h4>';
			$output.='</div>';
		
		$ps = PostSingle::where('id', $postBattle->post_id2)->first();
			$output.='<div style="width:50%">';
			$output.='<h2>'.$ps->caption.'</h2>';
			$output.='<img src="'.route('images.show', $ps->image_id).'">';
			$output.='<h4>'.json_encode(json_decode($ps->brands)).'</h4>';
			$output.='<h4>'.$ps->tags.'</h4>';
			$output.='</div>';
		
		$output.='</div>';
		
		return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PostBattle  $postBattle
     * @return \Illuminate\Http\Response
     */
    public function edit(PostBattle $postBattle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostBattleRequest  $request
     * @param  \App\Models\PostBattle  $postBattle
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostBattleRequest $request, PostBattle $postBattle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PostBattle  $postBattle
     * @return \Illuminate\Http\Response
     */
    public function destroy(PostBattle $postBattle)
    {
        //
    }
	
	public function like(Request $request)
	{
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'battle_id' => 'required|numeric|min:0',
			'like' => 'required|numeric|min:1|max:2'
		]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$postBattle = PostBattle::where('id', $request->input('battle_id'))->first();		
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		$userProfile=Profile::where('user_id', $userSession->user_id)->first();
		
		if(!$userSession || $request->input('token')=='GUEST'){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
			$postBattle->total_count=$postBattle->total_count+1;
		$postLike1 = Like::where('article_id', $postBattle->post_id1)->where('user_id', $userSession->user_id)->first();
		$postLike2 = Like::where('article_id', $postBattle->post_id2)->where('user_id', $userSession->user_id)->first();
		
		if($postLike2 && $request->input('like') == 1){
		    //disable choice change
		    /*
			$postLike2->delete();
			$postLike1 = Like::firstOrNew(array('user_id' => $userSession->user_id, 'article_id'=>$postBattle->post_id1, 'article_category'=>'postBattle'));
			$post1 = PostSingle::where('id', $postBattle->post_id1)->first();
			$post1->count=$post1->count+1;
			$post1->save();
			$post2 = PostSingle::where('id', $postBattle->post_id2)->first();
			$post2->count=$post2->count-1;
			$post2->save();
			
			$postLike1->save();
			*/
		}
		elseif($postLike1 && $request->input('like') == 2){
		    //disable choice change
		    /*
			$postLike1->delete();
			$postLike2 = Like::firstOrNew(array('user_id' => $userSession->user_id, 'article_id'=>$postBattle->post_id2, 'article_category'=>'postBattle'));
			$post2 = PostSingle::where('id', $postBattle->post_id2)->first();
			$post2->count=$post2->count+1;
			$post2->save();
			$post1 = PostSingle::where('id', $postBattle->post_id1)->first();
			$post1->count=$post1->count-1;
			$post1->save();
			$postLike2->save();
			*/
		}
		elseif(!$postLike1 && !$postLike2  ){
			
			$postLike = Like::firstOrNew(array(
							'user_id' => $userSession->user_id, 
							'article_id'=>($request->input('like')==1?$postBattle->post_id1:$postBattle->post_id2), 
							'article_category'=>'postBattle'));
			$post = PostSingle::where('id', ($request->input('like')==1?$postBattle->post_id1:$postBattle->post_id2))->first();
			$post->count=$post->count+1;
			$post->save();
			$post->save();
			$postLike->save();
			
			$demoLike = DemographicLike::firstOrNew(array(
							'user_id' => $userSession->user_id, 
							'post_id'=>($request->input('like')==1?$postBattle->post_id1:$postBattle->post_id2), 
							'type'=>'single'));
            $demoLike->count = 	1;
            $demoLike->country = $userProfile->country;
            $demoLike->sex = $userProfile->sex;
            $demoLike->birth_year = $userProfile->birth_year;
            $demoLike->save();
            
            
			/* Notification */
            $user = User::where('id', $post->user_id)->first();
			sendExpoPushNotification($user->notifier_id, 'You have a new like!', $userProfile->name.' has voted on your battle.');
			$notification = Notification::firstOrNew(array(
							'user_id' => $post->user_id, 
							'description'=>$userProfile->name.' has voted on your battle.'));
			$notification->save();
							
		}
			
			
			
			
			$postBattle->save();	
		return response()->json([ 'battle_id' => $postBattle->id,
									'like'=>$request->input('like'), ],200);
	}
	
	public function paginated_list(Request $request)
	{
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
		
		if($userSession){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		$postBattleSet = PostBattle::orderBy('created_at', 'desc')->paginate(20, ['*'], 'page', $start);
		//return $postBattleSet->getCollection();
		$output=array();
		foreach($postBattleSet->getCollection() as $postBattle) {
		    
		$itemFound1=false;
		$itemFound2=false;
		$postOwner = Profile::where('user_id', $postBattle["user_id"])->first();
		$followed = Follow::where('following_id', ($postOwner?$postOwner->user_id:-1))
		                    ->where('follower_id', ($userSession?$userSession->user_id:''))->count();;

		$post1 = PostSingle::where('id', $postBattle["post_id1"])->first();
				if($userSession && $wardrobe){
        			$_itemFound1=in_array($postBattle["post_id1"], json_decode($wardrobe->collection));
					$itemFound1=$itemFound1||$_itemFound1;
				}
		$post2 = PostSingle::where('id', $postBattle["post_id2"])->first();
				if($userSession && $wardrobe){
        			$_itemFound2=in_array($postBattle["post_id2"], json_decode($wardrobe->collection));
					$itemFound2=$itemFound2||$_itemFound2;
				}
		$postLike1 = Like::where('article_id', $postBattle["post_id1"])
		                    ->where('user_id', ($userSession?$userSession->user_id:''))->first();
		                    
		$postTotalLikes1 = Like::where('article_id', $postBattle["post_id1"])->count();
		$postLike2 = Like::where('article_id', $postBattle["post_id2"])
		                    ->where('user_id', ($userSession?$userSession->user_id:''))->first();
		$postTotalLikes2 = Like::where('article_id', $postBattle["post_id2"])->count();
		
		$battleTotalLikes=($postTotalLikes1+$postTotalLikes2);
		$likeId=($postLike1?'left':($postLike2?'right':'none'));
			$output=array_merge($output, [[ 'post_battle' => [
			    'battle_id'=>$postBattle["id"],
				'single_1' => [ 'id'=>$postBattle["post_id1"],
				                'image_uri' => route('images.show', $post1->image_id),
								'caption' => $post1->caption,
								'total_likes'=>$postTotalLikes1,
								'in_wardrobe'=>$itemFound1,
								'brands' => json_decode($post1->brands),
								'tags' => json_decode($post1->tags)->tags],
				'single_2' => [ 'id'=>$postBattle["post_id2"],
				                'image_uri' => route('images.show', $post2->image_id),
								'caption' => $post2->caption,
								'total_likes'=>$postTotalLikes2,
								'in_wardrobe'=>$itemFound2,
								'brands' => json_decode($post2->brands),
								'tags' => json_decode($post2->tags)->tags],
				'like'		=>$likeId,
				'total_likes'=>$battleTotalLikes,
				'is_annonymous'=>$postBattle->annonymous,
				'user'		=>	($postBattle->annonymous || !$postOwner?annonymous_profile():[
					'user_id'=>($postOwner?$postOwner->user_id:null),
				    'name'=>($postOwner?$postOwner->name:null),
				    'profile_image'=>(($postOwner&&$postOwner->image_id)?route('images.show', $postOwner->image_id):asset('assets/profile/guest/image.png')),
				    'is_followed'=>(bool)$followed,
				    'is_owner'=>(($postOwner&&$postOwner->user_id)==($userSession?$userSession->user_id:null)?true:false)])
				    
				]]]);
		}
		
		return response()->json([ 'post_battles' => $output,
									'start' => $start,
									'is_end'=>$postBattleSet->onLastPage()],200);
	}
	
	
	
	public function get_single(Request $request)
	{
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:100'
		]);
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		//wardrobe check
		$itemFound1=false;
		if($userSession){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
				if($wardrobe){
        			$_itemFound1=in_array($request->input('post_id'), json_decode($wardrobe->collection));
					$itemFound1=$itemFound1||$_itemFound1;
				}
		}
		$itemFound2=false;
		if($userSession){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
				if($wardrobe){
        			$_itemFound2=in_array($request->input('post_id'), json_decode($wardrobe->collection));
					$itemFound2=$itemFound2||$_itemFound2;
				}
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		$postBattle = PostBattle::where('post_id1',$request->input('single_id'))->first();
		if(!$postBattle){
		    $postBattle = PostBattle::where('post_id2',$request->input('single_id'))->first();
		    if(!$postBattle){
		        return response()->json(['error' => ['battle_id' => 'invalid']],403);
		    }
		}
		$output=array();
		{
		$postOwner = Profile::where('user_id', $postBattle["user_id"])->first();
		$followed = Follow::where('following_id', ($postOwner?$postOwner->user_id:-1))
		                    ->where('follower_id', ($userSession?$userSession->user_id:''))->count();;

		$post1 = PostSingle::where('id', $postBattle["post_id1"])->first();
		$post2 = PostSingle::where('id', $postBattle["post_id2"])->first();
		$postLike1 = Like::where('article_id', $postBattle["post_id1"])->first();
		$postLike2 = Like::where('article_id', $postBattle["post_id2"])->first();
		$likeId=($postLike1?'left':($postLike2?'right':'none'));
		
		
		$postTotalLikes1 = Like::where('article_id', $postBattle["post_id1"]);
		$postTotalLikes2 = Like::where('article_id', $postBattle["post_id2"]);
		
		$demoLikes1= DemographicLike::where('post_id',$postBattle["post_id1"])->get();
		$demoLikes2= DemographicLike::where('post_id',$postBattle["post_id2"])->get();
		$likesAgeArray=array();
		$likesGenderArray=array();
		$likesCountriesArray=array();
		foreach($demoLikes1 as $dLikes1){
		    $dL=date('Y')-$dLikes1->birth_year;
		    //age
                $likesAgeArray["18 & under"] ??= 0.00;
		        $likesAgeArray["18-24"]??=0.00;
		        $likesAgeArray["25-34"]??=0.00;
		        $likesAgeArray["35-44"]??=0.00;
		        $likesAgeArray["45-60"]??=0.00;
		        $likesAgeArray["60 & over"]??=0.00;
		    if($dL<18){
		        $likesAgeArray["18 & under"]=$likesAgeArray["18 & under"]+1;
		    } elseif($dL>=18 && $dL<=24){
		        $likesAgeArray["18-24"]=$likesAgeArray["18-24"]+1;
		    } elseif($dL>=25 && $dL<=34){
		        $likesAgeArray["25-34"]=$likesAgeArray["25-34"]+1;
		    } elseif($dL>=35 && $dL<=44){
		        $likesAgeArray["35-44"]=$likesAgeArray["35-44"]+1;
		    } elseif($dL>=45 && $dL<=60){
		        $likesAgeArray["45-60"]=$likesAgeArray["45-60"]+1;
		    } elseif($dL>60){
		        $likesAgeArray["60 & over"]=$likesAgeArray["60 & over"]+1;
		    }
		    
		    
		    $dLc = $dLikes1->country;
		    $likesCountriesArray[$dLc] ??=0.00;
		    $likesCountriesArray[$dLc]=$likesCountriesArray[$dLc]+1;
		    
		    
		    $dLa=$dLikes1->sex;
		    //age
                $likesGenderArray["Man"] ??= 0.00;
		        $likesGenderArray["Woman"]??=0.00;
		        $likesGenderArray["Non-binary"]??=0.00;
		        $likesGenderArray["Annonymous"]??=0.00;
		    if($dLa=='man'){
		        $likesGenderArray["Man"]=$likesGenderArray["Man"]+1;
		    } elseif($dLa=='woman'){
		        $likesGenderArray["Woman"]=$likesGenderArray["Woman"]+1;
		    } elseif($dLa=='non-binary'){
		        $likesGenderArray["Non-binary"]=$likesGenderArray["Non-binary"]+1;
		    } elseif($dLa=='na'){
		        $likesGenderArray["Annonymous"]=$likesGenderArray["Annonymous"]+1;
		    }
		    
		}
		foreach($demoLikes2 as $dLikes2){
		    $dL=date('Y')-$dLikes2->birth_year;
		    //age
                $likesAgeArray["18 & under"] ??= 0.00;
		        $likesAgeArray["18-24"]??=0.00;
		        $likesAgeArray["25-34"]??=0.00;
		        $likesAgeArray["35-44"]??=0.00;
		        $likesAgeArray["45-60"]??=0.00;
		        $likesAgeArray["60 & over"]??=0.00;
		    if($dL<18){
		        $likesAgeArray["18 & under"]=$likesAgeArray["18 & under"]+1;
		    } elseif($dL>=18 && $dL<=24){
		        $likesAgeArray["18-24"]=$likesAgeArray["18-24"]+1;
		    } elseif($dL>=25 && $dL<=34){
		        $likesAgeArray["25-34"]=$likesAgeArray["25-34"]+1;
		    } elseif($dL>=35 && $dL<=44){
		        $likesAgeArray["35-44"]=$likesAgeArray["35-44"]+1;
		    } elseif($dL>=45 && $dL<=60){
		        $likesAgeArray["45-60"]=$likesAgeArray["45-60"]+1;
		    } elseif($dL>60){
		        $likesAgeArray["60 & over"]=$likesAgeArray["60 & over"]+1;
		    }
		    
		    
		    $dLc = $dLikes2->country;
		    $likesCountriesArray[$dLc] ??=0.00;
		    $likesCountriesArray[$dLc]=$likesCountriesArray[$dLc]+1;
		    
		    $dLa=$dLikes2->sex;
		    //age
                $likesGenderArray["Man"] ??= 0.00;
		        $likesGenderArray["Woman"]??=0.00;
		        $likesGenderArray["Non-binary"]??=0.00;
		        $likesGenderArray["Annonymous"]??=0.00;
		    if($dLa=='man'){
		        $likesGenderArray["Man"]=$likesGenderArray["Man"]+1;
		    } elseif($dLa=='woman'){
		        $likesGenderArray["Woman"]=$likesGenderArray["Woman"]+1;
		    } elseif($dLa=='non-binary'){
		        $likesGenderArray["Non-binary"]=$likesGenderArray["Non-binary"]+1;
		    } elseif($dLa=='na'){
		        $likesGenderArray["Annonymous"]=$likesGenderArray["Annonymous"]+1;
		    }
		}
		
		
			$output=array_merge($output, [[ 'post_battle' => [
			    'battle_id'=>$postBattle["id"],
				'single_1' => [ 'id'=>$postBattle["post_id1"],
				                'image_uri' => route('images.show', $post1->image_id),
								'caption' => $post1->caption,
								'in_wardrobe'=>$itemFound1,
								'total_likes'=>$postTotalLikes1->count(),
								'brands' => json_decode($post1->brands),
								'tags' => json_decode($post1->tags)->tags],
				'single_2' => [ 'id'=>$postBattle["post_id2"],
				                'image_uri' => route('images.show', $post2->image_id),
								'caption' => $post2->caption,
								'in_wardrobe'=>$itemFound2,
								'total_likes'=>$postTotalLikes2->count(),
								'brands' => json_decode($post2->brands),
								'tags' => json_decode($post2->tags)->tags],
				'like'		=>$likeId,
				'total_likes'=>($postTotalLikes1->count()+$postTotalLikes2->count()),
				'is_annonymous'=>$postBattle->annonymous,
				'user'		=>	($postBattle->annonymous || !$postOwner?annonymous_profile():[
					'user_id'=>($postOwner?$postOwner->user_id:null),
				    'name'=>($postOwner?$postOwner->name:null),
				    'profile_image'=>(($postOwner&&$postOwner->image_id)?route('images.show', $postOwner->image_id):asset('assets/profile/guest/image.png')),
				    'is_followed'=>(bool)$followed,
				    'is_owner'=>(($postOwner&&$postOwner->user_id)==($userSession?$userSession->user_id:null)?true:false)]),
				'activity'=>(!$postOwner?null:[
				        'like'=>json_encode([
				                                'age'=>$likesAgeArray,
				                                'country'=>$likesCountriesArray,
				                                'gender'=>$likesGenderArray
				                                ]),
				        'view'=>'{}'
				    ])
				    
				]]]);
		}
		
		return response()->json([ 'post_battle' => $output,
									'battle_id' => $request->input('battle_id') ],200);
	}
	
	
	
	
	public function delete_battle(Request $request)
	{
		
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'battle_id' => 'required|numeric|min:0',
			'delete' => 'required|numeric|min:1|max:1'
		]);
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$postBattle = PostBattle::where('id', $request->input('battle_id'))->first();		
		    if(!$postBattle){
		        return response()->json(['error' => ['battle_id' => 'invalid']],403);
		    }
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		if($userSession->user_id !== $postBattle->user_id){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if(!$request->input('delete')){
			return response()->json(['error' => ['delete' => 'not assigned']],403);
		}
		
    try {
		$postBattle->delete();
        return response()->json($postBattle);
		        // Additional code after deletion (e.g., redirect, response, etc.)
    } catch (Exception $e) {
        dd($e);
    }
			
			
			$postBattle->save();	
		return response()->json([ 'battle_id' => $postBattle->id,
									'deleted'=>true, ],200);
	}
	
	
	
}
