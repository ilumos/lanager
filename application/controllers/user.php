<?php

class User_Controller extends Base_Controller {

	public function action_index()
	{
		return View::make('home.index');
	}

	public function action_login()
	{
		$openID = new LightOpenID;
		if(Input::has('openid_mode')) {
			$openID->validate();
			$identity = $openID->identity;
			$identity = str_replace('http://steamcommunity.com/openid/id/','',$identity);
			return View::make('home.index')->with('title',$identity);
		} else {
			return Redirect::home();
		}
	}

}