<?php
namespace LANager;
use Aware;

class User extends Aware {
	
	public function shouts()
	{
		return $this->has_many('LANager\Shout');
	}

	public function playlist_entries()
	{
		return $this->has_many('LANager\Playlist_entry');
	}


	public function roles()
	{
		return $this->has_many_and_belongs_to('LANager\Role', 'role_user');
	}

	public function has_role($key)
	{
		foreach($this->roles as $role)
		{
			if($role->name == $key)
			{
				return true;
			}
		}

		return false;
	}

	public function has_any_role($keys)
	{
		if( ! is_array($keys))
		{
			$keys = func_get_args();
		}

		foreach($this->roles as $role)
		{
			if(in_array($role->name, $keys))
			{
				return true;
			}
		}

		return false;
	}

	public function get_avatar_small()
	{
		return $this->get_attribute('avatar');
	}

	public function get_avatar_medium()
	{
		return str_replace('.jpg', '_medium.jpg', $this->get_attribute('avatar'));
	}

	public function get_avatar_large()
	{
		return str_replace('.jpg', '_full.jpg', $this->get_attribute('avatar'));
	}

}