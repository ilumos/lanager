@if (empty($playlist_entries->results))
	<p>No entries to show!</p>
@else
	@foreach ($playlist_entries->results as $entry)
		<a href="{{URL::to_action('user@profile',$entry->user->id)}}">
			<img src="{{$entry->user->avatar_small}}" title="{{ e($entry->user->username) }}">
		</a> 
		<a href="{{ e($entry->location) }}">{{ e($entry->title) }}</a><br>
	@endforeach
	{{$playlist_entries->links()}}
@endif