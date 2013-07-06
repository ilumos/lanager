var nowPlayingId;
var nowPlayingUniqueId;
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
	var url = siteUrl+'/playlist/get_entry';
	
	console.log('Playlist: Polling...');
	$.getJSON(url,function(entry)
	{
		if(entry)
		{
			// new entry, or entry skipped/deleted
			if(entry.video_id != nowPlayingId)
			{
				console.log('Playlist: Polling: Entry retrieved: '+entry.video_id+' (uid:'+entry.id+')');
				nowPlayingId = entry.video_id; // update now playing var
				nowPlayingUniqueId = entry.id; // set the unique video id
				loadEntry(entry.video_id); // load the new video
				updateNowPlaying(entry); // update now playing display
			}
			if(entry.playback_state != currentPlaybackState)
			{
				switch(entry.playback_state)
				{
					case 0: // unplayed
						console.log('Playlist: Polling: Staring playback of unplayed entry '+entry.video_id+' (uid:'+entry.id+')');
						yt_player.playVideo();
						currentPlaybackState = 1;
						break;				
					case 1: // playing
						console.log('Playlist: Polling: Staring playback of '+entry.video_id+' (uid:'+entry.id+')');
						yt_player.playVideo();
						currentPlaybackState = 1;
						break;
					case 2: // paused
						console.log('Playlist: Polling: Pausing playback of '+entry.video_id+' (uid:'+entry.id+')');
						yt_player.pauseVideo();
						currentPlaybackState = 2;
						break;
				}
			}
		}
		else
		{
			console.log('Playlist: Error polling - no response');
		}
	});
	setTimeout(pollPlaylist,2000);
}

// Load a video ID into the player
function loadEntry(videoId)
{
	console.log('Playlist: Entry '+videoId+' loading (uid:'+nowPlayingUniqueId+')');
	yt_player.loadVideoById(videoId);
	yt_player.setPlaybackQuality('highres'); // request best available quality
}

// Feed back a video's playback state to the database
function markEntry(uniqueVideoId, playbackState, playbackStateLabel, videoId)
{
	if(videoId)
	{
		$.get(siteUrl+'/playlist/mark_entry/'+uniqueVideoId+'/'+playbackState, function(response) {
			if(response == 1)
			{
				console.log('Playlist: Entry '+videoId+' (uid:'+uniqueVideoId+') marked as '+playbackStateLabel);
			}
			else
			{
				console.log('Playlist: Entry '+videoId+' (uid:'+uniqueVideoId+') already marked as '+playbackStateLabel);
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
			console.log('Playlist: Entry '+nowPlayingId+' (uid:'+nowPlayingUniqueId+') is unstarted ('+newState+')');
			break;
		case 0:
			console.log('Playlist: Entry '+nowPlayingId+' (uid:'+nowPlayingUniqueId+') has ended (4 / '+newState+')');
			markEntry(nowPlayingUniqueId, 4, 'played', nowPlayingId); // mark the last video as played
			break;
		case 1:
			console.log('Playlist: Entry '+nowPlayingId+' (uid:'+nowPlayingUniqueId+') is now playing ('+newState+')');
			markEntry(nowPlayingUniqueId, 1, 'playing', nowPlayingId); // mark the last video as playing
			currentPlaybackState = 1;
			break;
		case 2:
			console.log('Playlist: Entry '+nowPlayingId+' (uid:'+nowPlayingUniqueId+') is now paused ('+newState+')');
			// currentPlaybackState = 2; // removed due to YT player pausing just before video end and messing up script flow
			break;
		case 3:
			console.log('Playlist: Entry '+nowPlayingId+' (uid:'+nowPlayingUniqueId+') is now buffering ('+newState+')');
			break;
		case 5:
			console.log('Playlist: Entry '+nowPlayingId+' (uid:'+nowPlayingUniqueId+') has been cued ('+newState+')');
			break;
	}
}

// Display errors
function onErrorHandler(errorNum)
{
	console.log('Playlist: Player error: '+errorNum);
}

function updateNowPlaying(entry)
{
	console.log('Playlist: Updating now playing display');
	nowPlaying = '<strong>'+entry.title+'</strong><div class="pull-right">'+entry.user.username+' <img src="'+entry.user.avatar+'">';
	$('div#now_playing').html(nowPlaying);
}