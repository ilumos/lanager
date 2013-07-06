<?php
namespace LANager;
use Aware;

class Playlist_entry extends Aware {

	/**
	* Aware validation rules
	*/
	public static $rules = array(
		'title' => 'required',
	);

	public static $table = 'playlist_entries';

	public function user()
	{
		return $this->belongs_to('LANager\User');
	}

	public function user_skip_votes()
	{
		return $this->has_many('LANager\Playlist_entry_user_skip_vote');
	}

}