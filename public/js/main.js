function onYouTubePlayerReady(playerId)
{
	yt_player = document.getElementById('player_id');
	console.log('Playlist: YouTube video player ready');
	yt_player.setPlaybackQuality('highres');
	yt_player.addEventListener('onStateChange', 'onStateChangeHandler');
	yt_player.playVideo();
	console.log('Playlist: Playing first video');
}

var nowPlayingId;
var currentPlaybackState;

function pollPlaylist(firstCall)
{
	console.log('Playlist: Polling for changes');
	var url = 'http://localhost/playlist/get_entry';
	
	// Getting the first video ID for the embed code
	if(firstCall)
	{
		$.ajax({
			url: url,
			dataType: 'json',
			async: false,
	  		success: function(entry)
	  		{
				console.log('Playlist: Polling success');
				console.log('Playlist: Setting nowPlayingId for the first time');
				nowPlayingId = entry.id;
			},
			error: function(response)
			{
				console.log('Playlist: Polling error: '+response);
			}
		});
	}
	else // Checking for the next video or pausing
	{
		$.getJSON(url,function(entry)
		{
			// new entry, or entry skipped/deleted
			if(entry.id != nowPlayingId)
			{
				console.log('Playlist: Changing video to: '+entry.id);
				loadAndPlayEntry(entry.id);
			}
			if(entry.playback_state != currentPlaybackState)
			{
				switch(entry.playback_state)
				{
					case 1: // playing
						console.log('Playlist: Setting state to "playing"');
						yt_player.playVideo();
						currentPlaybackState = 1;
						break;
					case 2: // paused
						console.log('Playlist: Setting state to "paused"');
						yt_player.pauseVideo();
						currentPlaybackState = 2;
						break;
				}
			}
		});
	}
	setTimeout(pollPlaylist,2000);
}


function loadAndPlayEntry(videoId)
{
	if(videoId)
	{
		console.log('Playlist: Loading and playing video: '+videoId);
		yt_player.cueVideoById(videoId);
		yt_player.setPlaybackQuality('highres');
		yt_player.playVideo();
		currentPlaybackState = 1;
		nowPlayingId = videoId;
		markEntry(videoId,1);
	}
	else
	{
		yt_player.stopVideo();
		currentPlaybackState = 4;
	}
}

function markEntry(videoId, playbackState)
{
	if(videoId)
	{
		console.log('Playlist: Marking entry: '+videoId+' as '+playbackState);
		$.get('http://localhost/playlist/mark_entry/'+videoId+'/'+playbackState, function(response) {
			console.log('Playlist: Marking response: '+response+' rows affected');
		});
	}
}

function onStateChangeHandler(newState) {
	switch(newState)
	{
		case -1:
			console.log('Playlist: Video unstarted ('+newState+')');
			break;
		case 0:
			console.log('Playlist: Video ended ('+newState+')');
			markEntry(nowPlayingId, 4); // mark the last video as played
			break;
		case 1:
			console.log('Playlist: Video playing ('+newState+')');
			markEntry(nowPlayingId, 1); // mark the last video as playing
			break;
		case 2:
			console.log('Playlist: Video paused ('+newState+')');
			markEntry(nowPlayingId, 2); // mark the last video as paused
			break;
		case 3:
			console.log('Playlist: Video buffering ('+newState+')');
			break;
		case 5:
			console.log('Playlist: Video cued ('+newState+')');
			break;
	}
}
