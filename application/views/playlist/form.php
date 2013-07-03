<?php

if( Authority::can('submit', 'playlist_entry') )
{
	echo Form::open('/playlist/add_entry');
	echo Form::token();
	echo Form::text('url',NULL,array('placeholder' => 'Paste a YouTube video URL here', 'id' => 'playlist_entry_url'));

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

