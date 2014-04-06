#Troopbeat - Playlist Creation Application
Troopbeat is a web application which allows users to generate a YouTube playlist based off their current mood or their LastFM play history.

A live demo can be seen at [ohjann.netsoc.ie/bbranch](http://ohjann.netsoc.ie/bbranch).

##How to set up

`git clone https://github.com/Ohjann/Troopbeat.git`

Things you'll need:

- LastFM API Key (playlist_generator.php line 141)
- Echonest API Key (playlist_generator.php line 142)
- Google OAuth2 Client ID (auth.js line 5)

Details of how to aquire a LastFM API Key can be found at [www.last.fm/api](http://www.last.fm/api).
</br>Details of how to aquire an Echonest API Key can be found at [developer.echonest.com](http://developer.echonest.com/).

To aquire the required OAuth2 Client ID from Google you must first create a project at [console.developers.google.com](https://console.developers.google.com). Once created, click on your project name and navigate to the "Credentials" submenu within "APIs & auth" menu. Click the "Create New Client ID", keep "Web application" selected and enter your site hosting your code as the "Authorized Javascript Origins". A Client ID should then be created for you which you can copy and paste into the appropriate place in the auth.js file. If you are having trouble with playlist generation try deleteing all entries in "Redirect URIs".

Once all appropriate API Keys are inserted the application should work. <br/>Enjoy!
