@layout('layouts/default')
@section('content')
	@foreach ($users->results as $user)
		<img src="{{$user->avatar_small}}">
		<a href="{{URL::to_action('user@profile',$user->id)}}">{{ e($user->username) }}</a>
		<br>
	@endforeach
	{{$users->links()}}
@endsection