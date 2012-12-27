<li class="dropdown pull-right">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="{{$logged_in_user_avatar_small}}"> {{$logged_in_user}} <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="{{URL::to_action('user@profile',array(Session::get('user_id')))}}">Profile</a></li>
		<li><a href="{{URL::to_action('user@preferences')}}">Preferences</a></li>
		<li><a href="{{URL::to_action('user@logout')}}">Log Out</a></li>
	</ul>
</li>