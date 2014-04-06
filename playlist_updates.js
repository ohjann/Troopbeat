// Define some variables used to remember state.
var playlistId, channelId;
var APILoaded = false;

// After the API loads, call a function to enable the playlist creation form.
function handleAPILoaded() {
  enableForm();
  APILoaded = true;
}

function isLoaded(){
    return APILoaded;
}

// Enable the form for creating a playlist.
function enableForm() {
  $('#playlist-button').attr('disabled', false);
  $('#search-button').attr('disabled', false);
}

// Create a private playlist.
function createPlaylist(description, callback) {
  var request = gapi.client.youtube.playlists.insert({
    part: 'snippet,status',
    resource: {
      snippet: {
        title: 'Troopbeat '+ description+' Playlist',
        description: 'A private playlist created using Blue Nation playlist generating app'
      },
      status: {
        privacyStatus: 'private'
      }
    }
  });
  request.execute(function(response) {
    var result = response.result;
    if (result) {
      playlistId = result.id;
      $('#playlist-id').html("playlist Id: "+playlistId);
      $('#playlist-title').html("<h1>"+result.snippet.title+"</h1>");
      $('#playlist-description').html(result.snippet.description);
    } else {
      $('#status').html('Could not create playlist');
      console.log('Could not create playlist');
    }
    console.log("callback about to be called, playlistId:"+playlistId);
    callback();
  });
}

// Add a video ID specified in the form to the playlist.
function addVideoToPlaylist() {
  addToPlaylist($('#video-id').val());
}

// Add a video to a playlist. The "startPos" and "endPos" values let you
// start and stop the video at specific times when the video is played as
// part of the playlist. However, these values are not set in this example.
function addToPlaylist(id, startPos, endPos) {

    console.log("playlistId: "+playlistId);
    console.log("--id id: "+id);
  var details = {
    videoId: id,
    kind: 'youtube#video'
  }
  if (startPos != undefined) {
    details['startAt'] = startPos;
  }
  if (endPos != undefined) {
    details['endAt'] = endPos;
  }
  var request = gapi.client.youtube.playlistItems.insert({
    part: 'snippet',
    resource: {
      snippet: {
        playlistId: playlistId,
        resourceId: details
      }
    }
  });

  request.execute(function(response) {
      //console.log(JSON.stringify(response.result, null, "\t"));
      //$('#status').html('<pre>' + JSON.stringify(response.result) + '</pre>');
  });
}

// Search for a specified string.
function search(query) {
  var q = query;
  //for(var i=0; i<q.length; i++){

  var id = "";
  var request = gapi.client.youtube.search.list({
    q: q,
    part: 'snippet'
  });

  request.execute(function(response) {
    var str = JSON.stringify(response.result, null, '\t');
    var tok = str.split('"');
    var i =0;
    for(i=0; i<tok.length; i++){
        if(tok[i]==="videoId"){
            id = tok[i+2];
            break;
        }
    }
    addToPlaylist(id);
  });
  //}
}

function embedPlaylist() {
    var id = playlistId;
    console.log("YOOOOOOO");
    $('#embed').html('<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed?listType=playlist&list=' + id + '" frameborder="0"/>');
    console.log("Id:" +id);
}
