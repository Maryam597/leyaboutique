<?php
/**
 * Setup wizard notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-notice bw-info">
	<a class="bw-close-link bw-hide-notice" data-action="bw_hide_notice" rel="setup-wizard">x</a>
	<h2>
	<?php
		/* translators: 1) company name */
		echo sprintf( esc_html__( 'Welcome to %s!', 'boxtal-connect' ), 'Boxtal' );
	?>
	</h2>
	<p><?php esc_html_e( 'The adventure begins in a few clicks', 'boxtal-connect' ); ?></p>
	<p>
		<a href="<?php echo esc_url( $notice->onboarding_link ); ?>" target="_blank" class="button-primary">
			<?php esc_html_e( 'Connect my shop', 'boxtal-connect' ); ?>
		</a>
	</p>
</div>
