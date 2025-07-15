<?php
/**
 * Contains code for environment util class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Util
 */

namespace Boxtal\BoxtalConnectWoocommerce\Util;

/**
 * Frontend util class.
 *
 * Helper to handle frontend data among woocommerce legacy and block.
 */
class Frontend_Util {

	/**
	 * List of allowed html tags for parcel point label escaping
	 *
	 * @var array
	 */
	public static $label_allowed_html_tags = array(
		'span'  => array(
			'class'            => true,
			'data-branding'    => true,
			'data-package_key' => true,
		),
		'br'    => array(),
		'small' => array( 'class' => true ),
	);

	/**
	 * Nonce action name used for setting parcel point
	 *
	 * @var string
	 */
	public static $set_point_action = 'bw_set_point_action';

	/**
	 * Nonce action name used for getting parcel points
	 *
	 * @var string
	 */
	public static $get_points_action = 'bw_get_points_action';

	/**
	 * Nonce action name used for getting shipping method extra label
	 *
	 * @var string
	 */
	public static $get_shipping_method_extra_label_action = 'bw_get_shipping_method_extra_label_action';


	/**
	 * Get map url
	 *
	 * @return string $map_url
	 */
	public static function get_map_url() {
		$map_token_url = Auth_Util::get_map_token_url();
		$url           = null;

		if ( null !== $map_token_url ) {
			$token = Shipping_Api_Util::get_map_token( $map_token_url );
			if ( null !== $token ) {
				$url = str_replace( '${access_token}', $token, get_option( 'BW_MAP_BOOTSTRAP_URL' ) );
			}
		}

		return $url;
	}

	/**
	 * Get parcel points.
	 *
	 * @param string $shipping_rate_id shipping rate id.
	 * @return mixed
	 */
	public static function get_shipping_method_parcel_points( $shipping_rate_id ) {
		$parcel_points = null;

		$networks = Shipping_Rate_Util::get_shipping_method_networks( $shipping_rate_id );

		if ( ! empty( $networks ) ) {
			$address       = self::get_recipient_address();
			$parcel_points = Shipping_Api_Util::get_parcel_points( self::get_recipient_address(), $networks );
		}

		return $parcel_points;
	}

	/**
	 * Get recipient address.
	 *
	 * @return array recipient address
	 */
	public static function get_recipient_address() {
		$customer = Customer_Util::get_customer();
		return array(
			'street'  => trim( Customer_Util::get_shipping_address_1( $customer ) . ' ' . Customer_Util::get_shipping_address_2( $customer ) ),
			'city'    => trim( Customer_Util::get_shipping_city( $customer ) ),
			'zipCode' => trim( Customer_Util::get_shipping_postcode( $customer ) ),
			'country' => strtolower( Customer_Util::get_shipping_country( $customer ) ),
		);
	}

	/**
	 * Get parcel points.
	 *
	 * @param array      $address recipient address.
	 * @param int        $shipping_rate_id shipping rate id.
	 * @param string|int $package_key package key in cart.
	 * @return boolean
	 */
	public static function init_points( $address, $shipping_rate_id, $package_key ) {
		$network_parcel_points = self::get_shipping_method_parcel_points( $shipping_rate_id );
		$chosen_point          = self::get_chosen_point( $shipping_rate_id, $package_key );
		$has_parcel_points     = false;

		if ( null !== $network_parcel_points ) {
			if ( ! self::is_point_in_response( $network_parcel_points->nearbyParcelPoints, $chosen_point ) ) {
				self::reset_chosen_points( $package_key );
			}

			if ( count( $network_parcel_points->nearbyParcelPoints ) > 0 ) {
				$has_parcel_points = true;
			}
		}

		return $has_parcel_points;
	}

