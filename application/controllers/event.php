<?php

class Event_Controller extends Base_Controller {

	public function action_index()
	{
		//
	}

	public function action_list()
	{
		$events = LANager\Event::order_by('start', 'asc')->paginate(10);
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

	public function action_create()
	{
		$event_types = LANager\Event_type::order_by('name', 'asc')->lists('name','name');
		$managers = LANager\User::order_by('username', 'asc')->lists('username','id');
		//print_r($event_types);
		return View::make('event.form')
					->with('title','Create Event')
					->with('event_types',$event_types)
					->with('managers',$managers);
	}

}