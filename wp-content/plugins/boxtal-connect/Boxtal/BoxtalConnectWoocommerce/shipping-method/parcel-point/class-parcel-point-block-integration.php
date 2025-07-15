<?php
/**
 * Contains code for the parcel point block integration class
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point
 */

namespace Boxtal\BoxtalConnectWoocommerce\Shipping_Method\Parcel_Point;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;
use Boxtal\BoxtalConnectWoocommerce\Util\Auth_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Api_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Configuration_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Frontend_Util;

/**
 * Controller class.
 *
 * Handles setter and getter for parcel points.
 */
class Parcel_Point_Block_Integration implements IntegrationInterface {
	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'boxtal-connect-parcel-point';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {

		$assets_path = plugins_url( 'boxtal-connect/Boxtal/BoxtalConnectWoocommerce/assets', 'boxtal-connect.php' );

		wp_enqueue_script( 'bw_polyfills', $assets_path . '/js/polyfills.min.js', array(), '1.3.5', false );
		wp_enqueue_script( 'bw_maplibre_gl', $assets_path . '/js/maplibre-gl.js', array(), '1.3.5', false );
		wp_enqueue_script( 'bw_shipping', $assets_path . '/js/parcel-point.min.js', array( 'jquery-core', 'wp-hooks', 'wp-i18n' ), '1.3.5', false );
		wp_enqueue_style( 'bw_maplibre_gl', $assets_path . '/css/maplibre-gl.min.css', array(), '1.3.5' );
		wp_enqueue_style( 'bw_parcel_point', $assets_path . '/css/parcel-point.css', array(), '1.3.5' );
		wp_localize_script( 'bw_shipping', 'translations', Frontend_Util::get_map_translations() );

		// Je n'ai pas trouvé de docs de wp_set_script_translations n'utilisant pas les traductions en dur en 3ème paramètre.
		// Je laisse quand même vu que ça a fonctionné au moins une fois.
		wp_set_script_translations( 'bw_translation', 'boxtal-connect' );

		// frontend data injection for legacy scripts.
		Frontend_Util::inject_inline_data( 'bw_shipping', 'bwData', $this->get_script_data() );
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return array(
			'bw_polyfills',
			'bw_maplibre_gl',
			'bw_shipping',
		);
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return array();
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return Frontend_Util::get_frontend_data();
	}

	/**
	 * Files are reloaded when changed.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		return filemtime( $file );
	}
}
