<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\UserSession;
use App\Models\Profile;
use App\Models\PostSingle;
use App\Models\Tag;
use Validator;

class DiscoveryController extends Controller
{
    
	public function search_result(Request $request)
	{
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'query' => 'nullable|string|max:64',
			'start' => 'nullable|min:0|max:1000',
			'type' => 'nullable|in:profile,tag,brand',
		]);
		
		if(null === ($request->input('start'))){
				$start=0;
		}
		else{
			$start=((int)($request->input('start')/10))+1;
		}
		
		$userSession=UserSession::where('token', $request->input('token'))->first();
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		if(null === ($request->input('query'))){
				$search='';
		}
		else{
			$search=$request->input('query');
		}
		
		
		switch ($request->input('type')) {
			case 'profile':
				$searchedItems = Profile::where('name', 'LIKE', '%'.$search.'%')->paginate(10, ['*'], 'page', $start);
				break;
			case 'tag':
				$refinedQuery = Tag::where('name', 'LIKE', '%'.$search.'%')->first();
				if($refinedQuery){
				    $refinedQuery=$refinedQuery->name;
				}
				$searchedItems = PostSingle::where('tags', 'LIKE', '%'.$refinedQuery.'%')->paginate(10, ['*'], 'page', $start);
				break;
			case 'brand':
				$refinedQuery = Brand::where('name', 'LIKE', '%'.$search.'%')->first();
				if($refinedQuery){
				    $refinedQuery=$refinedQuery->name;
				}
				$searchedItems = PostSingle::orderBy('created_at', 'desc')->where('brands', 'LIKE', '%'.$refinedQuery.'%')->paginate(10, ['*'], 'page', $start);
				break;
			default:
				$refinedQuery='';
				$searchedItems = PostSingle::orderBy('created_at', 'desc')->where('brands', 'LIKE', '%'.$refinedQuery.'%')->paginate(10, ['*'], 'page', $start);
		}
		
		$output=array();
		//return response()->json($searchedItems->getCollection());
		foreach($searchedItems->getCollection() as $searchItem) {
			if($request->input('type')=='profile' || $searchItem["visiblity"]){
				$output=array_merge($output, [[ 'item' => [
					'id'=>($request->input('type')=='profile'?$searchItem["user_id"]:$searchItem["id"]),
					'image_url'=>((int)$searchItem["image_id"]?route('images.show', (int)$searchItem["image_id"]):asset('assets/profile/annonymous/image.png')),
					'name'=>$request->input('type')=='profile' ?($searchItem["name"]):"",
					]]]);
			}
		}
		
		return response()->json([ 'result' => $output,
									'query' => $search,
									'type'=>$request->input('type'),
									'is_end'=>$searchedItems->onLastPage()],200);
	}
}



	
	
