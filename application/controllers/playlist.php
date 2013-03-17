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


}