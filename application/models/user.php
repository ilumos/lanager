<?php

class User extends Aware {
	
	public function shouts()
	{
	  return $this->has_many('Shout');
	}

}