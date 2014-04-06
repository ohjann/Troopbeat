<?php

define('RAND_TRACKS', 15);

/**
 * Public class to generate a playlist based from a last fm user's playlist history
 *
 * An array in PHP is actually an ordered map, which means that they are multi-purpose
 *  Documentation of merging arrays is not detailed, but as arrays are implemented as hash maps,
 *  It can be assumed that merging is performed as an addition to a hash map, which is less complex
 *   than standard array merging
 *
 * @uses LastFM Unofficial Wrapper Class -> ./lastfm.api.class
 */
class PlayListGenerator {

	private $number_of_tracks; // int
	private $number_of_friends; // int
	private $number_of_songs; // int

	/**
	 * Constructor
	 */
	public function __construct (/* int */ $number_of_tracks, /* int */ $number_of_friends, /* int */ $number_of_songs) {
		$this->number_of_tracks = $number_of_tracks;
		$this->number_of_friends = $number_of_friends;
		$this->number_of_songs = $number_of_songs;
	} // end Constructor

	/**
	 * Public function to generate a random playlist based on a last fm
	 *  users play history
	 * 
	 * @return $randomTracks - Array of tracks
	 */
	public function generate_single_playlist (/* String */ $user) {
		$number = $this->number_of_tracks; // int
		$name = $user; // String
		// call to last fm api
		// will try limit this to one call only for a session
		/* array */ $tracks = User::getRecentTracks($name, $number); 
		if(!empty($tracks)) {
			$randomTracks = array();
			// remove duplicates
			array_unique($tracks, SORT_REGULAR);

			// choose a random subset of the tracks array
			foreach (array_rand($tracks, RAND_TRACKS) as $key) {
    			$randomTracks[] = $tracks[$key];
    		}
		}
		else throw new Exception("<h2>This user does not have any tracks!</h2>");
		return $randomTracks;
	} // end generate_single_playlist method

	/**
	 * Public function to generate a random playlist based on a group of last fm
	 *  users play histories
	 *
	 * @return $playlist_final - Array of tracks
	 */
	public function generate_rand_group_playlist(/* array */ $usernames) {
		$number = $this->number_of_tracks; // int
		$names = $usernames; // string
		$playlist = array();
		foreach ($names as $friend) {
			// call to last fm api
			// will try limit this to one call only for a session
			/* array */ $tracks = User::getRecentTracks($friend, $number);
			if(!empty($tracks)) {
				$randomTracks = array();
				// remove duplications 
				array_unique($tracks, SORT_REGULAR);

				// choose a random subset of the tracks array
				foreach (array_rand($tracks, RAND_TRACKS) as $key) {
		    		$randomTracks[] = $tracks[$key];
				}
				// merge the random tracks into our overall playlist
				$playlist = array_merge($playlist,$randomTracks);
			}
			else throw new Exception('<h2>This user does not have any tracks!</h2>');
		} // end foreach 

		// choose a random subset of the playlist of a all users
		$playlist_final = array();
		foreach (array_rand($playlist, RAND_TRACKS) as $key) {
    		$playlist_final[] = $playlist[$key];
		}

		return $playlist_final;
	} // end generate_rand_group_playlist method

	/**
	 * Public function to generate a playlist based on a group of last fm
	 *  users play histories and top artists
	 * @return $playlist_final - Array of tracks
	 */
	public function generate_group_playlist(/* array */ $usernames) {
		$number = $this->number_of_tracks; // int
		$names = $usernames; // string

		$playlist = array();
		foreach ($names as $friend) {
			// call to last fm api
			// will try limit this to one call only for a session

			/* array */ $top_artists = User::getTopArtists($friend, null, 5);
			$top_tracks = array();
			
			if(!empty($top_artists)) {
				foreach ($top_artists as $artist) {
					$top_tracks = User::getArtistTracks($friend, $artist->getName(), 10);
					$playlist = array_merge($playlist,$top_tracks);
				} // end foreach inner
			}
			else throw new Exception('<h2>User' . $friend->getName() . 'does not have any tracks!</h2>');
		} // end foreach	

		// choose a random subset of the playlist of a all users
		$playlist_final = array();
		// remove duplications 		
		array_unique($playlist,SORT_REGULAR);

		foreach (array_rand($playlist, RAND_TRACKS) as $key) {
    		$playlist_final[] = $playlist[$key];
		}
		
		return $playlist_final;
	} // end generate_group_playlist method


} // end PlayListGenerator class


/**************
 API PRE STUFF 
 **************/

require __DIR__ . "/src/lastfm.api.php";
define("API_KEY", "[YOUR API KEY HERE]");
define('ECHONEST_API_KEY', '[YOUR API KEY HERE]');
CallerFactory::getDefaultCaller()->setApiKey(API_KEY);

/******************
 END API PRE STUFF
 ******************/

echo "<h1> GENERATED PLAYLIST </h1>";

// grabbing the variable passed via javascript 
/* array */ $user = $_GET['user'];
/* String */ $choice = $_GET['choice'];

$gen = new PlayListGenerator(200,count($user),10);

switch ($choice) {
	case 'user': 
		try {
			$randomTracks = $gen->generate_single_playlist($user);
			echo "<ul>";
			foreach($randomTracks as $key => $track) {
			    echo "<li id='playlist'><div>";
	            //Playlist ID is set to hidden within CSS
			    echo "Track #" . ($key + 1) . " ";
			    echo "Title: <span id='title'>" . "`" . $track->getName() . "'" . "</span> ";
			    echo "Artist: <span id='artist'>" . $track->getArtist() . "</span>";
			    echo "</div></li>";
			} // end foreach
	        echo "</ul>";
	    }
    	catch(Exception $e) {
    		echo $e->getMessage();
    	}
    	break;
    
	case 'mood':
        $results = 10;
        $url = "http://developer.echonest.com/api/v4/playlist/static?api_key=" . ECHONEST_API_KEY . 
            "&mood=" . $user . "&type=artist-description&artist_min_familiarity=.5&sort=artist_familiarity-desc&song_type=studio";
        $json = json_decode(file_get_contents($url),true);

        echo "<ul>";
        foreach($json['response']['songs'] as $item) {
            echo "<li id='playlist'><div>";
            echo "Title: <span id='title'>" . $item['title'] . "</span>";
            echo "Artist: <span id='artist'>" . $item['artist_name'] . "</span>";
            echo "</div></li>";
        }   
        echo "</ul>";
        break;

	case 'group':
		try {
			// split the given array using '+' as a delimiter
			$user_array = explode("%2B", $user);
			foreach ($user_array as $user) {
				$s = $s . $user . " ";
			} 
			echo $s . "</h3>";

			$songs = $gen->generate_group_playlist($user_array);
			echo "<ul>";
			foreach($songs as $key => $track) {
			    echo "<li id='playlist'><div>";
	            //Playlist ID is set to hidden within CSS
			    echo "Track #" . ($key + 1) . " ";
			    echo "Title: <span id='title'>" . "`" . $track->getName() . "'" . "</span> ";
			    echo "Artist: <span id='artist'>" . $track->getArtist() . "</span>";
			    echo "</div></li>";
			} // end foreach
			echo "</ul>";
		}
	    catch(Exception $e) {
	    	echo $e->getMessage();
	    }
	    break;

	default:
		echo "<h2>Something went wrong! :(</h2>";
		break;
} // end switch

?>
