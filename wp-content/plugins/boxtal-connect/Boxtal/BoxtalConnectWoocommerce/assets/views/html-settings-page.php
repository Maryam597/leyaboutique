<?php
/**
 * Settings page rendering
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Assets\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap" id="bw-settings">
	<h1>Boxtal Connect</h1>

	<form method="post" action="options.php">
		<?php
			settings_fields( $plugin_settings_id );
			do_settings_sections( $plugin_settings_id );
			submit_button();
			do_settings_sections( $plugin_tutorial_id );
		?>
	</form>
</div>
