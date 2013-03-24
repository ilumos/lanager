var nowPlayingId;
var currentPlaybackState;

// Called when player loaded for first time
function onYouTubePlayerReady(playerId)
{
	yt_player = document.getElementById('player_id');
	console.log('Playlist: Embedded video player ready');
	pollPlaylist();
	yt_player.setPlaybackQuality('highres');
	yt_player.addEventListener('onStateChange', 'onStateChangeHandler');
	yt_player.addEventListener('onError', 'onErrorHandler');
}

// Poll database for next item or pausing
function pollPlaylist()
{
	var url = 'http://localhost/playlist/get_entry';
	
	console.log('Playlist: Polling...');
	$.getJSON(url,function(entry)
	{
		if(entry)
		{
			// new entry, or entry skipped/deleted
			if(entry.id != nowPlayingId)
			{
				console.log('Playlist: Polling: Entry retrieved: '+entry.id);
				nowPlayingId = entry.id; // update now playing var
				loadEntry(entry.id); // load the new video
			}
			if(entry.playback_state != currentPlaybackState)
			{
				switch(entry.playback_state)
				{
					case 0: // unplayed
						console.log('Playlist: Polling: Staring playback of unplayed entry');
						yt_player.playVideo();
						currentPlaybackState = 1;
						break;				
					case 1: // playing
						console.log('Playlist: Polling: Staring playback');
						yt_player.playVideo();
						currentPlaybackState = 1;
						break;
					case 2: // paused
						console.log('Playlist: Polling: Pausing playback');
						yt_player.pauseVideo();
						currentPlaybackState = 2;
						break;
				}
			}
		}
	});
	setTimeout(pollPlaylist,2000);
}

// Load a video ID into the player
function loadEntry(videoId)
{
	console.log('Playlist: Entry '+videoId+' loading');
	yt_player.loadVideoById(videoId);
	yt_player.setPlaybackQuality('highres'); // request best available quality
}

// Feed back a video's playback state to the database
function markEntry(videoId, playbackState, playbackStateLabel)
{
	if(videoId)
	{
		$.get('http://localhost/playlist/mark_entry/'+videoId+'/'+playbackState, function(response) {
			if(response == 1)
			{
				console.log('Playlist: Entry '+videoId+' marked as '+playbackStateLabel);
			}
			else
			{
				console.log('Playlist: Entry '+videoId+' already marked as '+playbackStateLabel);
			}
		});
	}
}

// Perform actions based on player's state changing, e.g. when last video stopped, load the next one
function onStateChangeHandler(newState) {
	if(!nowPlayingId)
	{
		nowPlayingId = '(empty)';
	}
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

// Display errors
function onErrorHandler(errorNum)
{
	console.log('Playlist: Player error: '+errorNum);
}