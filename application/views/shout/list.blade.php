@if (!is_array($shouts))
	<p>No shouts to show!</p>
@else
	@foreach ($shouts->results as $shout)
		<img src="{{$shout->user->avatar_small}}">
		<a href="{{URL::to_action('user@profile',$shout->user->id)}}">{{ e($shout->user->username) }}</a>:
		{{ e($shout->content) }}<br>
	@endforeach
	{{$shouts->links()}}
@endif