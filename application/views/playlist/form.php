<?php

// Show post shout form to logged in users
if( Session::has('username') )
{
	echo Form::open('/shout/post');
	echo Form::token();
	echo Form::text('content',NULL,array('placeholder' => 'What\'s going on?', 'maxlength' => 140));

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

