<?php

class User_Controller extends Base_Controller {

	public function action_index()
	{
		return View::make('user.signin');
	}

}