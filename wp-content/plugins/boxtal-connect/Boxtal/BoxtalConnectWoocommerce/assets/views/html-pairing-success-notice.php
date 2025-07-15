<?php
/**
 * Pairing success notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-success">
	<a class="bw-close-link bw-hide-notice" data-action="bw_hide_notice" rel="pairing">x</a>
	<h2><?php esc_html_e( 'Congratulations, your shop is connected !', 'boxtal-connect' ); ?></h2>
	<p><?php esc_html_e( 'Finalize your settings to start shipping', 'boxtal-connect' ); ?></p>
	<p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=boxtal-connect-settings' ) ); ?>" class="button-primary" rel="pairing">
			<?php esc_html_e( 'Finalize the settings', 'boxtal-connect' ); ?>
		</a>
	</p>
</div>
