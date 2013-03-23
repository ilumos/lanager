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

	public function action_screen()
	{ 
			// Show screen - first video loaded in javascript
			return View::make('playlist.screen')
						->with('title', 'Playlist Screen');
	}

	public function action_next($last_played_video_id = NULL)
	{
		if(!empty($last_played_video_id))
		{
			// mark the video that was last playing as played
			$mark_last_video_as_played = DB::table('playlist_entries')
											->where('id', '=', $last_played_video_id)
											->update(array('playback_state' => 2));
		}

		// get the currently playing video OR the next unplayed video
		$playlist_next_entry = LANager\Playlist_entry::where('playback_state', '=', 1)
													->or_where('playback_state', '=', 0)
													->order_by('playback_state', 'desc') // use now playing video if there is one
													->order_by('created_at', 'asc')
													->first();
		if(!empty($playlist_next_entry))
		{
			// mark it as "now playing"
			$mark_current_video_as_playing = DB::table('playlist_entries')
											->where('id', '=', $playlist_next_entry->id)
											->update(array('playback_state' => 1));
			// return its id
			return $playlist_next_entry->id;
		}
		else
		{
			return;
		}
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

}