	/**
	 * Get closest parcel point.
	 *
	 * @param string     $shipping_rate_id shipping rate id.
	 * @param string|int $package_key package key.
	 * @return mixed
	 */
	public static function get_closest_point( $shipping_rate_id, $package_key ) {
		$network_parcel_points = self::get_shipping_method_parcel_points( $shipping_rate_id );
		$closest_parcel_point  = null;

		if ( null !== $network_parcel_points && count( $network_parcel_points->nearbyParcelPoints ) > 0 ) {
			$closest_parcel_point = Parcelpoint_Util::normalize_parcelpoint( $network_parcel_points->nearbyParcelPoints[0] );
		}

		return $closest_parcel_point;
	}

	/**
	 * Get chosen parcel point.
	 *
	 * @param string     $shipping_rate_id shipping rate id.
	 * @param string|int $package_key package key.
	 * @return mixed
	 */
	public static function get_chosen_point( $shipping_rate_id, $package_key ) {
		$chosen_parcel_point = null;

		if ( WC()->session ) {
			$chosen_parcel_point = WC()->session->get( 'bw_chosen_parcel_point_' . $package_key . '_' . Shipping_Rate_Util::get_clean_id( $shipping_rate_id ), null );
			$chosen_parcel_point = Parcelpoint_Util::normalize_parcelpoint( $chosen_parcel_point );
		}

		return $chosen_parcel_point;
	}

	/**
	 * Reset chosen parcel point.
	 *
	 * @param string|int $package_key package key.
	 *
	 * @void
	 */
	public static function reset_chosen_points( $package_key ) {
		if ( WC()->session ) {
			foreach ( WC()->session->get_session_data() as $key => $value ) {
				if ( 0 === strpos( $key, 'bw_chosen_parcel_point_' . $package_key ) ) {
					WC()->session->set( $key, null );
				}
			}
		}
	}

	/**
	 * Check if parcelpoint is in the response.
	 *
	 * @param mixed $network_parcel_points parcelpoints.
	 * @param mixed $point chosen parcelpoint.
	 * @return boolean
	 */
	private static function is_point_in_response( $network_parcel_points, $point ) {
		$found = false;

		if ( null !== $point ) {
			foreach ( $network_parcel_points as $parcel_points ) {
				if ( $point->code === $parcel_points->parcelPoint->code ) {
					$found = true;
				}
			}
		}

		return $found;
	}

	/**
	 * Is the rate id the selected shipping method
	 *
	 * @param int $rate_id woocommmerce shipping rate id.
	 * @return boolean is selected
	 */
	public static function is_selected_shipping_method( $rate_id ) {
		return in_array( $rate_id, WC()->session->get( 'chosen_shipping_methods' ), true );
	}

	/**
	 * Return the extra label to add to a frontend shipping offer
	 *
	 * @param int $shipping_rate_id parcelpoints.
	 * @param int $package_key chosen parcelpoint.
	 * @return string|null
	 */
	public static function get_parcel_point_label( $shipping_rate_id, $package_key ) {
		$label = null;
		if ( Misc_Util::should_display_parcel_point_link( $shipping_rate_id ) ) {

			$has_parcel_points = self::init_points( self::get_recipient_address(), $shipping_rate_id, $package_key );

			if ( $has_parcel_points ) {
				$label                = '<span class="bw-parcel-point">';
				$chosen_parcel_point  = self::get_chosen_point( $shipping_rate_id, $package_key );
				$parcel_point_address = null;
				if ( null === $chosen_parcel_point ) {
					$closest_parcel_point = self::get_closest_point( $shipping_rate_id, $package_key );
					$label               .= '<span class="bw-parcel-client-' . $package_key . '">' . __( 'Closest parcel point:', 'boxtal-connect' ) . ' <span class="bw-parcel-name-' . $package_key . '">' . $closest_parcel_point->name . '</span></span>';
					$parcel_point_address = Parcelpoint_Util::get_parcelpoint_address( $closest_parcel_point );
				} else {
					$label               .= '<span class="bw-parcel-client-' . $package_key . '">' . __( 'Your parcel point:', 'boxtal-connect' ) . ' <span class="bw-parcel-name-' . $package_key . '">' . $chosen_parcel_point->name . '</span></span>';
					$parcel_point_address = Parcelpoint_Util::get_parcelpoint_address( $chosen_parcel_point );
				}

				if ( null !== $parcel_point_address ) {
					$label .= '<br/><small class="bw-parcel-address-' . $package_key . '"/>' . esc_html( $parcel_point_address ) . '</small>';
				}

				$label .= '<br/><span class="bw-select-parcel" data-package_key="' . $package_key . '" data-branding="bw">' . __( 'Choose another', 'boxtal-connect' ) . '</span>';
				$label .= '</span>';
			}
		}

		return $label;
	}

