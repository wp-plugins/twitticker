<?php

function fetch_tweets($username, $maxtweets) {
/* By AcornArtwork, http://www.acornartwork.com/blog/2010/04/12/tutorial-twitter-rss-feed-parser-in-pure-php/ */
    
     //Using simplexml to load URL
     $tweets = simplexml_load_file("http://twitter.com/statuses/user_timeline/" . $username . ".rss");

     $tweet_array = array();  //Initialize empty array to store tweets
     foreach ( $tweets->channel->item as $tweet ) { 
          //Loop to limitate nr of tweets.
          if ($maxtweets == 0) {
               break;
          } else {
               $twit = $tweet->description;  //Fetch the tweet itself

               //Remove the preceding 'username: '
               $twit = substr(strstr($twit, ': '), 2, strlen($twit));

               // Convert URLs into hyperlinks
               $twit = preg_replace("/(http:\/\/)(.*?)\/([\w\.\/\&\=\?\-\,\:\;\#\_\~\%\+]*)/", "<a href=\"\\0\">\\0</a>", $twit);

               // Convert usernames (@) into links 
               $twit = preg_replace("(@([a-zA-Z0-9\_]+))", "<a href=\"http://www.twitter.com/\\1\">\\0</a>", $twit);

               // Convert hash tags (#) to links 
               $twit = preg_replace('/(^|\s)#(\w+)/', '\1<a href="http://search.twitter.com/search?q=%23\2">#\2</a>', $twit);

               //Specifically for non-English tweets, converts UTF-8 into ISO-8859-1
               $twit = iconv("UTF-8", "ISO-8859-15//TRANSLIT", $twit);

               //Get the date it was posted
               $pubdate = strtotime($tweet->pubDate); 
               //$propertime = gmdate('F jS Y, H:i', $pubdate);  //Customize this to your liking
               $propertime = gmdate('YmdHis', $pubdate);

               //Store tweet and time into the array
               $tweet_item = array(
                     'desc' => $twit,
                     'date' => $propertime,
               );
               array_push($tweet_array, $tweet_item);

               $maxtweets--;
          }
     }
     //Return array
     return $tweet_array;
}


/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
function timeSince($posted_date) {
    /* By Garrett Murray, http://graveyard.maniacalrage.net/etc/relative/ */
    $in_seconds = strtotime(substr($posted_date,0,8).' '.
                  substr($posted_date,8,2).':'.
                  substr($posted_date,10,2).':'.
                  substr($posted_date,12,2));
    $diff = time()-$in_seconds;
    $months = floor($diff/2592000);
    $diff -= $months*2419200;
    $weeks = floor($diff/604800);
    $diff -= $weeks*604800;
    $days = floor($diff/86400);
    $diff -= $days*86400;
    $hours = floor($diff/3600);
    $diff -= $hours*3600;
    $minutes = floor($diff/60);
    $diff -= $minutes*60;
    $seconds = $diff;
 
    if ($days>1) {
        // over two day old, just show date (mm/dd/yyyy format)
       return 'on '.substr($posted_date,4,2).'/'.substr($posted_date,6,2).'/'.substr($posted_date,0,4);
        
       } else {
    	
        if ($weeks>0) {
            // weeks and days
            $relative_date .= ($relative_date?', ':'').$weeks.' week'.($weeks>1?'s':'');
            $relative_date .= $days>0?($relative_date?', ':'').$days.' day'.($days>1?'s':''):'';
        } elseif ($days>0) {
            // days and hours
            $relative_date .= ($relative_date?', ':'').$days.' day'.($days>1?'s':'');
        } elseif ($hours>0) {
            // hours and minutes
            $relative_date .= ($relative_date?', ':'').$hours.' hour'.($hours>1?'s':'');
        } elseif ($minutes>0) {
            // minutes only
            $relative_date .= ($relative_date?', ':'').$minutes.' minute'.($minutes>1?'s':'');
        } else {
            // seconds only
            $relative_date .= ($relative_date?', ':'').$seconds.' second'.($seconds>1?'s':'');
        }

       
    }
    // show relative date and add proper verbiage
    
    return $relative_date.' ago';
}

?>
