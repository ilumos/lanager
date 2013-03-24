@layout('layouts/default')
@section('content')
<h3>Playlist History</h3>
	<a href="{{URL::to_action('playlist@index')}}">Back to playlist</a>
	<br>
	<br>
@include('playlist.list')
@endsection