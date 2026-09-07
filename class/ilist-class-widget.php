<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined( 'ABSPATH' ) or die( 'Are you crazy!' );

// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
//           ICustomizer Widget
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
class ilist_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
			// widget ID
			'ilist_wg',
			// widget name
			__( 'Recent lists', ILIST_ID_LANGUAGES ),
			// widget description
			array('description' => __( 'Last lists', ILIST_ID_LANGUAGES ) )
		);
    }

	public function widget($args, $instance) {
		global $wpdb;
		
		echo $args['before_widget'];
		echo $args['before_title'];
		echo apply_filters('widget_title', $instance['title']);
		echo $args['after_title'];
		
		$limit = ($instance['list_number'] > 0) ? $instance['list_number'] : 5;
		
		$lists = $wpdb->get_results( "SELECT id, name, keyaccess FROM " . ILIST_TBL_MAIN . " WHERE active = '1' ORDER BY date DESC LIMIT $limit" );
		
		if ($lists) {
            $ilist_url_complete = ilist_get_option( 'ilist_url_complete' );
			foreach ( $lists as $list ) {
                echo '<a href="' . $ilist_url_complete . '?a=liste&k=' . $list->keyaccess . '">';
				echo $list->name;
				echo '</a>';
				echo '<br>';
			}
		} else {
			echo '<em style="color: darkgrey;">' . __( 'No list available', ILIST_ID_LANGUAGES ) . '</em>';
		}
		
		echo $args['after_widget'];
	}

	public function form($instance) {
		$title       = isset($instance['title']) ? $instance['title'] : '';
		$list_number = isset($instance['list_number']) ? $instance['list_number'] : 5;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', ILIST_ID_LANGUAGES ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'list_number' ); ?>"><?php _e( 'List number to display:', ILIST_ID_LANGUAGES ); ?></label>&nbsp;
			<input style="width: 70px;" class="widefat" id="<?php echo $this->get_field_id( 'list_number' ); ?>" name="<?php echo $this->get_field_name( 'list_number' ); ?>" type="number" value="<?php echo esc_attr($list_number); ?>" />
		</p>
		<?php
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']       = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['list_number'] = ( ! empty( $new_instance['list_number'] ) ) ? strip_tags( $new_instance['list_number'] ) : '';
		return $instance;
	}

}

add_action('widgets_init', function( ) {
	register_widget('ilist_Widget');
});
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
?>