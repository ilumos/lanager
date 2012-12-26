@layout('layouts/default')
@section('content')
<h3>Shouts</h3>
@foreach ($shouts as $shout)
    <img src="{{$shout->user->avatar_small}}"> {{ $shout->user->username }}: {{ $shout->content }}.<br>
@endforeach
@endsection