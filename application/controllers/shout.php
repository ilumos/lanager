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
		$shout = new Shout(array('content' => Input::get('content')));
		$shout->content = Input::get('content');
		$shout->user_id = Session::get('user_id');
		if( $shout->save() )
		{
			return Redirect::to_route('shouts');
		}
		else
		{
			return Redirect::back()->with('errors',$shout->errors->all());
		}
	}

}