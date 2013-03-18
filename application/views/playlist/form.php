<?php

// Show add video form to logged in users
if( Session::has('username') )
{
	echo Form::open('/playlist/add_entry');
	echo Form::token();
	echo Form::text('url',NULL,array('placeholder' => 'Paste a YouTube video URL here', 'maxlength' => 240));

	if(Session::has('errors'))
	{
		foreach(Session::get('errors') as $error)
		{
			echo '
			<div class="alert alert-error">
				<a class="close" data-dismiss="alert" href="#">x</a>
				<strong>Error: </strong>'.$error.'
			</div>
			';
		}
	}
}

