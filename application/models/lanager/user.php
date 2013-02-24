<?php
namespace lanager;
use Aware;

class User extends Aware {
	
	public function shouts()
	{
	  return $this->has_many('lanager\Shout');
	}

}