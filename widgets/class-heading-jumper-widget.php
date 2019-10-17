<?php

/**
 * The file that defines the widget to display the output of the plugin
 *
 * @link       http://github.com/peterhsteele/heading-jumper
 * @since      1.0.0
 *
 * @package    heading-jumper
 * @subpackage heading-jumper/widgets
 */

/**
 * The widget class. Extends wp_widget.
 *
 * @since      1.0.0
 * @package    heading-jumper
 * @subpackage heading-jumper/widgets
 * @author     Peter Steele steele.peter.3@gmail.com
 */

class Heading_Jumper_Widget extends WP_Widget {

	/**
	* The class that generates the html for the widget front end
	* @since 1.0.0
	* @access private
	* @var Heading_Jumper_Public 	$heading_jumper_public  generates html for the widget
	*/

	private $heading_jumper_public;

	/**
	* The class for the plugin settings
	* @since 1.0.0
	* @access private
	* @var Heading_Jumper_Options 	$heading_jumper_options  retrieves plugin settings
	*/

	private $heading_jumper_options;

	/**
	*	Calls for dependencies to be loaded.
	*	@since 1.0.0
	*/


	public function __construct(){
		parent::__construct(
			'hj_widget',
			__( 'Heading Jumper Widget' , 'heading_jumper' ),
			array(
				'description' => 'add heading navigation for the current page' 
			)
		);
		$this->load_dependencies();
	}

	/**
	*	Loads dependencies for this widget
	*
	*	@since 1.0.0
	*/

	public function load_dependencies(){
		/*
		* Instance of the class responsible for public-facing code,
		* including front end output for the widget
		*/
		
		//require_once( dirname( __DIR__ , 1 ) . '/public/class-heading-jumper-public.php' );
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-heading-jumper-public.php';
		//require_once( dirname( __DIR__ , 1 ) . '/admin/class-heading-jumper-options.php' );
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-heading-jumper-options.php';
		
		$this->heading_jumper_options = new Heading_Jumper_Options( 'heading_jumper_public', '1.0.0' );
		$this->heading_jumper_public = new Heading_Jumper_Public( 'heading_jumper_public', '1.0.0', $this->heading_jumper_options->get_pages() );
	}

	/**
	*	Front end display. Echos the $args from the theme's register_sidebar(),
	*	then call public class' print_table_of_contents().
	*
	*	@since 1.0.0
	*
	*	@param array 	$args 		the sidebar settings defined by the theme.
	*	@param array 	$instance 	current instance of the widget
	*/
	
	public function widget( $args, $instance ){

		$table_of_contents = $this->heading_jumper_public->print_table_of_contents();
		
		if ( ! is_singular() || ! $table_of_contents  ){
			return;
		}
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $args['before_widget'];

		if ( ! empty($title) ){
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		echo $table_of_contents;
		echo $args['after_widget'];
		
	}

	/**
	*	Admin area interface. Allows user to define a title for front end.
	*
	*	@since 1.0.0
	*
	*	@param 	array 	$instance 	current instance of the widget
	*/

	public function form ( $instance ){
		
		if ( isset( $instance['title'] ) ){
			$title = $instance['title'];
		}
		else{
			$title = __( 'Sections', 'heading-jumper' );
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'heading-jumper' ); ?></label> 
			<input 
			type="text" 
			name="<?php echo $this->get_field_name( 'title' )?>" 
			value="<?php echo esc_attr( $title ) ?>"
			id="<?php $this->get_field_id( 'title' )?>" />
		</p>
		<?php
	}

	/**
	*	Allows user to update widget title
	*
	*	@since 	1.0.0
	*
	*	@param 	array 	$old_instance 	instance of widget before update
	*	@param 	array 	$new_instance 	new instance of widget generated by update
	*/

	public function update( $new_instance, $old_instance ){
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '' ;
		return $instance;
	}

}