	/**
	 * Is the frontend checkout using woocommerce blocks instead of legacy
	 *
	 * @return boolean
	 */
	public static function is_checkout_using_woocommerce_blocks() {
		return class_exists( \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class )
			&& \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_checkout_block_default();

	}

	/**
	 * Is the frontend cart using woocommerce blocks instead of legacy
	 *
	 * @return boolean
	 */
	public static function is_cart_using_woocommerce_blocks() {
		return class_exists( \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::class )
			&& \Automattic\WooCommerce\Blocks\Utils\CartCheckoutUtils::is_cart_block_default();

	}

	/**
	 * Return an array of data necessary for frontend scripts
	 *
	 * @return array
	 */
	public static function get_frontend_data() {
		return array(
			'ajaxurl'                          => admin_url( 'admin-ajax.php' ),
			'mapUrl'                           => self::get_map_url(),
			'mapLogoImageUrl'                  => Configuration_Util::get_map_logo_image_url(),
			'mapLogoHrefUrl'                   => Configuration_Util::get_map_logo_href_url(),
			'setPointNonce'                    => wp_create_nonce( self::$set_point_action ),
			'getPointsNonce'                   => wp_create_nonce( self::$get_points_action ),
			'getShippingMethodExtraLabelNonce' => wp_create_nonce( self::$get_shipping_method_extra_label_action ),
		);
	}

	/**
	 * Return an array of translation map display
	 *
	 * @return array
	 */
	public static function get_map_translations() {
		return array(
			'Unable to find carrier'   => __( 'Unable to find carrier', 'boxtal-connect' ),
			'Opening hours'            => __( 'Opening hours', 'boxtal-connect' ),
			'Choose this parcel point' => __( 'Choose this parcel point', 'boxtal-connect' ),
			'Close map'                => __( 'Close map', 'boxtal-connect' ),
			'Your parcel point:'       => __( 'Your parcel point:', 'boxtal-connect' ),
			/* translators: %s: distance in km */
			'%skm away'                => __( '%skm away', 'boxtal-connect' ),
			'MONDAY'                   => __( 'MONDAY', 'boxtal-connect' ),
			'TUESDAY'                  => __( 'TUESDAY', 'boxtal-connect' ),
			'WEDNESDAY'                => __( 'WEDNESDAY', 'boxtal-connect' ),
			'THURSDAY'                 => __( 'THURSDAY', 'boxtal-connect' ),
			'FRIDAY'                   => __( 'FRIDAY', 'boxtal-connect' ),
			'SATURDAY'                 => __( 'SATURDAY', 'boxtal-connect' ),
			'SUNDAY'                   => __( 'SUNDAY', 'boxtal-connect' ),
		);
	}

	/**
	 *
	 * Inject an array of string as an inline script
	 *
	 * @deprecated inline script is a method used for injecting data into legacy scripts, it is no longer required since woocommerce blocks
	 *
	 * @param string $handle script handle.
	 * @param string $name name of the variable receiving the data.
	 * @param array  $data to inject into the variable.
	 */
	public static function inject_inline_data( $handle, $name, $data ) {
		wp_add_inline_script( $handle, 'var ' . $name . ' = ' . $name . ' ? ' . $name . ' : {}', 'before' );
		foreach ( $data as $key => $value ) {
			wp_add_inline_script( $handle, $name . '.' . $key . ' = "' . $value . '"', 'before' );
		}
	}
}
