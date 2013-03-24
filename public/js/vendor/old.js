/* Youtube Screen */

var nextVideoId;

function getNextVideoId() {
	lastVideoId = nextVideoId
	if(lastVideoId)
	{
		console.log('Playlist: Sending last played video ID to be marked as played: '+lastVideoId);
	}
	console.log('Playlist: Retrieving next video ID');
	jQuery.ajax({
		url:    'http://localhost/playlist/get_next_entry/'+lastVideoId, // pass last played video id for marking as played
		success: function(result) {
			nextVideoId = result;
			console.log('Playlist: Success - retrieved next video ID: '+nextVideoId);
		},
		error: function(result) {
			console.log('Playlist: Error - could not retrieve next video ID:'+result);
			yt_player.stopVideo();
		},
		async:   false
	});          
}


function onYouTubePlayerReady(playerId) {
	yt_player = document.getElementById("player_id");
	console.log('Playlist: YouTube video player ready');
	yt_player.setPlaybackQuality('highres');
	yt_player.addEventListener("onStateChange", "onStateChangeHandler");
	console.log('Playlist: Beginning playback');
	yt_player.playVideo();
	pollPlaybackState();
}



function onStateChangeHandler(newState) {
	console.log('Playlist: Player\'s state changed: ' + newState);
	if(newState == 0) // Video ended
	{
		advancePlaylist();
	}
}

function advancePlaylist()
{
	getNextVideoId();
	enqueueNextVideo(nextVideoId);
}

function enqueueNextVideo(videoId) {
	if(videoId)
	{
		console.log('Playlist: Loading next video: '+videoId);
		yt_player.cueVideoById(videoId);
		yt_player.setPlaybackQuality('highres');
		yt_player.playVideo();
	}
	else
	{
		yt_player.stopVideo();
	}
}

function pollPlaybackState()
{
	videoId = nextVideoId;
	$.get('http://localhost/playlist/get_playback_state/'+videoId, function(playbackState) {
		console.log('Playlist: Polling playback state: '+playbackState);

		switch(playbackState)
		{
			case 3: 
				yt_player.pauseVideo();
			break;
			case 4:
				advancePlaylist();
			break;
			default:
		}
		setTimeout(pollPlaybackState,2000);
	});
}