<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>LANager :: {{ $title }}</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width">
		{{ Asset::styles() }}
		{{ Asset::scripts() }}
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
					<a class="brand" href="/"><img src="{{ URL::base(); }}/img/logo.png"></a>
					<div class="nav-collapse collapse">
						<ul class="nav">
							<li><a href="events">Events</a></li>
							<li><a href="ftp://files">Files</a></li>
							<li><a href="shouts">Shouts</a></li>
							<li><a href="servers">Servers</a></li>
							<li><a href="people">People</a></li>
							<li><a href="music">Music</a></li>
							<li><a href="info">Info</a></li>
						</ul>
						@if (isset($logged_in_user))
							@include('partials.logged_in')
						@else
							@include('partials.login')
						@endif
						<!--  -->
					</div><!--/.nav-collapse -->
				</div>
			</div>
		</div>

		<div class="container">

			@yield('content')

			<hr>

			<footer>
				<p>Footer</p>
			</footer>
		</div> <!-- /container -->
	</body>
</html>
