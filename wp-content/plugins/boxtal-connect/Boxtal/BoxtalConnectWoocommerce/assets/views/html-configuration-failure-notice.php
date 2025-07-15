<?php
/**
 * Configuration failure notice rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="bw-notice bw-warning">
	<?php
	/* translators: 1) Plugin name */
	echo sprintf( esc_html__( 'There was a problem initializing the %s plugin. You should contact our support team.', 'boxtal-connect' ), 'Boxtal Connect' );
	?>
</div>
