<?php

class Event_Controller extends Base_Controller {

	public function action_index()
	{
		// show timetable
		$timetable['first_event_start'] = strtotime(DB::table('events')->min('start'));
		$timetable['last_event_end'] = strtotime(DB::table('events')->max('end'));

		$events = LANager\Event::order_by('start', 'asc')->get();
		return View::make('event.timetable')
					->with('title','Events')
					->with('timetable',$timetable)
					->with('events',$events);
	}

	public function action_get_create()
	{
		$event_types = LANager\Event_type::order_by('name', 'asc')->lists('name','name');
		$managers = LANager\User::order_by('username', 'asc')->lists('username','id');

		return View::make('event.form')
					->with('title','New Event')
					->with('event_types',$event_types)
					->with('managers',$managers);
	}

	public function action_post_create()
	{
		$shout = new LANager\Event(array('content' => Input::get('content')));
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