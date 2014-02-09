<?php
/*
Plugin Name: TheyWorkForYou for Wordpress
Plugin URI: http://philipjohn.me.uk/category/plugins/theyworkforyou/
Description: Provides tools for bloggers based on mySociety's TheyWorkForYou.com
Author: Philip John
Version: 0.1
Author URI: http://philipjohn.me.uk

Future features list;
 * Custom date format
 
*/
/*  Copyright 2009  Philip John Ltd  (email : talkto@philipjohn.co.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the Do What The Fuck You Want To Public License
    (WTFPL).

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

    You should have received a copy of the Do What The Fuck You Want To 
    Public License along with this program; if not, see
    http://wtfpl.net
*/

/**
 * Adds MPs_Recent_Activity widget.
 */
class MPs_Recent_Activity extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
				'mps_recent_activity', // Base ID
				__( 'MPs Recent Activity' ), // Name
				array( 'description' => __( 'A widget showing the latest activity from a Member of Parliament' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		
		if ( ! empty( $instance['mp'] ) ) {
			$xml = simplexml_load_file("http://www.theyworkforyou.com/api/getHansard?key=AMznwDBcpK3gCLwTTMC9PYHJ&output=xml&person=".$instance['mp']); // Load XML
			
			echo "<ul>\n";
			$i = 0; //counter for number of meetings
			foreach ( $xml->rows->match as $match ) {
				
				if ( $i >= $instance['limit'] ) // don't list more than X meetings
					break;
				
				$date = (string) $match->hdate;
				$date = strtotime($date);
				
				echo '<li>';
				
				if ( $instance['date'] == 1 ) {
					echo date( 'j M', $date ) . ': ';
				}
				
				$url = wp_kses( (string) $match->listurl );
				$url = esc_url( 'http://www.theyworkforyou.com' . $url );
				$body = wp_kses( (string) $match->parent->body );
				echo "<a href=\"$url\">$body</a>";
				
				if ( $instance['description'] == 1 ) {
					$description = wp_kses( (string) $match->extract );
					echo '<br/>' . $description;
				}
				
				echo '</li>' . "\n";
				
				$i++; //increment the counter
			}
			echo "</ul>\n";
			
			if ( $instance['link'] ) {
				// Link back to the MPs page on TWFY
				$MPurl = esc_url( (string) $xml->rows->match->speaker->url );
				echo '<p>More from <a href="http://www.theyworkforyou.com' . $MPurl . '">TheyWorkForYou.com</a></p>';
			}
		}
		
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		$defaults = array(
			'title' => __( 'MPs Recent Activity' ),
			'mp' => 10777,
			'description' => true,
			'date' => true,
			'limit' => 5,
			'link' => true,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		
		# Checkbox states
		$description = $instance['description'] ? 'checked="checked"' : '';
		$date = $instance['date'] ? 'checked="checked"' : '';
		$link = $instance['link'] ? 'checked="checked"' : '';
		
		$mps = $this->get_mps();
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'mp' ); ?>"><?php _e( 'Choose an MP:' ); ?></label> 
			<select id="<?php echo $this->get_field_id( 'mp' ); ?>" name="<?php echo $this->get_field_name( 'mp' ); ?>">
				<?php
					foreach ( $mps as $mid => $mname ) {
						echo '<option value="' . $mid . '"'
							. selected( $instance['mp'], $mid, false )
							. '>'. $mname . '</option>';
					}
				?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'How many items should be shown?' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo absint( $instance['limit'] ); ?>" size="3">
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php echo $description; ?> id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>">
			<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Show description?' ); ?></label>
			<br/>
			
			<input class="checkbox" type="checkbox" <?php echo $date; ?> id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>">
			<label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e( 'Show date?' ); ?></label>
			<br/>
			
			<input class="checkbox" type="checkbox" <?php echo $link; ?> id="<?php echo $this->get_field_id( 'link' ); ?>" name="<?php echo $this->get_field_name( 'link' ); ?>">
			<label for="<?php echo $this->get_field_id( 'link' ); ?>"><?php _e( 'Add a link to this MP on TheyWorkForYou?' ); ?></label>
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['mp'] = absint( $new_instance['mp'] );
		$instance['limit'] = absint( $new_instance['limit'] );
		$instance['description'] = $new_instance['description'] ? 1 : 0;
		$instance['date'] = $new_instance['date'] ? 1 : 0;
		$instance['link'] = $new_instance['link'] ? 1 : 0;

		return $instance;
	}
	
	function get_mps() {
		# @todo Add setting for API key
		$xml = simplexml_load_file('http://theyworkforyou.com/api/getMPs?key=AMznwDBcpK3gCLwTTMC9PYHJ&output=xml');
		
		$mps = array(); # for storage
		
		foreach ( $xml->match as $m ){
			$pid = (int) $m->person_id;
			$pname = (string) $m->name;
			$mps[$pid] = $pname;
		}
		
		asort($mps);
		
		return $mps;
	}

} // class MPs_Recent_Activity

// register MPs_Recent_Activity widget
function register_mps_recent_activity_widget() {
	register_widget( 'MPs_Recent_Activity' );
}
add_action( 'widgets_init', 'register_mps_recent_activity_widget' );


?>