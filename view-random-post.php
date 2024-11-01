<?php
/**
 * Plugin Name: View Random Post
 * Plugin URI: http://www.phpurchase.com/plugins
 * Description: Click a link to read a random post on your WordPress site
 * Version: 1.0
 * Author: PHPoet
 * Author URI: http://www.phpoet.com
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/
 
function rpost_load() {
  register_widget('View_Random_Post');
}
add_action('widgets_init', 'rpost_load');

function popper() {
  if(isset($_GET['task']) && $_GET['task'] == 'randompost') {
    
    // Initial session variable to hold viewed posts
    if(!isset($_SESSION['randomposts'])) {
      $_SESSION['randomposts'] = array();
    }
    
    // Get total number of published posts
    $count_posts = wp_count_posts();
    $published_posts = $count_posts->publish;
    
    // Check to see if all the posts have been viewed already
    if(count($_SESSION['randomposts']) >= $published_posts) {
      // Reset the the array if all the posts have already been viewed once
      $_SESSION['randomposts'] = array();
    }
    
    // Select a random post excluding previously viewed posts
    if(count($_SESSION['randomposts'])) {
      $exclude = implode(',', $_SESSION['randomposts']);
    }
    
    $rand_posts = get_posts('numberposts=1&orderby=rand&exclude=' . $exclude);
    $post = $rand_posts[0];

    // Check to see if this random post has been read yet
    if(!in_array($post->ID, $_SESSION['randomposts'])) {
      $_SESSION['randomposts'][] = $post->ID;
      $url = get_permalink($post->ID);
    }
    
    /*
    echo "<pre>";
    echo "viewed posts: " . count($_SESSION['randomposts']) . "\n";
    echo "published posts: " . $published_posts . "\n";
    print_r($_SESSION['randomposts']);
    echo "</pre>";
    */
    
    header("Location: $url");
    exit;
  }
}
add_action('init', 'popper');

class View_Random_Post extends WP_Widget {
  
  /**
   * Setup the random post widget
   */
  function View_Random_Post() {
    /* Widget settings. */
		$widget_ops = array( 'classname' => 'view_random_post', 
		                     'description' => __('Click a link to read a random post on your blog.', 'view_random_post') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'view-random-post' );

		/* Create the widget. */
		$this->WP_Widget('view-random-post', __('View Random Post', 'view_random_post'), $widget_ops, $control_ops );
  }
  
  /**
	 * How to display the widget on the screen.
	 */
	function widget($args, $instance) {
		extract($args);

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$link_text = $instance['link_text'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if($title)
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */
		if($link_text)
			echo "<p><a href='?task=randompost'>$link_text</a></p>";

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and title to remove HTML */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['link_text'] = $new_instance['link_text'];

		return $instance;
	}
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array('title' => __('View Random Post', 'view_random_post'), 'link_text' => __('Click here to read a random post', 'view_random_post'));
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'view_random_post'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:98%;" />
		</p>

		<!-- Your Name: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link_text' ); ?>"><?php _e('Link:', 'view_random_post'); ?></label>
			<input id="<?php echo $this->get_field_id( 'link_text' ); ?>" name="<?php echo $this->get_field_name( 'link_text' ); ?>" value="<?php echo $instance['link_text']; ?>" style="width:98%;" />
		</p>

	<?php
	}
  
}
