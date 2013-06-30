<?php

class Playlist_Controller extends Base_Controller {

	public function action_index()
	{
		// Show playlist
		$playlist_entries = LANager\Playlist_entry::where('playback_state', '=', 1) // playing
												->or_where('playback_state', '=', 0) // unplayed
												->or_where('playback_state', '=', 2) // paused
												->order_by('created_at', 'asc')->paginate(50);
		return View::make('playlist.show')
					->with('title', 'Playlist')
					->with('playlist_entries', $playlist_entries);
	}

	public function action_add_entry()
	{
		
		// Extract video ID from URL
		$raw_url = Input::get('url');
		parse_str( parse_url( $raw_url, PHP_URL_QUERY ), $youtube_url );

		if(empty($youtube_url['v']))
		{
			return Redirect::back()->with('errors',array(0=>'Invalid video URL'));
		}
		// Retrieve video metadata
		$response = file_get_contents('http://gdata.youtube.com/feeds/api/videos/'.$youtube_url['v'].'?format=5&alt=json');
		$response = json_decode($response, true); // convert JSON response to array

		$playlist_entry = new LANager\Playlist_entry(array('video_id' => $youtube_url['v']));
		$playlist_entry->title = $response['entry']['title']['$t'];
		$playlist_entry->duration = $response['entry']['media$group']['yt$duration']['seconds'];
		$playlist_entry->user_id = Session::get('user_id');

		if( $playlist_entry->save() )
		{
			return Redirect::to_route('playlist');
		}
		else
		{
			return Redirect::back()->with('errors',$playlist_entry->errors->all());
		}
	}


	public function action_screen()
	{ 
			// Show screen - all videos loaded via javascript
			return View::make('playlist.screen')
						->with('title', 'Playlist Screen');
	}

	// Get current playlist entry and its playback state
	public function action_get_entry()
	{
		// get either
		//  - currently playing video
		//  - currently paused video
		//  - next unplayed video
		$playlist_entry = LANager\Playlist_entry::with('user')
												->where('playback_state', '=', 2) // paused
												->or_where('playback_state', '=', 1) // playing
												->or_where('playback_state', '=', 0) // unplayed
												->order_by('playback_state', 'desc') // order: paused, playing, unplayed
												->order_by('created_at', 'asc') // secondary order: oldest to newest
												->first();
		if(!empty($playlist_entry))
		{
			// return entry as JSON
			return Response::eloquent($playlist_entry);
		}
	}

	// Mark entry as playing/played/paused
	public function action_mark_entry($entry_id,$playback_state)
	{
		return DB::table('playlist_entries')
					->where('id', '=', $entry_id)
					->update(array('playback_state' => $playback_state));
	}


	public function action_history()
	{
		// Show playlist history
		$playlist_entries = LANager\Playlist_entry::where('playback_state', '=', 4)
												->order_by('updated_at', 'desc')->paginate(50);
		return View::make('playlist.history')
					->with('title', 'Playlist')
					->with('playlist_entries', $playlist_entries);
	}

	// Pause the currently playing item
	public function action_pause()
	{
		DB::table('playlist_entries')
			->where('playback_state', '=', 1) // playing
			->update(array('playback_state' => 2)); // set to paused
		return Redirect::back();
	}

	// Play the currently paused item
	public function action_play()
	{
		DB::table('playlist_entries')
			->where('playback_state', '=', 2) // paused
			->update(array('playback_state' => 1)); // set to playing
		return Redirect::back();
	}

	// Skip the currently playing item
	public function action_skip()
	{
		DB::table('playlist_entries')
			->where('playback_state', '=', 1) // playing
			->or_where('playback_state', '=', 2) // paused
			->update(array('playback_state' => 3)); // set to skipped
		return Redirect::back();
	}

	// Delete the specified item
	public function action_delete_entry($entry_id)
	{
		DB::table('playlist_entries')->where('id', '=', $entry_id)->delete();
		return Redirect::back();
	}

	// Skip the specified item
	public function action_skip_entry($entry_id)
	{
		DB::table('playlist_entries')->where('id', '=', $entry_id)->update(array('playback_state' => 3));
		return Redirect::back();
	}

}