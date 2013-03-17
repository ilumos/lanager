@layout('layouts/default')
@section('content')
<h3>Playlist</h3>
@include('playlist.form')<br>
@include('playlist.list')
@endsection