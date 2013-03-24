@forelse ($playlist_entries->results as $entry)
	<a href="{{URL::to_action('user@profile',$entry->user->id)}}"><img src="{{$entry->user->avatar_small}}" title="{{ e($entry->user->username) }}"></a>
	&nbsp;
	{{ e($entry->title) }}<br>
	
@empty
	<p>No entries to show!</p>
@endforelse

{{$playlist_entries->links()}}