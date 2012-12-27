@layout('layouts/default')
@section('content')
<h3>Popular Games</h3>
@if (!empty($error))
<p>{{$error}}</p>
@else
	@foreach ($games as $game)
	<a href="steam://store/{{$game['app_id']}}" title="View in the Steam Store"><img src="http://cdn.steampowered.com/v/gfx/apps/{{$game['app_id']}}/capsule_sm_120.jpg"></a>
		<span class="game_user_count">{{$game['user_count']}}</span>
		<a href="steam://store/{{$game['app_id']}}" title="View in the Steam Store"><span class="game_name">{{$game['name']}}</span></a>
		@foreach ($game['users'] as $user)
			<a href="{{URL::to_action('user@profile',array($user['id']))}}">{{$user['username']}}</a> &nbsp;
		@endforeach
	<br>
	@endforeach
@endif
@endsection