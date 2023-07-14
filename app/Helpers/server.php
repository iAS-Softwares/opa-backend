<?php 

if( !function_exists('avinash_is_local')){
	function avinash_is_local(){
		$whitelist = array(	'127.0.0.1', '::1');
		if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
			return true;
		}
		else{
			return false;
		}
    }
}
