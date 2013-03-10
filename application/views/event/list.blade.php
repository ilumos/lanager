@layout('layouts/default')
@section('content')
<h3>Events</h3>
@forelse ($events->results as $event)
	<h4>{{e($event->title)}}</h4>
	Start: {{$event->start}}<br>
	End: {{$event->end}}<br>
	Details: {{e($event->details)}}<br>
	@if (is_object($event->manager))
		Manager: {{e($event->manager->username)}}<br>
	@endif
	Type: {{e($event->type)}}
	<br><br>
@empty
	<p>There are no events scheduled.</p>
@endforelse
{{$events->links()}}
@endsection