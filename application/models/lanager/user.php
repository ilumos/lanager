<?php
namespace LANager;
use Aware;

class User extends Aware {
	
	public function shouts()
	{
	  return $this->has_many('LANager\Shout');
	}

}