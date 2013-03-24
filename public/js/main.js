function onYouTubePlayerReady(playerId)
{
	yt_player = document.getElementById('player_id');
	console.log('Playlist: Embedded video player ready');
	yt_player.setPlaybackQuality('highres');
	yt_player.addEventListener('onStateChange', 'onStateChangeHandler');
	yt_player.playVideo();
	console.log('Playlist: Playing first video');
}

var nowPlayingId;
var currentPlaybackState;

function pollPlaylist(firstCall)
{
	var url = 'http://localhost/playlist/get_entry';
	
	// Getting the first video ID for the embed code
	if(firstCall)
	{
		console.log('Playlist: Retreiving first entry');
		$.ajax({
			url: url,
			dataType: 'json',
			async: false,
	  		success: function(entry)
	  		{
				console.log('Playlist: Entry '+entry.id+' set as first video');
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
			console.log('Playlist: Polling for changes');
			// new entry, or entry skipped/deleted
			if(entry.id != nowPlayingId)
			{
				console.log('Playlist: Changing to entry: '+entry.id);
				loadAndPlayEntry(entry.id);
			}
			if(entry.playback_state != currentPlaybackState)
			{
				switch(entry.playback_state)
				{
					case 1: // playing
						console.log('Playlist: Entry '+entry.id+' setting state to "playing"');
						yt_player.playVideo();
						currentPlaybackState = 1;
						break;
					case 2: // paused
						console.log('Playlist: Entry '+entry.id+' setting state to "paused"');
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
		console.log('Playlist: Entry '+videoId+' loading and playing');
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

function markEntry(videoId, playbackState, playbackStateLabel)
{
	if(videoId)
	{
		console.log('Playlist: Entry '+videoId+' marking as '+playbackStateLabel+' ('+playbackState+')');
		$.get('http://localhost/playlist/mark_entry/'+videoId+'/'+playbackState, function(response) {
			console.log('Playlist: Entry '+videoId+' marking response: '+response+' rows affected ('+playbackStateLabel+')');
		});
	}
}

function onStateChangeHandler(newState) {
	switch(newState)
	{
		case -1:
			console.log('Playlist: Entry '+nowPlayingId+' is unstarted ('+newState+')');
			break;
		case 0:
			console.log('Playlist: Entry '+nowPlayingId+' has ended ('+newState+')');
			markEntry(nowPlayingId, 4, 'played'); // mark the last video as played
			break;
		case 1:
			console.log('Playlist: Entry '+nowPlayingId+' is now playing ('+newState+')');
			markEntry(nowPlayingId, 1, 'playing'); // mark the last video as playing
			currentPlaybackState = 1;
			break;
		case 2:
			console.log('Playlist: Entry '+nowPlayingId+' is now paused ('+newState+')');
			markEntry(nowPlayingId, 2, 'paused'); // mark the last video as paused
			currentPlaybackState = 2;
			break;
		case 3:
			console.log('Playlist: Entry '+nowPlayingId+' is now buffering ('+newState+')');
			break;
		case 5:
			console.log('Playlist: Entry '+nowPlayingId+' has been cued ('+newState+')');
			break;
	}
}
