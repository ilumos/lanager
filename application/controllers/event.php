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

	public function action_timetable()
	{
		
		$timetable['first_event_start'] = strtotime(DB::table('events')->min('start'));
		$timetable['last_event_end'] = strtotime(DB::table('events')->max('end'));

		$events = LANager\Event::order_by('start', 'asc')->get();
		return View::make('event.timetable')
					->with('title','Events')
					->with('timetable',$timetable)
					->with('events',$events);
	}

}