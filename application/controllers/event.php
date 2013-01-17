<?php

class Event_Controller extends Base_Controller {

	public function action_index()
	{
		//
	}

	public function action_list()
	{
		$events = LANager\Event::order_by('time_start', 'asc')->paginate(10);
		return View::make('event.list')
					->with('title','Events')
					->with('events',$events);
	}

}