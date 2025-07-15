<?php
/**
 * Contains code for the parcel point controller class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point;

use Boxtal\BoxtalConnectWoocommerce\Util\Configuration_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Customer_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Rate_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Parcelpoint_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Api_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Logger_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Frontend_Util;

/**
 * Controller class.
 *
 * Handles setter and getter for parcel points.
 */
class Controller {

	/**
	 * Plugin url.
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $plugin_version;

	/**
	 * Construct function.
	 *
	 * @param array $plugin plugin array.
	 * @void
	 */
	public function __construct( $plugin ) {
		$this->plugin_url     = $plugin['url'];
		$this->plugin_version = $plugin['version'];
	}

	/**
	 * Run class.
	 *
	 * @void
	 */
	public function run() {
		add_action( 'wp_ajax_bw_get_points', array( $this, 'get_points_callback' ) );
		add_action( 'wp_ajax_bw_set_point', array( $this, 'set_point_callback' ) );
		add_action( 'wp_ajax_bw_get_map_url', array( $this, 'get_map_url_callback' ) );
		add_action( 'wp_ajax_bw_get_shipping_method_extra_label', array( $this, 'get_shipping_method_extra_label_callback' ) );
		add_action( 'wp_ajax_nopriv_bw_get_points', array( $this, 'get_points_callback' ) );
		add_action( 'wp_ajax_nopriv_bw_set_point', array( $this, 'set_point_callback' ) );
		add_action( 'wp_ajax_nopriv_bw_get_map_url', array( $this, 'get_map_url_callback' ) );
		add_action( 'wp_ajax_nopriv_bw_get_shipping_method_extra_label', array( $this, 'get_shipping_method_extra_label_callback' ) );

		if (Frontend_Util::is_cart_using_woocommerce_blocks()) {
			add_action( 'woocommerce_blocks_cart_block_registration', array( $this, 'register_parcel_point_block' ) );
		} else {
			add_action( 'woocommerce_after_shipping_calculator', array( $this, 'parcel_point_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'parcel_point_styles' ) );
		}

		if (Frontend_Util::is_checkout_using_woocommerce_blocks()) {
			add_action( 'woocommerce_blocks_checkout_block_registration', array( $this, 'register_parcel_point_block' ) );
		} else {
			add_action( 'woocommerce_after_checkout_form', array( $this, 'parcel_point_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'parcel_point_styles' ) );
		}
	}

	/**
	 * Check if the current page is on checkout or cart
	 *
	 * @boolean
	 */
	private function is_checkout_or_cart() {
		return ( ! function_exists( 'is_checkout' ) || is_checkout() ) || ( ! function_exists( 'is_cart' ) || is_cart() );
	}

	/**
	 * Register parcel point block class
	 *
	 * @param mixed $integration_registry woocommerce block integration registry.
	 * @void
	 */
	public function register_parcel_point_block( $integration_registry ) {
		$integration_registry->register( new Parcel_Point_Block_Integration() );
	}

	/**
	 * Enqueue pickup point script
	 *
	 * @void
	 */
	public function parcel_point_scripts() {
		if ( $this->is_checkout_or_cart() ) {
			wp_enqueue_script( 'bw_polyfills', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/polyfills.min.js', array(), $this->plugin_version, false );
			wp_enqueue_script( 'bw_maplibre_gl', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/maplibre-gl.js', array( 'bw_polyfills' ), $this->plugin_version, false );
			wp_enqueue_script( 'bw_shipping', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/js/parcel-point.min.js', array( 'bw_maplibre_gl', 'bw_polyfills' ), $this->plugin_version, false );
			Frontend_Util::inject_inline_data( 'bw_shipping', 'bwData', Frontend_Util::get_frontend_data() );
			wp_localize_script( 'bw_shipping', 'translations', Frontend_Util::get_map_translations() );
			wp_set_script_translations( 'bw_translation', 'boxtal-connect' );
		}
	}

	/**
	 * Enqueue parcel point styles
	 *
	 * @void
	 */
	public function parcel_point_styles() {
		if ( $this->is_checkout_or_cart() ) {
			wp_enqueue_style( 'bw_maplibre_gl', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/maplibre-gl.min.css', array(), $this->plugin_version );
			wp_enqueue_style( 'bw_parcel_point', $this->plugin_url . 'Boxtal/BoxtalConnectWoocommerce/assets/css/parcel-point.css', array(), $this->plugin_version );
		}
	}

	/**
	 * Get parcel points callback.
	 *
	 * @void
	 */
	public function get_points_callback() {
		check_ajax_referer( Frontend_Util::$get_points_action, '_wpnonce' );
		header( 'Content-Type: application/json; charset=utf-8' );

		if ( ! isset( $_REQUEST['carrier'] ) || ! isset( $_REQUEST['packageKey'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to get parcel points: unable to find carrier or package key', 'boxtal-connect' ) ) );
		}

		$carrier       = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
		$parcel_points = Frontend_Util::get_shipping_method_parcel_points( $carrier );

		if ( null === $parcel_points ) {
			wp_send_json_error( array( 'message' => __( 'Failed to get parcel points: no response from shipping service', 'boxtal-connect' ) ) );
		}

		wp_send_json_success( $parcel_points );
	}

	/**
	 * Set parcel point callback.
	 *
	 * @void
	 */
	public function set_point_callback() {
		check_ajax_referer( Frontend_Util::$set_point_action, '_wpnonce' );
		header( 'Content-Type: application/json; charset=utf-8' );

		if ( ! isset( $_REQUEST['carrier'], $_REQUEST['network'], $_REQUEST['code'], $_REQUEST['name'], $_REQUEST['packageKey'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to set parcel point: invalid request', 'boxtal-connect' ) ) );
		}

		$carrier       = sanitize_text_field( wp_unslash( $_REQUEST['carrier'] ) );
		$package_key   = sanitize_text_field( wp_unslash( $_REQUEST['packageKey'] ) );
		$network       = sanitize_text_field( wp_unslash( $_REQUEST['network'] ) );
		$code          = sanitize_text_field( wp_unslash( $_REQUEST['code'] ) );
		$name          = sanitize_text_field( wp_unslash( $_REQUEST['name'] ) );
		$address       = isset( $_REQUEST['address'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['address'] ) ) : '';
		$zipcode       = isset( $_REQUEST['zipcode'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['zipcode'] ) ) : '';
		$city          = isset( $_REQUEST['city'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['city'] ) ) : '';
		$country       = isset( $_REQUEST['country'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['country'] ) ) : '';
		$opening_hours = isset( $_REQUEST['openingHours'] ) ? json_decode( sanitize_text_field( wp_unslash( $_REQUEST['openingHours'] ) ) ) : null;
		$distance      = isset( $_REQUEST['distance'] ) ? json_decode( sanitize_text_field( wp_unslash( $_REQUEST['distance'] ) ) ) : null;

		$parcel_point = ParcelPoint_Util::create_parcelpoint(
			$network,
			$code,
			$name,
			$address,
			$zipcode,
			$city,
			$country,
			$opening_hours,
			is_numeric( $distance ) ? floatval( $distance ) : null
		);

		if ( WC()->session ) {
			WC()->session->set( 'bw_chosen_parcel_point_' . $package_key . '_' . Shipping_Rate_Util::get_clean_id( $carrier ), $parcel_point );
		} else {
			wp_send_json_error( array( 'message' => 'could not set point. Woocommerce sessions are not enabled!' ) );
		}

		wp_send_json_success(
			array(
				'label' => Frontend_Util::get_parcel_point_label( $carrier, $package_key ),
			)
		);
	}

	/**
	 * Return extra label for a shipping method and package
	 */
	public function get_shipping_method_extra_label_callback() {
		check_ajax_referer( Frontend_Util::$get_shipping_method_extra_label_action, '_wpnonce' );
		header( 'Content-Type: application/json; charset=utf-8' );

		if ( ! isset( $_REQUEST['shippingMethod'], $_REQUEST['packageKey'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to get shipping method extra label: invalid request', 'boxtal-connect' ) ) );
		}
		$shipping_method = sanitize_text_field( wp_unslash( $_REQUEST['shippingMethod'] ) );
		$package_key     = sanitize_text_field( wp_unslash( $_REQUEST['packageKey'] ) );

		$label = Frontend_Util::get_parcel_point_label( $shipping_method, $package_key );

		wp_send_json_success( array( 'label' => $label ) );
	}

	/**
	 * Return maplibre configuration map url
	 */
	public function get_map_url_callback() {
		wp_send_json_success( array( 'mapUrl' => Frontend_Util::get_map_url() ) );
	}
}
