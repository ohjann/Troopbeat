<!doctype html>
<head>
  <meta charset="utf-8">
  <title>TroopBeat</title>
  <meta name="description" content="Welcome to my basic template.">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
</head>

<body>


    <div id="wrap">
    	
        <div id="core" class="clearfix">

		    <!-- show loading image when opening the page -->
		    <a href="index.html"><img width="213px" height="111px" id="loading_spinner" src="loading.gif"/></a>
            <div>
                <div id="playlist-title">
                </div>
                <div id="embed">
                </div>
		    </div>
	        <div id="error_msg"> </div>
		    <div id="content">
		    </div>

       	</div>
			
    </div>      
            
<!-- SCRIPTS -->
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>
<script src="auth.js"></script>
<script src="playlist_updates.js"></script>
<script src="https://apis.google.com/js/client.js?onload=googleApiClientReady"></script>

<script type="text/javascript">

    $(window).load(function(){
        /* In this case var user can be both the mood and also the user */
        var logoimage = $('<img />').attr('src', 'logo.png');
   		$('#loading_spinner').show();
        var pathArray = window.location.href.split('=');
        var description = "";
        if(pathArray[0].indexOf("user") == -1){
            /*  If there is no "user" in the url i.e. base playlist of mood   */
            user = pathArray[1];  
            choice= "mood";
            description = user.charAt(0).toUpperCase() + user.substring(1); // Capitalise first letter
        }
        else if(pathArray[1].indexOf("%2B") != -1){
            /* If the "+" character is there i.e.  more than one user */
            user = pathArray[1];
            choice = "group"
            description = user+"'s Taste";
            description = description.replace(/%2B/g,', '); // Strips "+" encoding from description
        }
        else {
            user = pathArray[1];  
            choice = "user";
            description = user+"'s Taste";
        }
   		$.ajax({
		    type:'GET',
		    url: 'playlist_generator.php',
		  	data: { user: user, choice: choice},
		    success:function(data){
		        $('#content').html(data);
		    },
                complete: function(){
                    function checkAPI(){
                        /* Wait for API to load before executing script */
                        var loaded = isLoaded();
                        if(loaded==true){
                            console.log("API Loaded");
                            makePlaylist();
                        }
                        else{
                            console.log("waiting...");
                            setTimeout(function(){ checkAPI() }, 500);
                        }
                    }
                    function makePlaylist(){
                        createPlaylist(description,function(){
                            var query = new Array();
                            var index = 0;
                            $('ul span:even').each(function(){
                                console.log($(this).text()+" + "+$(this).next().text());
                                query[index] = $(this).text()+" "+$(this).next().text();
                                index++;
                            });
                            var i =0;
                            for(i; i<(query.length-1); i++){
                                setTimeout(function(x) { 
                                    return function() { 
                                        search(query[x]); 
                                    };
                                }(i), i*500);
                                // Due to API constraints have to wait 0.5s before each call
                            }
                            i++;
                            // One more iteration and then embed playlist
                            setTimeout(function(x) { 
                                return function() { 
                                    search(query[x]); 
                                    embedPlaylist();
                                    $('#loading_spinner').attr("src","logo.png");
                                };
                            }(i), i*500);
                        });
                    }
                    checkAPI();
		    },
		    error: function() {
		    	 $('#error_msg').append('Sorry, something went wrong: ' + textStatus + ' (' + errorThrown + ')');
		    }
        });
	});
</script>

</body>

</html>

