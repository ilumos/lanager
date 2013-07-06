<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Initialize User Permissions Based On Roles
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Authority\Ability class' "initialize" method
	|
	*/

	'initialize' => function($user)
	{
		// If a user doesn't have any roles, we don't have to give him permissions so we can stop right here.
		if(count($user->roles) === 0) return false;

		if($user->has_role('attendee'))
		{
			Authority::allow('submit', 'playlist_entry');
			Authority::allow('submit', 'shout');
			
			// attendees can vote to skip a video if they haven't already voted on the video before
			Authority::allow('vote_skip', 'playlist_entry', function($playlist_entry) use ($user)
			{
				// passed id instead of object
				if(is_numeric($playlist_entry)) $playlist_entry = LANager\Playlist_entry::where('id', '=', $playlist_entry)->first();

				$this_user_skip_votes = LANager\Playlist_entry_user_skip_vote::where('playlist_entry_id', '=', $playlist_entry->id)
																	->where('user_id', '=', $user->id)
																	->count();
				return ($this_user_skip_votes == 0);
			});

			// attendees can delete their own submitted playlist entries
			Authority::allow('delete', 'playlist_entry', function($playlist_entry) use ($user)
			{
				// passed id instead of object
				if(is_numeric($playlist_entry)) $playlist_entry = LANager\Playlist_entry::where('id', '=', $playlist_entry)->first();

				return ($playlist_entry->user_id == $user->id);
			});

		}

		if($user->has_role('admin'))
		{
			Authority::allow('skip', 'playlist_entry');
			Authority::allow('delete', 'playlist_entry');
			Authority::allow('control', 'playlist_playback');

			Authority::allow('create', 'event');

		}

		if($user->has_role('playlist_screener'))
		{
			Authority::allow('display', 'playlist_screen');
		}
		
	}

);