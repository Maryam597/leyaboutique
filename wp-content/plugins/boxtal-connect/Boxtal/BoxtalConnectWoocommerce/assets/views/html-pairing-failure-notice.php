<?php
/**
 * Pairing failure notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php
	/* translators: 1) company name 2) company name */
	echo sprintf( esc_html__( 'Pairing with %1$1s is not complete. Please check your WooCommerce connector in your %2$2s account for a more complete diagnostic.', 'boxtal-connect' ), 'Boxtal', 'Boxtal' );
	?>
</div>
