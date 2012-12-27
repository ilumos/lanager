@layout('layouts/default')
@section('content')
<h3>Shouts</h3>
@include('shout.form')<br>
@foreach ($shouts as $shout)
    <img src="{{$shout->user->avatar_small}}"> {{ e($shout->user->username) }}: {{ e($shout->content) }}<br>
@endforeach
@endsection