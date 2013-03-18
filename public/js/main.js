/* Youtube Screen */

function onYouTubePlayerReady(playerId) {
	yt_player = document.getElementById("player_id");
	console.log('YouTube video player ready!');
	yt_player.setPlaybackQuality('highres');
	yt_player.playVideo();
	yt_player.addEventListener("onStateChange", "onStateChangeHandler");
}

function onStateChangeHandler(newState) {
	console.log("Player's new state: " + newState);
	if(newState == 0) // Video ended
	{
		$.get('http://localhost/playlist/next', function(data) {
		enqueueNextVideo(data);
	});		

	}
}

function enqueueNextVideo(videoId) {
	if(videoId)
	{
		console.log('Loading next video: '+videoId);
		yt_player.cueVideoById(videoId);
		yt_player.setPlaybackQuality('highres');
		yt_player.playVideo();
	}
	else
	{
		yt_player.stopVideo();
	}
}