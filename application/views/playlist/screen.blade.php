@layout('layouts/default')
@section('content')
@if (empty($playlist_first_entry))
	<p>Nothing to play!</p>
@else
  <div id="player_display">
    You need Flash player 8+ and JavaScript enabled to view this video.
  </div>

  <script type="text/javascript">

    var params = { allowScriptAccess: "always" };
    var atts = { id: "player_id" };
    swfobject.embedSWF("http://www.youtube.com/v/{{ e($playlist_first_entry->id) }}?enablejsapi=1&playerapiid=ytplayer&version=3",
                       "player_display", "800", "600", "8", null, null, params, atts);

  </script>
@endif
@endsection



