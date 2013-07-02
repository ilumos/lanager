<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Initialize User Permissions Based On Roles
	|--------------------------------------------------------------------------
	|
	| This closure is called by the Authority\Ability class' "initialize" method
	|
	*/

	'initialize' => function($user)
	{
		// If a user doesn't have any roles, we don't have to give him permissions so we can stop right here.
		if(count($user->roles) === 0) return false;

		if($user->has_role('admin'))
		{
			Authority::allow('skip', 'playlist_entry');
			Authority::allow('delete', 'playlist_entry');
			Authority::allow('submit', 'shout');
		}

		if($user->has_role('attendee'))
		{
			Authority::allow('submit', 'playlist_entry');
			Authority::allow('submit', 'shout');
		}
	}

);