<?php
namespace LANager;
use Aware;

class Playlist_entry extends Aware {

	/**
	* Aware validation rules
	*/
	// public static $rules = array(
	// 	'content' => 'required|max:140',
	// );
	public static $table = 'playlist_entries';

	public function user()
	{
		return $this->belongs_to('LANager\User');
	}

}