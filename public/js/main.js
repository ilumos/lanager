function onYouTubePlayerReady(playerId) {
	ytplayer = document.getElementById("player_id");
	console.log('YouTube video player ready!');
	ytplayer.playVideo();
  	ytplayer.addEventListener("onStateChange", "onStateChangeHandler");
}

function onStateChangeHandler(newState) {
	console.log("Player's new state: " + newState);
	if(newState == 0)
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
		ytplayer.cueVideoById(videoId);
		ytplayer.playVideo();
	}
	else
	{
		ytplayer.stopVideo();
	}
}