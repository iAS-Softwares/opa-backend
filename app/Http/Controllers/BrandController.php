<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\UserSession;
use Validator;

class BrandController extends Controller
{
    
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
		
		$brandsFound = Brand::where('name', 'LIKE', '%'.$search.'%')->paginate(10);
	
		$output=array();
		foreach($brandsFound->getCollection() as $brandItem) {
			if($brandItem["visiblity"]){
				$output=array_merge($output, [[ 'brand' => [
					'name'=>$brandItem["name"],
					'slug'=>$brandItem["slug"],
					]]]);
			}
		}
		if(null !== ($request->input('query'))){
			$output=array_merge($output, [[ 'brand' => [
			    'name'=>$search,
			    'slug'=>null,
				]]]);
		}
		
		return response()->json([ 'brands' => $output,
									'query' => $search ],200);
	}
}
