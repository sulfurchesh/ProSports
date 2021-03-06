<?php
/**
 * Sponsors Shortcode
 *
 * @author 		ProSports
 * @category 	Class
 * @package 	ProSports Sponsors
 * @version     1.0
 */
class SP_Shortcode_Sponsors {

	/**
	 * Constructor
	 */
	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		//add_filter( 'prosports_locate_template', array( $this, 'locate_template' ) );
	}

	/**
	 * Init shortcodes
	 */
	public static function init() {
		add_shortcode( 'sponsors', __CLASS__ . '::output' );
	}

	/**
	 * Output the sponsors shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		ob_start();

		echo '<div class="prosports">';
		sp_get_template( 'sponsors.php', $atts, 'sponsors', SP_SPONSORS_DIR . 'templates/' );
		echo '</div>';

		return ob_get_clean();
	}

	public static function locate_template( $template = null, $template_name = null, $template_path = null ) {
		if ( ! $template_path && $template_name == 'sponsors' ) {
			return SP_SPONSORS_DIR . '/templates/sponsors.php';
		}
	}
}

new SP_Shortcode_Sponsors();
