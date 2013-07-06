<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<a class="brand" href="/"><img src="{{ URL::base(); }}/img/logo.png" alt="LANager Logo"></a>

			<div class="nav-collapse collapse">
				<ul class="nav">
					<li class="{{ URI::is('events*') ? 'active' : '' }}">
						<a href="{{URL::to_route('events');}}">Events</a>
					</li>
					<li class="{{ URI::is('files*') ? 'active' : '' }}">
						<a href="{{URL::to_route('files');}}">Files</a>
					</li>
					<li class="{{ URI::is('shouts*') ? 'active' : '' }}">
						<a href="{{URL::to_route('shouts');}}">Shouts</a>
					</li>
					<li class="{{ URI::is('games*') ? 'active' : '' }}">
						<a href="{{URL::to_route('games');}}">Games</a>
					</li>
					<li class="{{ URI::is('servers*') ? 'active' : '' }}">
						<a href="#">Servers</a>
					</li>
					<li class="{{ URI::is('people*') ? 'active' : '' }}">
						<a href="{{URL::to_route('people');}}">People</a>
					</li>
					@if(Authority::can('display','playlist_screen'))
						<li class="dropdown{{ URI::is('playlist*') ? ' active' : '' }}">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">Playlist <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="{{URL::to_route('playlist');}}">Playlist</a></li>
								<li><a href="{{URL::to_action('playlist@screen');}}">Screen</a></li>
							</ul>
						</li>
					@else							
						<li class="{{ URI::is('playlist*') ? 'active' : '' }}">
							<a href="{{URL::to_route('playlist');}}">Playlist</a>
						</li>
					@endif
					@include('partials.info')

				</ul>
				<ul class="nav pull-right">
				@if (Auth::check())
					<li class="dropdown{{ URI::is('profile/'.Auth::user()->id.'*') ? ' active' : '' }}">
						<a href="#" class="dropdown-toggle user-menu" data-toggle="dropdown"><img src="{{Auth::user()->avatar_small}}" alt="Avatar"> {{e(Auth::user()->username)}} <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="{{URL::to_action('user@profile',array(Auth::user()->id))}}">Profile</a></li>
							<li><a href="{{URL::to_action('user@logout')}}">Log Out</a></li>
						</ul>
					</li>
				@else
					<a href="{{ $login_button }}"><img class="pull-right steam-signin" src="{{URL::base();}}/img/sits_small.png"></a>
				@endif
				</ul>
				

				<!--  -->
			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>