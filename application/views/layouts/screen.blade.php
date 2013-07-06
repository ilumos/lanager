@include('partials.header')
<style>
	body {
		padding-top: 10px;
		padding-bottom: 10px;
	}
	.container {
		margin: 20px;
	}
	div#now_playing {
		width: {{$player_dimensions['width']}}px;
	}
</style>
@include('partials.content')