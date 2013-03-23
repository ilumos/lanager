/* Youtube Screen */

var nextVideoId;

function getNextVideoId() {
	console.log('Playlist: Retrieving next video ID');
	jQuery.ajax({
		url:    'http://localhost/playlist/next',
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
}



function onStateChangeHandler(newState) {
	console.log('Playlist: Player\'s state changed: ' + newState);
	if(newState == 0) // Video ended
	{
		getNextVideoId();
		enqueueNextVideo(nextVideoId);
	}
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