<?php
/**
 * Contains code for environment util class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
 */

namespace Boxtal\BoxtalConnectWoocommerce\Util;

use Boxtal\BoxtalConnectWoocommerce\Plugin;

/**
 * Environment util class.
 *
 * Helper to check environment.
 */
class Environment_Util {

	/**
	 * Get warning about PHP version, WC version.
	 *
	 * @param Plugin $plugin plugin object.
	 * @return string $message
	 */
	public static function check_errors( $plugin ) {

		if ( version_compare( PHP_VERSION, $plugin['min-php-version'], '<' ) ) {
			/* translators: 1) int version 2) int version */
			$message = __( '%1$s - The minimum PHP version required for this plugin is %2$s. You are running %3$s.', 'boxtal-connect' );
			return sprintf( $message, 'Boxtal Connect', $plugin['min-php-version'], PHP_VERSION );
		}

		if ( ! defined( 'WC_VERSION' ) ) {
			/* translators: 1) Plugin name */
			return sprintf( __( '%s requires WooCommerce to be activated to work.', 'boxtal-connect' ), 'Boxtal Connect' );
		}

		if ( version_compare( WC_VERSION, $plugin['min-wc-version'], '<' ) ) {
			/* translators: 1) Plugin name 2) minimum woocommerce version 3) current woocommerce version */
			$message = __( '%1$s - The minimum WooCommerce version required for this plugin is %2$s. You are running %3$s.', 'boxtal-connect' );

			return sprintf( $message, 'Boxtal Connect', $plugin['min-wc-version'], WC_VERSION );
		}
		return false;
	}
}
