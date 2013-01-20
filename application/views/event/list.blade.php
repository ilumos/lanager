@layout('layouts/default')
@section('content')
<h3>Events</h3>
	@foreach ($events->results as $event)
		<h4>{{$event->title}}</h4>
		Start: {{$event->start}}<br>
		End: {{$event->end}}<br>
		Details: {{$event->details}}<br>
		@if (is_object($event->manager))
			Manager: {{$event->manager->username}}<br>
		@endif
		Type: {{$event->type}}
		<br><br>
	@endforeach
	{{$events->links()}}
@endsection