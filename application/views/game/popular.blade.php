@layout('layouts/default')
@section('content')
<h3>Popular Games</h3>
@forelse ($games as $game)
	<a href="steam://store/{{$game['app_id']}}" title="View in the Steam Store"><img src="http://cdn.steampowered.com/v/gfx/apps/{{$game['app_id']}}/capsule_sm_120.jpg"></a>
		<span class="game_user_count">{{$game['user_count']}}</span>
		<a href="steam://store/{{$game['app_id']}}" title="View in the Steam Store"><span class="game_name">{{e($game['name'])}}</span></a>
		@foreach ($game['users'] as $user)
			<a href="{{URL::to_action('user@profile',array($user['id']))}}">{{e($user['username'])}}</a> &nbsp;
		@endforeach
	<br>
@empty
	<p>Nobody's playing any games! What kind of LAN party is this!?</p>
@endforelse
@endsection