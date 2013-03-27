@layout('layouts/default')
@section('content')

<h3>Create Event</h3>

{{ Form::open('/event/create') }}
{{ Form::token() }}
{{ Form::label('title', 'Title') }}
{{ Form::text('title',NULL,array('placeholder' => 'Event title', 'maxlength' => 200)) }}
<br>
{{ Form::label('type', 'Type') }}
{{ Form::select('type', $event_types) }}
<br>
{{ Form::label('details', 'Details') }}
{{ Form::textarea('details',NULL,array('placeholder' => 'Some details', 'rows' => 4)) }}
<br>
{{ Form::label('manager', 'Manager') }}
{{ Form::select('manager', $managers) }}
<br>
{{ Form::submit('Create Event') }}

@if(Session::has('errors'))
	@foreach(Session::get('errors') as $error)
		<div class="alert alert-error">
			<a class="close" data-dismiss="alert" href="#">x</a>
			<strong>Error: </strong>{{$error}}
		</div>
	@endforeach
@endif

{{ Form::close() }}
@endsection