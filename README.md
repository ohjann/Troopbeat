#Troopbeat - Playlist Creation Application
Troopbeat is a web application which allows users to generate a YouTube playlist based off their current mood or their LastFM play history.

A (broken) live demo can be seen at [ohjann.netsoc.ie/Troopbeat](http://ohjann.netsoc.ie/Troopbeat).

##How to set up

`git clone https://github.com/Ohjann/Troopbeat.git`

Things you'll need:

- LastFM API Key (playlist_generator.php line 141)
- Echonest API Key (playlist_generator.php line 142)
- Google OAuth2 Client ID (auth.js line 5)

Details of how to aquire a LastFM API Key can be found at [www.last.fm/api](http://www.last.fm/api).
</br>Details of how to aquire an Echonest API Key can be found at [developer.echonest.com](http://developer.echonest.com/).

To aquire the required OAuth2 Client ID from Google you must first create a project at [console.developers.google.com](https://console.developers.google.com). Once created, click on your project name and navigate to the "Credentials" submenu within "APIs & auth" menu. Click the "Create New Client ID", keep "Web application" selected and enter your site hosting your code as the "Authorized Javascript Origins". A Client ID should then be created for you which you can copy and paste into the appropriate place in the auth.js file. If you are having trouble with playlist generation try deleteing all entries in "Redirect URIs".

Once all appropriate API Keys are inserted the application should work.

Code relies on Javascript so obviously you will have to disable any NoScript addons you may have for it to work correctly.

The LastFM API calls are made through an ‘unofficial’ PHP-specific wrapper class, as detailed by LastFm at http://www.last.fm/api/downloads. The wrapper class can be found here http://lastfm.felixbruns.de/php-last.fm-api/. This module is a requirement for the PHP code to function correctly, as the API calls are made through this, server-side. The documentation for the wrapper class details how the XML is parsed and returned as objects with the data hidden away inside these objects. This module is included in the code provided.
<br/><br/>

##Known Issues

YouTube playlist generation relies on the YouTube search API call, and is based off the presumption that when searching for the name of an artist and the song name, more often than not the first result in that search will be a video containing (at the very least) the audio for that track. For more obscure artists or tracks this video fetching can be inaccurate.

Troopbeat has been tested on the current latest version of Google Chrome (33.0) but not for other web browsers. Problems with YouTube embed have been noticed when using alternative browsers. We believe the reason for this is due to problems with the YouTube embed code rather than problems with our own code, but further investigation is needed. Playlist is still generated in the user’s YouTube playlist page regardless, so they do have the option of playing playlist directly from YouTube should this occur.

Due to restrictions in the YouTube API, it sometimes happens that our application attempts to insert videos too quickly into the playlist, which causes some of the video insert API calls to “miss” and the corresponding video not inserted. A timeout is used to stagger the API calls (playlist.php line 82) so that they are fired only once every half second. Increasing this time slows the application but increases accuracy. We tried to find the “sweet spot” between speed and accuracy and found that a half second wait works the vast majority of the time without the application feeling too slow.

