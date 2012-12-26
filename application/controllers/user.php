<?php

class User_Controller extends Base_Controller {

	public function action_index()
	{
		return View::make('home.index');
	}

	public function action_login()
	{
		$LightOpenId = new LightOpenId;
		if(Input::has('openid_mode')) {
			
			// Process OpenID validation
			$LightOpenId->validate();
			$identity = $LightOpenId->identity;
			$steamId64 = str_replace('http://steamcommunity.com/openid/id/','',$identity);
			
			// Pull profile details from Steam
			Bundle::start('SteamProfile');
			$SteamProfile = new SteamProfile($steamId64);
			$SteamProfile->fetchProfile();

			// Create or update user details in database
			$user = User::find($steamId64);
			if($user == NULL) {
				$user = new User;
			}
			$user->id = $steamId64;
			$user->username = $SteamProfile->getProfileName();
			$user->avatar_small = $SteamProfile->getAvatarSmall();
			$user->avatar_medium = $SteamProfile->getAvatarMedium();
			$user->avatar_large = $SteamProfile->getAvatarLarge();
			$user->save();

			// Start a session
			Session::put('user_id', $steamId64);
			Session::put('username', $SteamProfile->getProfileName());
			Session::put('avatar_small', $SteamProfile->getAvatarSmall());
			Session::put('avatar_medium', $SteamProfile->getAvatarMedium());
			Session::put('avatar_large', $SteamProfile->getAvatarLarge());
		}
		return Redirect::home();
	}
	
	public function action_logout()
	{
		Session::flush();
		return Redirect::home();
	}

}