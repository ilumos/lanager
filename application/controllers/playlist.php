<?php

class Playlist_Controller extends Base_Controller {

	public function action_index()
	{
		// Show playlist
		$playlist_entries = LANager\Playlist_entry::order_by('id', 'desc')->paginate(50);
		return View::make('playlist.show')
					->with('title', 'Playlist')
					->with('playlist_entries', $playlist_entries);
	}

	public function action_screen()
	{ 
		$playlist_first_entry = LANager\Playlist_entry::where('played', '=', 0)->first();
			return View::make('playlist.screen')
						->with('title', 'Playlist Screen')
						->with('playlist_first_entry', $playlist_first_entry);
	}

	public function action_next()
	{
		// Mark last video as played
		$playlist_last_entry = LANager\Playlist_entry::where('played', '=', 0)->first();
		
		if(!empty($playlist_last_entry))
		{
			$playlist_entry = LANager\Playlist_entry::find($playlist_last_entry->id);
			$playlist_entry->played = true;
			$playlist_entry->save();
			
			$playlist_next_entry = LANager\Playlist_entry::where('played', '=', 0)->first();
			if(!empty($playlist_next_entry))
			{
				return $playlist_next_entry->id;
			}
			else
			{
				return;
			}
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