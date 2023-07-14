<?php 


if (! function_exists('encrypt_integer')) {
    function encrypt_integer($key) {
		return $key + 26022001;
    }
}

if (! function_exists('decrypt_integer')) {
    function decrypt_integer($key) {
		return $key - 26022001;
    }
}

if (! function_exists('validate_app')) {
    function validate_app($key) {
		$status = 401;
		
        if($key!='specialKey'){
			return false;
		}
		return true;
    }
}

if (! function_exists('validate_access_key')) {
    function validate_access_key($key, $trueKey) {
		$token = 'unauthorize';
		$status = 401;
		
        if($key!=$trueKey){
			return response()->json(['token' => $token],$status);
		}
    }
}

if (! function_exists('validate_request')) {
    function validate_request($request, $trueKey) {
		validate_app($request->input('appId'));
		validate_access_key($request->input('accessKey'), $trueKey);
    }
}