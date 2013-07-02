<?php
namespace LANager;
use Aware;

class Event extends Aware {

	/**
	* Aware validation rules
	*/
	public static $rules = array(
		'name' => 'required|max:32',
		'description' => 'required',
		'start' => 'required',
		'end' => 'required',
	);


     public function type()
     {
          return $this->has_one('LANager\Event_type');
     }

     public function manager()
     {
          return $this->belongs_to('LANager\User');
     }


}