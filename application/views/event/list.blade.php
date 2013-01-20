@layout('layouts/default')
@section('content')
	@foreach ($events->results as $event)
		<h3>{{$event->title}}</h3>
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