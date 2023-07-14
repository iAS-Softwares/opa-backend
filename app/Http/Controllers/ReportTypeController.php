<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\ReportType;
use App\Models\UserSession;

class ReportTypeController extends Controller
{
    
	 public function get_reports(Request $request)
    {
        
		if(!validate_app($request->input('appId'))){
			return response()->json(['error' => ['token' => 'unauthorized']],403);
		}

        $validator = Validator::make($request->all(), [
			'token' => 'required|string|max:64',
			'post_type' => 'nullable|in:single,battle',
		]);
		
		if($validator->fails()){
			return response()->json(['error'=>array('validation' => $validator->errors()->all())],400);
		}
		
		$userSession = UserSession::where('token', $request->input('token'))->first();
		if(!$userSession){
			return response()->json(['error' => ['session' => 'unauthorized']],403);
		}
		
		$reportTypes = ReportType::all();
		
		$output=array();
		foreach($reportTypes as $reportType){
		    if(!$reportType->hidden){
			    array_push($output, [
									'report_id'=>$reportType->id,
									'report_title'=>$reportType->title,
									'report_description'=>$reportType->description
									] );
		    }
		}
		
		return response()->json([
						'report_types'=>$output,
						], 200);
    }
}
