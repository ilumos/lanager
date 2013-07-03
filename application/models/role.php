<?php
namespace LANager;
use Aware;

class Role extends Aware {

	public function users()
	{
		return $this->has_many_and_belongs_to('LANager\User');
	}

}