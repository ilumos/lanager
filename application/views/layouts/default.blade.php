<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>{{ e($title) }} :: LANager</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		{{ Asset::styles() }}
		{{ Asset::scripts() }}
		<script type="text/javascript">
			var siteUrl = '{{ URL::base(); }}';
		</script>
		<style>
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
		</style>
	</head>
	<body>
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->
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
							<li class="dropdown{{ URI::is('events*') ? ' active' : '' }}">
								<a href="#"
									class="dropdown-toggle"
									data-toggle="dropdown">
									Events
									<b class="caret"></b>
								</a> 
								<ul class="dropdown-menu">
									<li><a href="{{URL::to_action('event@timetable')}}">Timetable</a></li>
									<li><a href="{{URL::to_action('event@list')}}">List</a></li>
								</ul>
							</li>
							<li>
								<a href="ftp://files" target="_blank">Files</a>
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
							<li class="{{ URI::is('playlist*') ? 'active' : '' }}">
								<a href="{{URL::to_route('playlist');}}">Playlist</a>
							</li>
							<li class="{{ URI::is('info*') ? 'active' : '' }}">
								<a href="{{URL::to_route('info');}}">Info</a>
							</li>
						</ul>
						<ul class="nav pull-right">
						@if (isset($logged_in_user))
							@include('partials.logged_in')
						@else
							@include('partials.login')
						@endif
						</ul>
						

						<!--  -->
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>

		<div class="container">



			@yield('content')

			<hr>

			<footer>
				<p><a href="https://github.com/ilumos/LANager" target="_blank">LANager</a> is copyright &copy; {{date('Y')}} Oliver Baker and licensed under <a href="http://www.gnu.org/licenses/agpl-3.0.html" target="_blank">AGPLv3</a></p>
			</footer>
		</div> <!-- /container -->
	</body>
</html>
