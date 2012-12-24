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
			$LightOpenId->validate();
			$identity = $LightOpenId->identity;
			$identity = str_replace('http://steamcommunity.com/openid/id/','',$identity);
			
			Bundle::start('SteamProfile');
			$SteamProfile = new SteamProfile($identity);
			$SteamProfile->fetchProfile();

			var_dump($SteamProfile);

			return View::make('home.index')->with('title',$SteamProfile->getProfileName());
		} else {
			return Redirect::home();
		}
	}

}