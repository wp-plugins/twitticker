<?php
/*
Plugin Name: Twitticker
Plugin URI: http://www.omjsmultimedia.nl
Description: Show your tweets with a javascript newsticker.
Version: 0.1.4
Author: Giel vd Berg
Author URI: http://omjsmultimedia.nl/profiel/Giel/

*** Credits *** 
Fetch tweets: By AcornArtwork, http://www.acornartwork.com/blog/2010/04/12/tutorial-twitter-rss-feed-parser-in-pure-php/
Datetime like twitter: By Garrett Murray, http://graveyard.maniacalrage.net/etc/relative/

*/

include_once('includes/twitter.php');

/* Default settings */

if (!defined('TWITTICKER_PLUGIN_NAME'))
    define('TWITTICKER_PLUGIN_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));

if (!defined('TWITTICKER_PLUGIN_URL'))
    define('TWITTICKER_PLUGIN_URL', WP_PLUGIN_URL . '/' . TWITTICKER_PLUGIN_NAME);

if (!defined('TWITTICKER_VERSION_KEY'))
    define('TWITTICKER_VERSION_KEY', 'TWITTICKER_version');

if (!defined('TWITTICKER_VERSION_NUM'))
    define('TWITTICKER_VERSION_NUM', '0.0.1');

add_option(TWITTICKER_VERSION_KEY, TWITTICKER_VERSION_NUM);


/* Add our function to the widgets_init hook. */
add_action( 'widgets_init', 'load_twitticker_widgets' );
  
/* Function that registers our widget. */
function load_twitticker_widgets() {
	register_widget( 'twitticker_widget' );
}

class twitticker_widget extends WP_Widget {
  
	function twitticker_widget() {
		/* Widget settings. */
  		$widget_ops = array( 'classname' => 'twitticker_widget', 'description' => __( 'Add Twitticker' ) );
    	/* Widget control settings. */
  		$control_ops = array( 'width' => 150, 'height' => 350, 'id_base' => 'twitticker_widget' );
    	/* Create the widget. */
  		$this->WP_Widget( 'twitticker_widget', 'Twitticker widget', $widget_ops, $control_ops );
  	}
  	
  	/* The widget */
	function widget( $args, $instance ) {
		extract($args, EXTR_SKIP);
		echo $before_widget;
		echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		/* Get the tweets */
		$mytweets = fetch_tweets($instance['twitterUsername'], $instance['shownTweets']);
    	echo '<div id="ticker" class="newsTicker">';
   		echo '<ul>';
			foreach ($mytweets as $k => $v) {
	   			echo '<li>';
	   			echo '<p class="tweet"> ' . html_entity_decode($v['desc']) . '</p>';
	   			echo '<span class="date">' . timeSince($v['date']) . '</span>';
	   			echo '</li>';
			}
   		echo '</ul>';
   		echo '</div>';
		echo $after_widget;
    }
    
    /* Function for widgets options */
	function form( $instance ) {
		if ( $instance ) {
			$title 				= esc_attr( $instance[ 'title' ] );
			$twitterUsername 	= esc_attr( $instance[ 'twitterUsername' ] );
			$shownTweets 		= esc_attr( $instance[ 'shownTweets' ] );
		}
		else {
			$title 				= __( 'My Tweets' );
			$twitterUsername 	= 'GielvdBerg';
			$shownTweets 		= '10';
		}
	?>
		
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('twitterUsername'); ?>"><?php _e('Twitter username:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('twitterUsername'); ?>" name="<?php echo $this->get_field_name('twitterUsername'); ?>" type="text" value="<?php echo $twitterUsername; ?>" />
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id('shownTweets'); ?>"><?php _e('Shown tweets:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('shownTweets'); ?>" name="<?php echo $this->get_field_name('shownTweets'); ?>" type="text" value="<?php echo $shownTweets; ?>" />
		</p>
		
	<?php 
	}
	
	/* Function for update widget settings */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
	
		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['twitterUsername'] = strip_tags( $new_instance['twitterUsername'] );
		$instance['shownTweets'] = strip_tags( $new_instance['shownTweets'] );
	
		return $instance;
	}
  
}

/* Add javascript and CSS to header */
add_action('wp_print_scripts', 'Twitticker_mainhead');
function Twitticker_mainhead(){

	if (function_exists('wp_enqueue_script')) {

		if(is_front_page() || is_singular() || is_archive()){
		
		 	echo '<link type="text/css" rel="stylesheet" href=" '. TWITTICKER_PLUGIN_URL .'/style/twitticker.css "';
		 	
			wp_deregister_script( 'twitticker_jquery' );
		    wp_register_script('twitticker_jquery', TWITTICKER_PLUGIN_URL . '/js/jquery.min.js');
		    wp_enqueue_script( 'twitticker_jquery' );
			
			wp_deregister_script( 'twitticker_newsticker' );
		    wp_register_script('twitticker_newsticker', TWITTICKER_PLUGIN_URL . '/js/newsticker.js');
		    wp_enqueue_script( 'twitticker_newsticker' );
	
		}

	}

}
          

?>