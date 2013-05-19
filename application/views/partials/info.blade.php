<li class="dropdown {{ URI::is('info*') ? 'active' : '' }}">
	<a href="#"
		class="dropdown-toggle"
		data-toggle="dropdown">
		Info
		<b class="caret"></b>
	</a> 
	<ul class="dropdown-menu">
	@if (!empty($children))
		@foreach($children as $child)
			<li><a href="{{URL::to_action('info@display',$child->id)}}">{{e($child->title)}}</a></li>
		@endforeach
	@endif
	</ul>
</li>