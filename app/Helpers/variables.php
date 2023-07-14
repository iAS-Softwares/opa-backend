<?php 

if( !function_exists('annonymous_profile')){
	function annonymous_profile(){
		$profile = ['name'=>'Annonymous',
		            'user_id'=>null,
					'profile_image'=>asset('assets/profile/annonymous/image.png'),
					'is_followed'=>false,
					'is_annonymous'=>true,
					'is_owner'=>false
								];
		return $profile;
    }
}