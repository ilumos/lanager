@layout('layouts/default')
@section('content')
<h3>Playlist</h3>
@include('playlist.form')<br>
@if(Config::get('lanager.playlist_screen_allowed_user') == Session::get('user_id'))
	@include('playlist.controls')
@endif
@include('playlist.list')
<br>
<a href="{{URL::to_action('playlist@history')}}">See History</a>
@endsection