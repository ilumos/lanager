@layout('layouts/default')
@section('content')
<h3>Shouts</h3>
@include('shout.form')<br>
@include('shout.list')
@endsection