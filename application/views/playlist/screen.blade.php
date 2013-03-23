@layout('layouts/default')
@section('content')
	<div id="player_display">
		You need Flash player 8+ and JavaScript enabled to view this video.
	</div>

	<script type="text/javascript">
		// Load first video
		getNextVideoId();

		var params = { allowScriptAccess: "always" };
		var atts = { id: "player_id" };
		swfobject.embedSWF(
			"http://www.youtube.com/v/"+nextVideoId+"?enablejsapi=1&playerapiid=ytplayer&version=3",
			"player_display", "1024", "800", "8", null, null, params, atts);
	</script>

@endsection