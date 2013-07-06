<?php
namespace LANager;
use Aware;

class Playlist_entry_user_skip_vote extends Aware {

	/**
	* Aware validation rules
	*/
	public function user()
	{
		return $this->belongs_to('LANager\User');
	}

	public function playlist_entries()
	{
		return $this->belongs_to('LANager\Playlist_entry');
	}

}