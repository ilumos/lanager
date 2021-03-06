<?php

class Playlist_Controller extends Base_Controller {

	public function action_index() // display playlist & add entry form
	{
		// Get all enqueued playlist items
		$playlist_entries = LANager\Playlist_entry::where('playback_state', '=', 1) // playing
												->or_where('playback_state', '=', 0) // unplayed
												->or_where('playback_state', '=', 2) // paused
												->order_by('created_at', 'asc')->paginate(50);
		return View::make('playlist.show')
					->with('title', 'Playlist')
					->with('playlist_entries', $playlist_entries);
	}


	public function action_add_entry() // accept post data
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

		// Insert video id and metadata into database
		$playlist_entry = new LANager\Playlist_entry(array('video_id' => $youtube_url['v']));
		$playlist_entry->title = $response['entry']['title']['$t'];
		$playlist_entry->duration = $response['entry']['media$group']['yt$duration']['seconds'];
		$playlist_entry->user_id = Auth::user()->id;

		if( $playlist_entry->save() )
		{
			return Redirect::to_route('playlist');
		}
		else
		{
			return Redirect::back()->with('errors',$playlist_entry->errors->all());
		}
	}


	public function action_history() // Show playlist history
	{
		$playlist_entries = LANager\Playlist_entry::where('playback_state', '=', 4)
												->order_by('updated_at', 'desc')->paginate(50);
		return View::make('playlist.history')
					->with('title', 'Playlist')
					->with('playlist_entries', $playlist_entries);
	}


	public function action_screen() // Show playout screen (all videos loaded via js)
	{ 
		// calculate youtube player width from supplied resolution
		$player['width'] = floor((Config::get('lanager.playlist_screen_width')-Config::get('lanager.player_padding_horizontal')*2));
		$player['height'] = floor((Config::get('lanager.playlist_screen_height')-Config::get('lanager.player_padding_vertical')*2));

		return View::make('playlist.screen')
					->with('title', 'Playlist Screen')
					->with('player_dimensions', $player);
	}


	public function action_get_entry() 	// Get current playlist entry and its playback state
	{
		$playlist_entry = LANager\Playlist_entry::with('user') // no getter methods available :(
												->where('playback_state', '=', 2) // paused entries
												->or_where('playback_state', '=', 1) // playing entries
												->or_where('playback_state', '=', 0) // unplayed entries
												->order_by('playback_state', 'desc') // order: paused, playing, unplayed
												->order_by('created_at', 'asc') // secondary order: oldest to newest
												->first();
		if(!empty($playlist_entry))	return Response::eloquent($playlist_entry); // return playlist entry as JSON

	}


	public function action_mark_entry($entry_id,$playback_state) // Mark specified entry as playing/paused/played
	{
		return DB::table('playlist_entries')
					->where('id', '=', $entry_id)
					->update(array('playback_state' => $playback_state));
	}


	public function action_pause() // Pause the currently playing item
	{
		DB::table('playlist_entries')
			->where('playback_state', '=', 1) // playing
			->update(array('playback_state' => 2)); // set to paused
		return Redirect::back();
	}

	
	public function action_play() // Play the currently paused playlist entry
	{
		DB::table('playlist_entries')
			->where('playback_state', '=', 2) // paused
			->update(array('playback_state' => 1)); // set to playing
		return Redirect::back();
	}


	public function action_skip() // Skip the currently playing playlist entry
	{
		DB::table('playlist_entries')
			->where('playback_state', '=', 1) // playing
			->or_where('playback_state', '=', 2) // paused
			->update(array('playback_state' => 3)); // set to skipped
		return Redirect::back();
	}


	public function action_delete_entry($entry_id) // Delete the specified playlist entry
	{
		DB::table('playlist_entries')->where('id', '=', $entry_id)->delete();
		return Redirect::back();
	}


	public function action_vote_skip_entry($entry_id) // Skip the specified playlist entry
	{
		if($playlist_entry = LANager\Playlist_entry::find($entry_id))
		{
			// insert new user skip vote for this playlist entry
			$user_skip_vote = new LANager\Playlist_entry_user_skip_vote(array('user_id' => Auth::user()->id));
			$user_skip_vote = $playlist_entry->user_skip_votes()->insert($user_skip_vote);

			// check if total votes for this video exceeds required percentage of users
			$total_entry_skip_votes = LANager\Playlist_entry_user_skip_vote::where('playlist_entry_id', '=', $playlist_entry->id)->count();
			$total_users = LANager\User::count();
			$threshold_percent = Config::get('lanager.playlist_entry_vote_skip_threshold_percent');
			$skip_vote_percent = (100/$total_users)*$total_entry_skip_votes;
			
			if($skip_vote_percent >= $threshold_percent)
			{
				DB::table('playlist_entries')->where('id', '=', $entry_id)->update(array('playback_state' => 3));
			}

			return Redirect::back()->with('success',array('Your vote to skip this entry has been recorded.'));
		}
		else
		{
			return Response::error('404');
		}


	}


}