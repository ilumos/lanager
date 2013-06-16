@layout('layouts/default')
@section('content')

<?php array_unshift($event_types, NULL); array_unshift($managers, NULL); ?>

<h3>New Event</h3>

{{ Form::open('events/create') }}
{{ Form::token() }}
{{ Form::label('name', 'Name') }}
{{ Form::text('name',NULL,array('placeholder' => 'e.g. Team Fortress 2', 'maxlength' => 200)) }}
<br>
{{ Form::label('details', 'Details') }}
{{ Form::textarea('details',NULL,array('placeholder' => 'Add more info', 'rows' => 4)) }}
<br>
{{ Form::label('type', 'Type') }}
{{ Form::select('type', $event_types) }}
<br>
{{ Form::label('manager', 'Manager') }}
{{ Form::select('manager', $managers) }}
<br>
<br>
{{ Form::submit('Create') }}

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