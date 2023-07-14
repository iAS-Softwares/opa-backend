<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\ReportType;
use App\Models\PostReport;
use App\Models\UserSession;
use App\Models\PostSingle;
use App\Models\PostBattle;


class PostReportController extends Controller
{
    public function submit_report(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'post_type' => 'nullable|in:single,battle,user',
			'post_id' => 'required|numeric|min:0',
			'report_id' => 'required|numeric|min:0',
			'report_description' => 'string',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		$isPostPresent=false;
		if($request->input('post_type')=='battle'){
			$isPostPresent=PostBattle::where('id', $request->input('post_id'))->first();
		} elseif($request->input('post_type')=='user'){
			$isPostPresent=User::where('id', $request->input('post_id'))->first();
		    
		   
		   } else{
			$isPostPresent=PostSingle::where('id', $request->input('post_id'))->first();
		}
			
			if(!$isPostPresent){
				return response()->json(['error' => ['post' => 'invalid']],403);
			}
		
		if(!ReportType::where('id', $request->input('report_id'))->first()){
			return response()->json(['error' => ['report_type' => 'invalid']],403);
		}
			
		$post_report = PostReport::create([
			'user_id'=>$userSession->user_id,
			'post_id' => $request->input('post_id'),
			'report_id' => $request->input('report_id'),
			'description' => $request->input('report_description'),
			'type' => ($request->input('post_type')?$request->input('post_type'):'single'),
		]);
		
		return response()->json([
						'status'=>'success',
						], 200);
    }
}
