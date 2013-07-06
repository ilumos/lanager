@layout('layouts/default')
@section('content')
<h3>Playlist</h3>
@include('playlist.form')<br>
@if(Authority::can('display','playlist_screen'))
	<a href="{{URL::to_action('playlist@screen')}}" target="_blank">Open Screen</a><br><br>
@endif
@if(Session::has('success'))
	@foreach(Session::get('success') as $success)

		<div class="alert alert-success">
			<a class="close" data-dismiss="alert" href="#">x</a>
			<strong>Success: </strong> {{$success}}
		</div>
	@endforeach
@endif
@include('playlist.list')
<br>
<a href="{{URL::to_action('playlist@history')}}">See History</a>
@endsection