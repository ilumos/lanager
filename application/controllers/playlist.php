<?php

class Playlist_Controller extends Base_Controller {

	public function action_index()
	{
		// Show playlist
		$playlist_entries = LANager\Playlist_entry::where('playback_state', '=', 1)
												->or_where('playback_state', '=', 0)
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
		// Retrieve video title
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'http://gdata.youtube.com/feeds/api/videos/'.$youtube_url['v']);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		curl_close($ch);

		if ($response) {
			$youtube_xml = new SimpleXMLElement($response);
			$title = (string) $youtube_xml->title;
		} else {
			return Redirect::back()->with('errors',array(0=>'Error retrieving requested video'));
		}

		$playlist_entry = new LANager\Playlist_entry(array('id' => $youtube_url['v']));
		$playlist_entry->title = (string) $youtube_xml->title;
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
			// Show screen - first video loaded in javascript
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
			// return entry
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
												->or_where('playback_state', '=', 3)
												->order_by('created_at', 'asc')->paginate(50);
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

	// Skip the currently playing item
	public function action_delete_entry($entry_id)
	{
		DB::table('playlist_entries')->where('id', '=', $entry_id)->delete();
		return Redirect::back();
	}

}