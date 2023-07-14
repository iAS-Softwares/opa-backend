<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\PostSingle;
use App\Models\UserSession;
use App\Models\Wardrobe;

class WardrobeController extends Controller
{
    
    public function get_wardrobe(Request $request)
    {
        if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
		]);
				
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		$wardrobes = Wardrobe::where('user_id', $userSession->user_id)
						->orderBy('parent_id', 'DESC')->get();
		$collections=array();
		
		foreach($wardrobes as $wardrobe){
			$collections=array_merge($collections, [[ 
			    'collection' => [ 
                    'collection_id'=>$wardrobe->id,
                    'parent_id'=>$wardrobe->parent_id,
                    'items' => $wardrobe->collection,]
                    ]]);
		}
		
		return response()->json(['wardrobe'=>$collections], 200);
    }
	
	 public function add_item(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'wardrobe_id' => 'nullable|numeric|min:0',
			'single_id' => 'required|numeric|min:0',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($request->input('wardrobe_id')>0){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('id', $request->input('wardrobe_id'))
					->orderBy('parent_id', 'DESC')->first();
		}
		else{
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
		}
		
		if(!$wardrobe){
			$wardrobe=Wardrobe::create(['user_id'=> $userSession->user_id]);
		}
		
		
		//return var_dump(json_decode($wardrobe->collection));
		if(!in_array($request->input('single_id'), json_decode($wardrobe->collection))){
		    $wardrobe->collection = json_encode(
									array_merge(
										json_decode($wardrobe->collection), [$request->input('single_id')]
									)
								);
								
		    $wardrobe->save();
		}
		
		return response()->json([
						'status'=>'success',
						'wardrobe_id'=>$wardrobe->id,
						], 200);
    }


	 public function remove_item(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'wardrobe_id' => 'nullable|numeric|min:0',
			'single_id' => 'required|numeric|min:0',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($request->input('wardrobe_id')>0){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('id', $request->input('wardrobe_id'))
					->orderBy('parent_id', 'DESC')->first();
		}
		else{
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
		}
		
		if(!$wardrobe){
		    return response()->json([
						'status'=>'success',
						], 200);
		}
		
		//return var_dump(json_decode($wardrobe->collection));
		if(in_array($request->input('single_id'), json_decode($wardrobe->collection))){
		    $filteredArr = array_diff(	json_decode($wardrobe->collection), [$request->input('single_id')]	);
		    $_filteredArr=array();
		    foreach($filteredArr as $k){
		        array_push($_filteredArr, $k);
		    }
		    $wardrobe->collection = $_filteredArr;
		    $wardrobe->save();
		}
		
		return response()->json([
						'status'=>'success',
						'wardrobe_id'=>$wardrobe->id,
						], 200);
    }
	 public function remove_items(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}
        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'wardrobe_id' => 'nullable|numeric|min:0',
			'single_ids' => 'required|string|min:0',
		]);
		
		$singleIds=json_decode($request->input('single_ids'));
		if(gettype($singleIds)!=='array'){
		    return response()->json([
						'error'=>'The single_ids must be an array.',
						], 400);
		}

		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		if($request->input('wardrobe_id')>0){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('id', $request->input('wardrobe_id'))
					->orderBy('parent_id', 'DESC')->first();
		}
		else{
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('parent_id', 0)
					->orderBy('parent_id', 'DESC')->first();
		}
		
		if(!$wardrobe){
		    return response()->json([
						'status'=>'success',
						], 200);
		}
		$wardrobeCollection=json_decode($wardrobe->collection);				
		foreach($singleIds as $singleId){
		$wardrobeCollection=json_decode($wardrobe->collection);	
		//return var_dump(json_decode($wardrobe->collection));
		if(in_array($singleId, $wardrobeCollection)){
		    $filteredArr = array_diff(	$wardrobeCollection, [$singleId]	);
		    $_filteredArr=array();
		    foreach($filteredArr as $k){
		        array_push($_filteredArr, $k);
		    }
		    $wardrobe->collection = json_encode($_filteredArr);
		    $wardrobe->save();
		}
		}
		
		return response()->json([
						'status'=>'success',
						'wardrobe_id'=>$wardrobe->id,
						], 200);
    }

	 public function get_wardrobe_id(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'wardrobe_id' => 'nullable|numeric|min:0',
			'single_id' => 'required|numeric|min:0',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		$foundWardrobeIds=array();
		$itemFound=false;
		if($request->input('wardrobe_id')>0){
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->where('id', $request->input('wardrobe_id'))
					->orderBy('parent_id', 'DESC')->first();
			$_itemFound=in_array($request->input('single_id'), json_decode($wardrobe->collection), true);
			if($_itemFound){
				$itemFound=$itemFound||$_itemFound;
				array_push($foundWardrobeId, $wardrobe->id);
			}
		}
		else{
			$wardrobe = Wardrobe::where('user_id', $userSession->user_id)
					->orderBy('parent_id', 'DESC')->all();
					
			foreach($wardrobes as $wardrobe){
				$_itemFound=in_array($request->input('single_id'), json_decode($wardrobe->collection), true);
				if($_itemFound){
					$itemFound=$itemFound||$_itemFound;
					array_push($foundWardrobeId, $wardrobe->id);
				}
			}
		}
		
		if($itemFound){
			return response()->json([
						'status'=>'found',
						'wardrobe_ids'=>$foundWardrobeIds,
						], 200);
		} else{
			return response()->json([
						'status'=>'absent',
						'wardrobe_ids'=>$foundWardrobeIds,
						], 200);
		}
    }
	
	
	
	public function get_details_wardrobe(Request $request)
    {
        //https://picsum.photos/200
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'start' => 'nullable|integer|min:0',
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=$request->input('start');
		}
		
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		$wardrobes = Wardrobe::where('user_id', $userSession->user_id)
						->orderBy('parent_id', 'DESC')->get();
					
		$collections=array();
		
		foreach($wardrobes as $wardrobe){
		    $myItems=array();
		    $i=0;
            foreach(json_decode($wardrobe->collection) as $postId){
                if($i<16){$i=$i+1;}
		        $post1 = PostSingle::where('id', $postId)->first();
                
			    $myItems=array_merge($myItems, [[ 'post' => [
			    	'post_id'=> $post1->id,
				    'image_uri' => route('images.show', $post1->image_id),
				    'show_icon' => (bool)rand(0,1),
				    'caption' => $post1->caption,
				    //'brands' => json_decode($post1->brands)->brands,
				    //'tags' => json_decode($post1->tags)->tags 
			    	] ]]);
            }
		    
			$collections=array_merge($collections, [[ 
			    'collection' => [ 
                    'collection_id'=>$wardrobe->id,
                    'parent_id'=>$wardrobe->parent_id,
                    'items' =>$myItems]
                    ]]);
		}
		
		return response()->json(['wardrobe'=>$collections], 200);
	}
}
