@forelse ($playlist_entries->results as $entry)
	<a href="{{URL::to_action('user@profile',$entry->user->id)}}"><img src="{{$entry->user->avatar_small}}" title="{{ e($entry->user->username) }}"></a>
	&nbsp;
	@if($entry->playback_state == 3)
		<em>(Skipped)</em>
	@endif
	{{ e($entry->title) }}
	@if(Config::get('lanager.playlist_screen_allowed_user') == Session::get('user_id'))
		<a href="{{URL::to_action('playlist@delete_entry',$entry->id)}}">X</a>
	@endif
	<br>
	
@empty
	<p>No entries to show!</p>
@endforelse

{{$playlist_entries->links()}}