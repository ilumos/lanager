<?php

class Shout_Controller extends Base_Controller {

	public function action_index()
	{
		// Show shouts
		$shouts = Shout::order_by('id', 'desc')->take(10)->get();
		return View::make('shout.show')
					->with('title', 'Shouts')
					->with('shouts', $shouts);
	}

	public function action_post()
	{
		//
	}

}