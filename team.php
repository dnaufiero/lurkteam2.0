<html>
<head>
<script src= "http://player.twitch.tv/js/embed/v1.js"></script>
<script src="https://embed.twitch.tv/embed/v1.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>

	//runs the funtion to check what streams are live, 
	//updates which are displayed, etc, on a 5 minute interval
	setInterval(function(){
   		allStreamers();
	}, 300000);

	var count = 0;
	
	var live_streams = {};
	var windows = {};
	var streamCount = {};

	allStreamers();
	
	//function which loops through the stream list to check which are live, etc
	function allStreamers(){
	
		jlist = localStorage.streamers;
	
		var streamers = JSON.parse(jlist);
		
		for(i = 0; i<streamers.length; i++){
			getStatus(streamers[i],i);	
		}
	
	}
	
	//checks if a stream is live, among other things (see internmal comments)
	function getStatus(streamer,num){
		var status = "<span class=\"offline\">Offline</span>";
		var statusElement = document.querySelectorAll(".status")[0];
		var request = new XMLHttpRequest();
		var url = "https://api.twitch.tv/kraken/streams/" + streamer + "?client_id=ewvlchtxgqq88ru9gmfp1gmyt6h2b93&redirect_uri=http://b00stedgorilla.com";
		request.open('GET', url, true);
	
		request.onload = function() {
			if (this.status >= 200 && this.status < 400) {
		    		var data = JSON.parse(this.response);
	
					//stream is live, stream is not playing currently. Stream has either just gone live, or is in the 5 minute cooldown period
		    		if(data.stream && !(streamer in live_streams) && (!(streamer in streamCount) || (streamCount[streamer] > 1))){
		        		if(count == 0){
		        			$(".main").append("<tr>");
		        		}
		        		$(".main").append("<td id='twitch" + num + "'></td>");
		        		if(count == 4){
		        			$(".main").append("</tr>");
		        			count = 0;
		        		}
		        		else{
		        			count++;
		        		}
		        		live_streams[streamer] = "twitch" + num;
		        		playVideo(streamer,num);
		        		streamCount[streamer] = 0;
		    		}
		    		else if(data.stream && (streamer in windows)){
		    			//stream is playing, and player window has been saved (checks if video is paused/crashed[theoretically, hard to test a crash]), removes it and re-adds it if it is)
		    			var player = windows[streamer];
		    			if(player.isPaused()){
		    				$("#" + live_streams[streamer]).remove();
		    	 			delete live_streams[streamer];
		    	 			delete windows[streamer];
		    	 			allStreamers(streamer, num);
		    			}
		    			streamCount[streamer] = streamCount[streamer] + 1;
		    			//stream has be playing for 30 minutes. Remove it for the 5 minute cooldown
		    			if(streamCount[streamer] > 5){
		    				$("#" + live_streams[streamer]).remove();
		    	 			delete live_streams[streamer];
		    	 			delete windows[streamer];
		    			}
		    			else{
		    				//stream has been playing but not for 30 minutes, add 1 to the counter for that stream
		    				streamCount[streamer] = streamCount[streamer] + 1;
		    			}
		    		}
		    		else if(data.stream && !(streamer in windows)){
		    			//stream is live but is not playing, this is catching the 5 minute cooldown period
		    			streamCount[streamer] = streamCount[streamer] + 1;
		    		}
		    		else{
		    			//stream is offline, remove it from the live streams and delete the window
		    			$("#" + live_streams[streamer]).remove();
		    	 		delete live_streams[streamer];
		    		}
		  	} 
		  	else {
		  		request.onerror;
		  	}
		};
	
		request.onerror = function() {
		  //statusElement.innerHTML = "Cannot Retrieve Stream Status";
		};
		request.send();
	}
	
	//embeds the twitch video into the created <td>, and adds the player object to an array when the embed is ready
	function playVideo(streamer, num){
	
		var embed = new Twitch.Embed("twitch" + num, {
			width: 340,
		        height: 480,
		        channel: streamer
		});
		
		embed.addEventListener(Twitch.Embed.VIDEO_READY, () => {
        		var player = embed.getPlayer();
        		windows[streamer] = player;
      		});	
	}

	//function called when pressing the lurk button. Updates the localStorage to be whatever is in the 'streamlurk' textarea
	function lurk(){

		var streamerz = [];

		var arrayOfLines = $('#streamlurk').val().split('\n');
    	$.each(arrayOfLines, function(index, item) {
        	streamerz[index] = item;
    	});

    	localStorage.setItem("streamers",JSON.stringify(streamerz));
  
    	//Removed as we no longer want to reload the page
    	//location.reload();

	}
	
	//puts the localstorage data into the textarea upon pageload
	function storetext(){
	
		jlist = localStorage.streamers;
	
		var streamers = JSON.parse(jlist);
		var enter = "";

		for(i = 0; i<streamers.length; i++){
			document.getElementById("streamlurk").value += enter;
			document.getElementById("streamlurk").value += streamers[i];
			enter = '\n';
		}
	}
</script>
</head>
<body onload='storetext();'>
<h2> Lurk Team 2.0 </h2>
	<table class='main'>
	</table>
	<textarea id='streamlurk' name='streamlurk' cols=50 rows=20></textarea>
	<button onclick="lurk()">Lurk Away!</button>
</body>
</html>