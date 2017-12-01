<html>
<head>
<script src= "http://player.twitch.tv/js/embed/v1.js"></script>
<script src="https://embed.twitch.tv/embed/v1.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>

	setInterval(function(){
   		allStreamers();
	}, 300000);

	var count = 0;
	
	var live_streams = {};
	var windows = {};
	var streamCount = {};

	allStreamers();
	
	function allStreamers(){
	
		//var streamerz = ["jeffieblaze", "bergerbrush", "reiss_wolf", "imaqtpie", "shiphtur", "kinggothalion", "drlupo"];
		
		//localStorage.setItem("streamers",JSON.stringify(streamerz));
		
		jlist = localStorage.streamers;
	
		var streamers = JSON.parse(jlist);
		
		for(i = 0; i<streamers.length; i++){
			getStatus(streamers[i],i);	
		}
	
	}
	
	function getStatus(streamer,num){
		var status = "<span class=\"offline\">Offline</span>";
		var statusElement = document.querySelectorAll(".status")[0];
		var request = new XMLHttpRequest();
		var url = "https://api.twitch.tv/kraken/streams/" + streamer + "?client_id=ewvlchtxgqq88ru9gmfp1gmyt6h2b93&redirect_uri=http://b00stedgorilla.com";
		request.open('GET', url, true);
	
		request.onload = function() {
			if (this.status >= 200 && this.status < 400) {
		    		var data = JSON.parse(this.response);
	
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
		    			var player = windows[streamer];
		    			if(player.isPaused()){
		    				$("#" + live_streams[streamer]).remove();
		    	 			delete live_streams[streamer];
		    	 			delete windows[streamer];
		    	 			allStreamers(streamer, num);
		    			}
		    			streamCount[streamer] = streamCount[streamer] + 1;
		    			if(streamCount[streamer] > 5){
		    				$("#" + live_streams[streamer]).remove();
		    	 			delete live_streams[streamer];
		    	 			delete windows[streamer];
		    			}
		    			else{
		    				streamCount[streamer] = streamCount[streamer] + 1;
		    			}
		    		}
		    		else if(data.stream && !(streamer in windows)){
		    			streamCount[streamer] = streamCount[streamer] + 1;
		    			//do nothing we are just catching this. They are streaming but the player isnt ready and hasn't been stored yet
		    		}
		    		else{
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
	
	
	function playVideo(streamer, num){
	
		//var options = {
		//	width: 300,
		//	height: 400,
	 	//	channel: streamer,
		//};
		//var player = new Twitch.Player("twitch" + num, options);
		//player.setVolume(0.5);
		
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

	function lurk(){

		var streamerz = [];

		var arrayOfLines = $('#streamlurk').val().split('\n');
    	$.each(arrayOfLines, function(index, item) {
        	streamerz[index] = item;
    	});

    	localStorage.setItem("streamers",JSON.stringify(streamerz));
    	
    	//location.reload();

	}
	
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