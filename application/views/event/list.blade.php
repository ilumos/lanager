@layout('layouts/default')
@section('content')
	@foreach ($events->results as $event)
		
		<h3>{{$event->title}}</h3><br>
		Start: {{$event->time_start}}<br>
		End: {{$event->time_end}}<br>
		Details: {{$event->details}}<br>
		Manager: {{$event->manager->username}}<br>
		Type: {{$event->type}}
		<br><br>
	@endforeach
	{{$events->links()}}
@endsection