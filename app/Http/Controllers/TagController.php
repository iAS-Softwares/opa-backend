<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\UserSession;
use Validator;

class TagController extends Controller
{
    
	
    public function index()
    {
        $tags = Tag::all();
		return json_encode($tags);
    }
	
    public function add(Request $request){
		if($request->bearerToken()!='TrustMeNotARobot'){
			return response()->json(
			[	'message' => 'error',
				'data' => 'invalid bearer token' ]
				);
		}
		
        $request->validate([
            'name' => 'required|max:20'
        ]);
		
		$oldTag=Tag::where('name', '==', $request->name)->first();
		if(is_null($oldTag)){
			$oldTag->update([
				'count' => ($oldTag->count + 1)
				]);
			return response()->json(json_encode($oldTag));
		}
		else{
			
		}
	}
	public function search_result(Request $request)
	{
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['appId' => 'unauthorized']],403);
		}
		
		$validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'query' => 'nullable|string|max:64'
		]);
		
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
		
		$TagsFound = Tag::where('name', 'LIKE', '%'.$search.'%')->paginate(10);
	
		$output=array();
		foreach($TagsFound->getCollection() as $TagItem) {
			if($TagItem["visiblity"]){
				$output=array_merge($output, [[ 'Tag' => [
					'name'=>$TagItem["name"],
					'slug'=>$TagItem["slug"],
					]]]);
			}
		}
		if(null !== ($request->input('query'))){
			$output=array_merge($output, [[ 'Tag' => [
			    'name'=>$search,
			    'slug'=>null,
				]]]);
		}
		
		return response()->json([ 'Tags' => $output,
									'query' => $search ],200);
	}
}
