<?php

class User_Controller extends Base_Controller {

	public function action_index()
	{
		//
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

			// See if the user has logged in previously
			$user = LANager\User::where('steam_id_64', '=', $steamId64)->first();
	
			// If they have not, create them in the database as an attendee
			if($user == NULL) {
				$user = new LANager\User;
				$roles[] = LANager\Role::where('name','=','attendee')->first();

				// If they are the first user to sign in (or the first user after the initial seed)
				if(LANager\User::count() <= 1)
				{
					// Give them admin privileges
				$roles[] = LANager\Role::where('name','=','admin')->first();
				}
			}

			// Add or update their details
			$user->steam_id_64 = $steamId64;
			$user->username = $SteamProfile->getProfileName();
			$user->ip = getenv("REMOTE_ADDR");
			$user->avatar = $SteamProfile->getAvatarSmall();
			$user->save();

			if(isset($roles))
			{
				foreach($roles as $role)
				{
					$user->roles()->attach($role);
				}
			}

			// Log the user in
			Auth::login($user->id);

		}
		return Redirect::home();
	}
	
	public function action_logout()
	{
		Auth::logout();
		return Redirect::back();
	}

	public function action_profile($user_id)
	{
		$user = LANager\User::find($user_id);
		$steamProfile = new SteamProfile($user->steam_id_64);
		$steamProfile->fetchProfile();
		return View::make('user.profile')
					->with('title',$user->username)
					->with('user',$user)
					->with('steamProfile',$steamProfile)
					->with('shouts',$user->shouts()->order_by('id', 'desc')->paginate(10)); // recent shouts
	}

	public function action_list()
	{
		$users = LANager\User::order_by('username', 'asc')->paginate(10);
		return View::make('user.list')
					->with('title','People')
					->with('users',$users);

	}


}