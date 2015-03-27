<?php
/**
 * Statistic Equation
 *
 * @author 		ProSports
 * @category 	Admin
 * @package 	ProSports/Admin/Meta_Boxes
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'SP_Meta_Box_Equation' ) )
	include( 'class-sp-meta-box-equation.php' );

/**
 * SP_Meta_Box_Statistic_Equation
 */
class SP_Meta_Box_Statistic_Equation extends SP_Meta_Box_Equation {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$equation = get_post_meta( $post->ID, 'sp_equation', true );
		self::builder( $post->post_title, $equation, array( 'player_event', 'outcome', 'performance', 'metric' ) );
	}
}
