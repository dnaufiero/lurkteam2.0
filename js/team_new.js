"use strict";
(function ($) {
    //runs the funtion to check what streams are live, 
    //updates which are displayed, etc, on a 5 minute interval

    var count = 0;
    var streamCount = {};
    var cooldownCount = {};
    var live_currently = [];
    var jlist;
    var i;
    var streamers = [];

    setInterval(function () {
        //localStorage.setItem("live_streams", JSON.stringify(live_streams));
        localStorage.setItem("streamCount", JSON.stringify(streamCount));
        localStorage.setItem("cooldownCount", JSON.stringify(cooldownCount));
        location.reload();
    }, 300000);

    allStreamers();

    //function which loops through the stream list to check which are live, etc
    function allStreamers() {

        init();
        storetext();
        getStatus(streamers);

    }

    function init(){

        if (!(localStorage.getItem("streamers") === null)) {
            jlist = localStorage.streamers;
            streamers = JSON.parse(jlist);
        }
        if (!(localStorage.getItem("streamCount") === null)) {
            streamCount = JSON.parse(localStorage.streamCount);
        }
        if (!(localStorage.getItem("cooldownCount") === null)) {
            cooldownCount = JSON.parse(localStorage.cooldownCount);
        }

    }

    //checks if a stream is live, among other things (see internal comments)
    function getStatus(streamers) {
        var streamlist = "";
        var comma = "";
        for (i = 0; i < streamers.length; i++) {
            streamlist = streamlist + comma + streamers[i];
            comma = ",";
        }
        if(streamlist != ""){
            var request = new XMLHttpRequest();
            var url = "https://api.twitch.tv/kraken/streams/?client_id=ewvlchtxgqq88ru9gmfp1gmyt6h2b93&redirect_uri=http://b00stedgorilla.com&channel=" + streamlist;
            request.open('GET', url, true);

            request.onload = function () {
                if (this.status >= 200 && this.status < 400) {
                    var data = JSON.parse(this.response);
                    var data = data.streams;
                    for (var i = 0, len = data.length; i < len; ++i) {
                    	//if(data[i].channel.name == 'reiss_wolf' || data[i].channel.name == 'Reiss_wolf'){
                    		//alert("no");
                    	//}
                    	//else{
                        var stream = data[i];
                        live_currently.push(stream.channel.name);
                        //}
                    }
                    for (i = 0; i < streamers.length; i++) {
                        var streamer = streamers[i];
                        var num = i;
                        //stream is live, stream is not playing currently.
                        if (live_currently.includes(streamer) && !(streamer in cooldownCount) && (!(streamer in streamCount))) {
                            $(".main").append("<div id='twitch" + num + "' class=\"stream-container\"></div>");
                            playVideo(streamer, num);
                            streamCount[streamer] = 0;
                        }
                        else if(live_currently.includes(streamer) && streamer in cooldownCount){
                            //catching cooldown
                            if(cooldownCount[streamer] > 0){
                                //remove cooldown window and add back video
                                $(".main").append("<div id='twitch" + num + "' class=\"stream-container\"></div>");
                                playVideo(streamer, num);
                                delete cooldownCount[streamer];
                            }
                            else{
                                $(".main").append("<div style='height:480px; width:320px; border-style:dotted;'><span>" + streamer + " on Cooldown</span></div>");
                                cooldownCount[streamer] = cooldownCount[streamer] + 1;
                            }
                        }
                        else if (live_currently.includes(streamer)) {
                            //stream is playing
                            //stream has be playing for 30 minutes. Remove it for the 5 minute cooldown
                            if (streamCount[streamer] > 5) {
                                $(".main").append("<div style='height:480px; width:320px; border-style:dotted;'><span>" + streamer + " on Cooldown</span></div>");
                                streamCount[streamer] = 0;
                                cooldownCount[streamer] = 0;
                            }
                            else {
                                //stream has been playing but not for 30 minutes, add 1 to the counter for that stream
                                $(".main").append("<div id='twitch" + num + "' class=\"stream-container\"></div>");
                                playVideo(streamer, num);
                                streamCount[streamer] = streamCount[streamer] + 1;
                            }
                        }
                        else {
                            //stream is offline, remove it from the live streams and delete the window
                            if(streamer in streamCount){
                                delete streamCount[streamer];
                            }
                            if(streamer in cooldownCount){
                                delete cooldownCount[streamer];
                            }
                            if(live_currently.includes(streamer)){
                                delete live_currently[live_currently.indexOf(streamer)];
                            }
                        }
                    }
                }
                else {
                    request.onerror;
                }
            };
            request.onerror = function () {
            };
            request.send();
        }
    }

    //embeds the twitch video into the created <td>, and adds the player object to an array when the embed is ready
    function playVideo(streamer, num) {

        var embed = new Twitch.Embed("twitch" + num, {
            width: 340,
            height: 480,
            channel: streamer,
            autoplay: true,
            muted: false,
            theme: 'dark'
        });

        embed.addEventListener(Twitch.Embed.VIDEO_READY, () => {
            var player = embed.getPlayer();
            player.setMuted(false);
            player.setVolume(0.1);
        });
    }

    //function called when pressing the lurk button. Updates the localStorage to be whatever is in the 'streamlurk' textarea
    function lurk() {
        if (!$('#streamlurk').val().length) {
            return;
        }
        var streamerz = [];

        var arrayOfLines = $('#streamlurk').val().split('\n');
        $.each(arrayOfLines, function (index, item) {
            streamerz[index] = item.toLowerCase();
        });

        localStorage.setItem("streamers", JSON.stringify(streamerz));
        location.reload();

        

    }

    //puts the localstorage data into the textarea upon pageload
    function storetext() {

        var enter = "";

        if (!(localStorage.getItem("streamers") === null)) {
                jlist = localStorage.streamers;
                streamers = JSON.parse(jlist);
        }

        for (var i = 0; i < streamers.length; i++) {
            document.getElementById("streamlurk").value += enter;
            document.getElementById("streamlurk").value += streamers[i].toLowerCase();
            enter = '\n';
        }
    }

    $('.settings').on('click', function(){
        if($(this).hasClass('active')) {
            $(".settings-drawer").animate({
                right: -454
            }, 300);
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
            $(".settings-drawer").animate({
                right: 0
            }, 700);
        }
    });

    $('button#lurkBtn').on('click', function(){
        lurk();
    });

})(jQuery);




