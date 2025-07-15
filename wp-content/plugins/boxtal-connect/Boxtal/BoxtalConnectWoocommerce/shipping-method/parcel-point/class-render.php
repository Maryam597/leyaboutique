<?php
/**
 * Contains code for the parcel point render class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Rate_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Logger_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Frontend_Util;

/**
 * Render class.
 *
 * Adds relay map link if configured.
 */
class Render {

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'add_parcelpoint_choice' ), 10, 2 );
	}

	/**
	 * Add relay map link to shipping method choice.
	 *
	 * @param \WC_Shipping_Rate $shipping_rate shipping rate.
	 * @param string|int        $package_key key of package in cart.
	 * @return void
	 */
	public function add_parcelpoint_choice( $shipping_rate, $package_key ) {
		$shipping_rate_id = Shipping_Rate_Util::get_id( $shipping_rate );

		if ( Frontend_Util::is_selected_shipping_method( $shipping_rate_id ) ) {
			$label = Frontend_Util::get_parcel_point_label( $shipping_rate_id, $package_key );
			if ( null !== $label ) {
				echo wp_kses( $label, Frontend_Util::$label_allowed_html_tags );
			}
		}
	}
}
