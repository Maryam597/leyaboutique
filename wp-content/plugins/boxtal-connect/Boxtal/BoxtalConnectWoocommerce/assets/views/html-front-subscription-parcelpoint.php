<?php
/**
 * Front subscription parcelpoint rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bw-subscription-parcelpoint">
	<h2><?php esc_html_e( 'Chosen pickup point', 'boxtal-connect' ); ?></h2>

	<?php
		require 'html-admin-order-parcelpoint.php';
	?>
</div>
