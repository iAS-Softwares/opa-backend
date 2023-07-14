<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostSingleRequest;
use App\Http\Requests\UpdatePostSingleRequest;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Follow;
use App\Models\Profile;
use App\Models\Image;
use App\Models\Like;
use App\Models\UserSession;
use App\Models\PostSingle;
use App\Models\PostBattle;
use App\Models\PostBoost;
use App\Models\Wardrobe;

class PostSingleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $postSingles = PostSingle::all();
		return json_encode($postSingles);
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
     * @param  \App\Http\Requests\StorePostSingleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostSingleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PostSingle  $postSingle
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'post_id' => 'nullable|min:0|max:100'
		]);
				
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		$winnerPost = PostSingle::where('id', $request->input('post_id'))->first();
		//$winnerPost = PostSingle::where('id', 1)->first();
		if(!$winnerPost){
			return response()->json(['error' => ['post_id' => 'invalid']],403);
		}
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		//wardrobe check
		$itemFound=false;
		if($userSession){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
				if($wardrobe){
        			$_itemFound=in_array($request->input('post_id'), json_decode($wardrobe->collection));
					$itemFound=$itemFound||$_itemFound;
				}
		}
		
		$postOwner = Profile::where('user_id', $winnerPost->user_id)->first();
		$followed = Follow::where('following_id', ($postOwner?$postOwner->user_id:-1))
		                    ->where('follower_id', ($userSession?$userSession->user_id:''))->count();
		$postBattle=PostBattle::where('post_id1', $winnerPost->id)
		                ->where('user_id', $winnerPost->user_id)->first();
		if($postBattle){
		    $is_annonymous=$postBattle?$postBattle->annonymous:true;
		}else{
		    $postBattle=PostBattle::where('post_id2', $winnerPost->id)
		                ->where('user_id', $winnerPost->user_id)->first();
		    $is_annonymous=$postBattle?$postBattle->annonymous:true;
		}
		$is_annonymous=$postBattle?$postBattle->annonymous:true;
			$output=[ 'post' => [
				'post_id'=>$winnerPost->id,
				'image_uri' => route('images.show', $winnerPost->image_id),
				'caption' => $winnerPost->caption,
				'brands' => json_decode($winnerPost->brands),
				'tags' => json_decode($winnerPost->tags)->tags 
				],
				'battle_id'=>($postBattle?$postBattle->id:null),
				'is_annonymous'=>$is_annonymous,
				'in_wardrobe'=>$itemFound,
				'user'		=>	($is_annonymous?annonymous_profile():[
					'user_id'=>($postOwner?$postOwner->user_id:null),
				    'name'=>($postOwner?$postOwner->name:null),
				    'profile_image'=>(($postOwner&&$postOwner->image_id)?route('images.show', $postOwner->image_id):''),
				    'is_followed'=>(bool)$followed,
				    'is_owner'=>($postOwner&&($postOwner->user_id==($userSession?$userSession->user_id:null)?true:false))
				    ])
				    ];
		
		
		return response()->json($output, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PostSingle  $postSingle
     * @return \Illuminate\Http\Response
     */
    public function edit(PostSingle $postSingle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostSingleRequest  $request
     * @param  \App\Models\PostSingle  $postSingle
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostSingleRequest $request, PostSingle $postSingle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PostSingle  $postSingle
     * @return \Illuminate\Http\Response
     */
    public function destroy(PostSingle $postSingle)
    {
        //
    }
	
	public function whatshot_view(Request $request)
    {
        //https://picsum.photos/200
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
			$start=((int)($request->input('start')/12))+1;
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		$postBattleSet = PostBattle::whereRaw('MOD(total_count, 2) = 1')->orderBy('updated_at', 'desc')->paginate(12, ['*'], 'page', $start);
		$output=array();
		foreach($postBattleSet->getCollection() as $postBattle) {
		$post1 = PostSingle::where('id', $postBattle["post_id1"])->first();
		$post2 = PostSingle::where('id', $postBattle["post_id2"])->first();
		if($post1->count > $post2->count){
			$winnerPost = $post1;
		}
		elseif($post1->count < $post2->count){
			$winnerPost = $post2;
		}
		else{
			continue;
		}
			$output=array_merge($output, [[ 'winner_post' => [
				'winner_id'=> $winnerPost->id,
				'image_uri' => route('images.show', $winnerPost->image_id),
				'show_icon' => false,
				'caption' => $winnerPost->caption,
				//'brands' => json_decode($winnerPost->brands)->brands,
				//'tags' => json_decode($winnerPost->tags)->tags 
				] ]]);
		}
		
		return response()->json([ 'winner_posts' => $output,
									'start' => $start,
									'is_end'=>$postBattleSet->onLastPage()],200);
	}
	
	
	
	public function get_details_ofArray(Request $request)
    {
        //https://picsum.photos/200
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			"list"    => "required|array|min:1|max:51",
            "list.*"  => "required|integer|distinct|min:0",
		]);
		
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$output=array();
		foreach($request->input('list') as $postId){
		    $post1 = PostSingle::where('id', $postId)->first();
			
			$output=array_merge($output, [[ 'post' => [
				'post_id'=> $post1->id,
				'image_uri' => route('images.show', $post1->image_id),
				'show_icon' => false,
				'caption' => $post1->caption,
				//'brands' => json_decode($post1->brands)->brands,
				//'tags' => json_decode($post1->tags)->tags 
				] ]]);
		}
		
		return response()->json([ 'posts' => $output ],200);
	}
	
	
	
	public function user_uploads(Request $request)
    {
        //https://picsum.photos/200
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:100',
			'user_id' => 'required|integer|min:-1'
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/4))+1;
		}
		
		
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		$userId = ($userSession)?$userSession->user_id:0;
		$requestedId=$request->input('user_id');
		if($request->input('user_id')<1){
		    if($request->input('user_id')==-1){
		        $requestedId=$userId;
		    }
		    else{
		        return response()->json(['error' => ['user_id' => 'invalid']],403);
		    }
		}
		
		$askedProfile = Profile::where('user_id', $requestedId)->first();
		if(!$askedProfile){
			return response()->json(['error' => ['user_id' => 'invalid']],403);
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		$postBattleSet = PostBattle::where('user_id', $requestedId)->orderBy('created_at', 'desc')->paginate(4, ['*'], 'page', $start);
		$output=array();
		foreach($postBattleSet->getCollection() as $postBattle) {
		$post1 = PostSingle::where('id', $postBattle["post_id1"])->first();
		$post2 = PostSingle::where('id', $postBattle["post_id2"])->first();
		
		$isBoosted = PostBoost::where('user_id', $askedProfile->user_id)->where('post_id', $postBattle["id"])->where('type', "battle")->first();
        
        
        $testVal=($userId==$askedProfile->user_id) || (!$postBattle['annonymous']);
        if($testVal && !$postBattle['annonymous']){
            if($post1->visiblity||($request->input('user_id')==-1)){
			$output=array_merge($output, [[
			        'post'=>[
			                        'id'=>$post1->id,
			                        'image_url'=>route('images.show', $post1->image_id),
			                        'isBoosted'=>(boolean)$isBoosted],
				             ]]);
            }
            if($post2->visiblity||($request->input('user_id')==-1)){
			$output=array_merge($output, [[
			        'post'=>[
			                        'id'=>$post2->id,
			                        'image_url'=>route('images.show', $post2->image_id),
			                        'isBoosted'=>(boolean)$isBoosted],
				 ]]);
            }
		}
		}
		return response()->json([ 'posts' => $output,
									'start' => $start,
									'is_end'=>$postBattleSet->onLastPage()],200);
	}
	
	
	public function user_battles(Request $request)
    {
        //https://picsum.photos/200
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:100',
			'user_id' => 'required|integer|min:-1'
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/6))+1;
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		$userId = ($userSession)?$userSession->user_id:0;
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		if($request->input('user_id')<1){
		    if($request->input('user_id')==-1){
		        $requestedId=$userId;
		    }
		    else{
		        return response()->json(['error' => ['user_id' => 'invalid']],403);
		    }
		}
		
		$askedProfile = Profile::where('user_id', $requestedId)->first();
		if(!$askedProfile){
			return response()->json(['error' => ['user_id' => 'invalid']],403);
		}
		
		$postBattleSet = PostBattle::where('user_id', $requestedId)->paginate(6, ['*'], 'page', $start);
		$output=array();
		foreach($postBattleSet->getCollection() as $postBattle) {
		$post1 = PostSingle::where('id', $postBattle["post_id1"])->first();
		$post2 = PostSingle::where('id', $postBattle["post_id2"])->first();
		
		$isBoosted = PostBoost::where('user_id', $askedProfile->user_id)->where('post_id', $postBattle["id"])->where('type', "battle")->first();
        
        
        $testVal=($userId==$askedProfile->user_id) || (!$postBattle['annonymous']);
        if($testVal && $postBattle['visiblity']){
			$output=array_merge($output, [[ 'post_battle' => [
			        'single_1'=>[
			                        'id'=>$post1->id,
			                        'image_url'=>route('images.show', $post1->image_id),
			                        'isBoosted'=>(boolean)$isBoosted],
			                        
			        'single_2'=>[
			                        'id'=>$post2->id,
			                        'image_url'=>route('images.show', $post2->image_id),
			                        'isBoosted'=>(boolean)$isBoosted],
			        'isBoosted'=>(boolean)$isBoosted,
				] ]]);
		}
		}
		return response()->json([ 'post_battles' => $output,
									'start' => $start,
									'is_end'=>$postBattleSet->onLastPage()],200);
	}
	
	
	public function user_likes(Request $request)
    {
        //https://picsum.photos/200
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:100',
			'user_id' => 'required|integer|min:-1'
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/12))+1;
		}
		
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		$userId = ($userSession)?$userSession->user_id:0;
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		//to stop asking other users likes
		$requestedId=$userId;
		$askedProfile = Profile::where('user_id', $requestedId)->first();
		if(!$askedProfile){
			return response()->json(['error' => ['user_id' => 'invalid']],403);
		}
		
		$postBattleSet = Like::where('user_id', $requestedId)->where('article_category', 'postBattle')->orderBy('updated_at', 'desc')->paginate(12, ['*'], 'page', $start);
		
		$output=array();
		foreach($postBattleSet->getCollection() as $postBattle) {
		$post1 = PostSingle::where('id', $postBattle["article_id"])->first();
		
		$isBoosted = PostBoost::where('user_id', $askedProfile->user_id)->where('post_id', $postBattle["id"])->where('type', "battle")->first();
        
        
        $testVal=($userId==$askedProfile->user_id) || !$post1->banned;
        if($testVal && $post1->visiblity){
			$output=array_merge($output, [[
			        'post'=>[
			                        'id'=>$post1->id,
			                        'image_url'=>route('images.show', $post1->image_id),
			                        'isBoosted'=>(boolean)$isBoosted],
				             ]]);
		}
		}
		return response()->json([ 'posts' => $output,
									'start' => $start,
									'is_end'=>$postBattleSet->onLastPage()],200);
	}
	
	
	
	public function whatshot_related(Request $request)
    {
        //https://picsum.photos/200
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|min:0|max:100',
			'post_id' => 'required|min:1'
			
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)(($request->input('start')-1)/4))+1;
		}
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		$output=array();
		$refPost = PostSingle::where('id', $request->input('post_id'))->first();
		$postOwner = Profile::where('user_id', $refPost->user_id)->first();
		
		//wardrobe check
		$itemFound=false;
		if($userSession){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
				if($wardrobe){
        			$_itemFound=in_array($request->input('post_id'), json_decode($wardrobe->collection));
					$itemFound=$itemFound||$_itemFound;
				}
		}
		
		    $is_annonymous=$refPost?$refPost->annonymous:true;
		$followed = Follow::where('following_id', ($postOwner?$postOwner->user_id:-1))
		                    ->where('follower_id', ($userSession?$userSession->user_id:''))->count();
			$output=array_merge($output, [[ 'post' => [
				'post_id'=>$refPost->id,
				'image_uri' => route('images.show', $refPost->image_id),
				'caption' => $refPost->caption,
				'brands' => json_decode($refPost->brands),
				'tags' => json_decode($refPost->tags)->tags 
				],
				'is_annonymous'=>$is_annonymous,
				'in_wardrobe'=>$itemFound,
				'user'		=>	($is_annonymous?annonymous_profile():[
					'user_id'=>($postOwner?$postOwner->user_id:null),
				    'name'=>($postOwner?$postOwner->name:null),
				    'profile_image'=>(($postOwner&&$postOwner->image_id)?route('images.show', $postOwner->image_id):''),
				    'is_followed'=>(bool)$followed,
				    'is_owner'=>($postOwner&&($postOwner->user_id==($userSession?$userSession->user_id:null)?true:false))
				    ])
				    ]]);
		
		$postBattleSet = PostBattle::whereRaw('MOD(total_count, 2) = 1')->orderBy('updated_at', 'desc')->paginate(4, ['*'], 'page', $start);
		foreach($postBattleSet->getCollection() as $postBattle) {
		$post1 = PostSingle::where('id', $postBattle["post_id1"])->first();
		$post2 = PostSingle::where('id', $postBattle["post_id2"])->first();
		if($post1->count > $post2->count){
			$winnerPost = $post1;
		}
		elseif($post1->count < $post2->count){
			$winnerPost = $post2;
		}
		else{
			continue;
		}
		$postOwner = Profile::where('user_id', $winnerPost->user_id)->first();
		    $is_annonymous=$postBattle?$postBattle->annonymous:true;
		    
		$followed = Follow::where('following_id', ($postOwner?$postOwner->user_id:-1))
		                    ->where('follower_id', ($userSession?$userSession->user_id:''))->count();
		    
		//wardrobe check
		$itemFound=false;
		if($userSession){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
				if($wardrobe){
        			$_itemFound=in_array($winnerPost->id, json_decode($wardrobe->collection));
					$itemFound=$itemFound||$_itemFound;
				}
		}
		    if($winnerPost->id !==$refPost->id){
			$output=array_merge($output, [[ 'post' => [
				'post_id'=>$winnerPost->id,
				'image_uri' => route('images.show', $winnerPost->image_id),
				'caption' => $winnerPost->caption,
				'brands' => json_decode($winnerPost->brands),
				'tags' => json_decode($winnerPost->tags)->tags 
				],
				'battle_id'=>($postBattle?$postBattle->id:null),
				'is_annonymous'=>$is_annonymous,
				'in_wardrobe'=>$itemFound,
				'user'		=>	($is_annonymous?annonymous_profile():[
					'user_id'=>($postOwner?$postOwner->user_id:null),
				    'name'=>($postOwner?$postOwner->name:null),
				    'profile_image'=>(($postOwner&&$postOwner->image_id)?route('images.show', $postOwner->image_id):''),
				    'is_followed'=>(bool)$followed,
				    'is_owner'=>($postOwner&&($postOwner->user_id==($userSession?$userSession->user_id:null)?true:false))
				    ])
				    ]]);
		}
		}
		return response()->json([ 'winner_posts' => $output,
									'start' => $start,
									'is_end'=>$postBattleSet->onLastPage()],200);
	}
	
	
	
}
