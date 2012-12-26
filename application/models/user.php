<?php

class User extends Eloquent {
	
	public function shout()
	{
	  return $this->has_many('Shout');
	}

}