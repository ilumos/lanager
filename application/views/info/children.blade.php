@if (!empty($children))
	@foreach($children as $child)
		<a href="{{URL::to_action('info@display',$child->id)}}">{{e($child->title)}}</a><br>
	@endforeach
@endif