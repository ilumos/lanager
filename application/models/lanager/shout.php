<?php
namespace LANager;
use Aware;

class Shout extends Aware {

	/**
	* Aware validation rules
	*/
	public static $rules = array(
		'content' => 'required|max:140',
	);

	public function user()
	{
		return $this->belongs_to('LANager\User');
	}

}