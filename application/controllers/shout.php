<?php

class Shout_Controller extends Base_Controller {

	public function action_index()
	{
		Bundle::start('authority');

		// Show shouts
		$shouts = LANager\Shout::order_by('id', 'desc')->paginate(10);
		return View::make('shout.show')
					->with('title', 'Shouts')
					->with('shouts', $shouts);
	}

	public function action_post()
	{
		$shout = new LANager\Shout(array('content' => Input::get('content')));
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