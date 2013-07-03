<li class="dropdown{{ URI::is('profile/'.Auth::user()->id.'*') ? ' active' : '' }}">
	<a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown"><img src="{{Auth::user()->avatar_small}}" alt="Avatar"> {{e(Auth::user()->username)}} <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="{{URL::to_action('user@profile',array(Auth::user()->id))}}">Profile</a></li>
		<li><a href="{{URL::to_action('user@logout')}}">Log Out</a></li>
	</ul>
</li>