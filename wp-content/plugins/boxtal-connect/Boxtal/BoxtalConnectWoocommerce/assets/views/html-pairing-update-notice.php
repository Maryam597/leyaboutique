<?php
/**
 * Pairing update notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php
		/* translators: 1) company name */
		echo sprintf( esc_html__( 'Security alert: someone is trying to pair your site with %s. Was it you?', 'boxtal-connect' ), 'Boxtal' );
	?>
	<button class="button-secondary bw-pairing-update-validate" bw-pairing-update-validate="1" href="#"><?php esc_html_e( 'yes', 'boxtal-connect' ); ?></button>
	<button class="button-secondary bw-pairing-update-validate" bw-pairing-update-validate="0" href="#"><?php esc_html_e( 'no', 'boxtal-connect' ); ?></button>
</div>
