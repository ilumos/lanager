@layout('layouts/screen')
@section('content')
	<style>

	</style>
	<div id="now_playing">

	</div>
	<div id="player_display">
		You need Flash player 8+ and JavaScript enabled to view this video.
	</div>



	<script type="text/javascript">
		var params = { allowScriptAccess: "always"};
		var atts = { id: "player_id" };
		swfobject.embedSWF(
			"http://www.youtube.com/apiplayer?version=3&enablejsapi=1&playerapiid=yt_player&iv_load_policy=3",
			"player_display", {{$player_dimensions['width']}}, {{$player_dimensions['height']}}, "8", null, null, params, atts);
	</script>
@endsection