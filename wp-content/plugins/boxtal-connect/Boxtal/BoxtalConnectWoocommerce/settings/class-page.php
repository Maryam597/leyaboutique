<?php
/**
 * Contains code for the settings page class.
 *
 * @package     Boxtal\BoxtalConnectWoocommerce\Settings
 */

namespace Boxtal\BoxtalConnectWoocommerce\Settings;

use Boxtal\BoxtalConnectWoocommerce\Notice\Notice_Controller;
use Boxtal\BoxtalConnectWoocommerce\Util\Misc_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Shipping_Method_Util;
use Boxtal\BoxtalConnectWoocommerce\Util\Configuration_Util;

/**
 * Settings page class.
 *
 * Manages settings for the plugin.
 */
class Page {

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
	 * Plugin settings section id.
	 *
	 * @var string
	 */
	private $plugin_settings_id = 'boxtal-connect';

	/**
	 * Plugin tutorial section id.
	 *
	 * @var string
	 */
	private $plugin_tutorial_id = 'boxtal-connect-section-tutorial';

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
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add settings page.
	 *
	 * @void
	 */
	public function add_menu() {
		add_submenu_page( 'woocommerce', 'Boxtal Connect', 'Boxtal Connect', 'manage_woocommerce', 'boxtal-connect-settings', array( $this, 'render_page' ) );
	}

	/**
	 * Return the list of options for order status select.
	 *
	 * @return array list of order status options
	 */
	private function get_order_status_options() {
		$status         = wc_get_order_statuses();
		$status_options = array(
			'none' => esc_html__( 'No status associated', 'boxtal-connect' ),
		);

		foreach ( $status as $key => $translation ) {
			$status_options[ $key ] = esc_html( $translation );
		}

		return $status_options;
	}

	/**
	 * Register settings.
	 *
	 * @void
	 */
	public function register_settings() {
		$status_options   = $this->get_order_status_options();
		$slug             = 'boxtal-connect';
		$tutorial_section = 'boxtal-connect-section-tutorial';

		add_settings_section(
			$slug,
			'1. ' . esc_html__( 'Plugin settings', 'boxtal-connect' ),
			'',
			$this->plugin_settings_id
		);

		register_setting(
			$slug,
			'BW_ORDER_SHIPPED',
			array(
				'type'              => 'string',
				'default'           => null,
				'sanitize_callback' => array( $this, 'sanitize_status' ),
			)
		);
		register_setting(
			$slug,
			'BW_ORDER_DELIVERED',
			array(
				'type'              => 'string',
				'default'           => null,
				'sanitize_callback' => array( $this, 'sanitize_status' ),
			)
		);
		register_setting(
			$slug,
			'BW_LOGGING',
			array(
				'type'    => 'boolean',
				'default' => false,
			)
		);

		add_settings_field(
			'BW_ORDER_SHIPPED',
			esc_html__( 'Shipped status', 'boxtal-connect' ),
			'woocommerce_wp_select',
			$this->plugin_settings_id,
			$slug,
			array(
				'type'         => 'select',
				'option_group' => $this->plugin_settings_id,
				'id'           => 'BW_ORDER_SHIPPED',
				'name'         => 'BW_ORDER_SHIPPED',
				'label_for'    => 'BW_ORDER_SHIPPED',
				'value'        => Configuration_Util::get_order_shipped(),
				'cbvalue'      => Configuration_Util::get_order_shipped(),
				'label'        => '',
				'options'      => $status_options,
			)
		);

		add_settings_field(
			'BW_ORDER_DELIVERED',
			esc_html__( 'Delivered status', 'boxtal-connect' ),
			'woocommerce_wp_select',
			$this->plugin_settings_id,
			$slug,
			array(
				'type'         => 'select',
				'option_group' => $this->plugin_settings_id,
				'id'           => 'BW_ORDER_DELIVERED',
				'name'         => 'BW_ORDER_DELIVERED',
				'label_for'    => 'BW_ORDER_DELIVERED',
				'value'        => Configuration_Util::get_order_delivered(),
				'cbvalue'      => Configuration_Util::get_order_delivered(),
				'label'        => '',
				'options'      => $status_options,
			)
		);

		add_settings_field(
			'BW_LOGGING',
			esc_html__( 'Enable logging', 'boxtal-connect' ),
			'woocommerce_wp_checkbox',
			$this->plugin_settings_id,
			$slug,
			array(
				'type'        => 'checkbox',
				'name'        => 'BW_LOGGING',
				'id'          => 'BW_LOGGING',
				'label_for'   => 'BW_LOGGING',
				'value'       => Configuration_Util::get_logging(),
				'cbvalue'     => '1',
				'label'       => '',
				'description' => esc_html__( 'Should remain unchecked by default.', 'boxtal-connect' ),
			)
		);

		$tuto_url = Configuration_Util::get_help_center_link();
		if ( null !== $tuto_url ) {
			add_settings_section(
				$slug,
				'2. ' . esc_html__( 'Shipping settings', 'boxtal-connect' ),
				array( $this, 'output_shipping_settings_description' ),
				$this->plugin_tutorial_id
			);
		}

	}

	/**
	 * Print shipping settings description.
	 *
	 * @param string $tuto_url tutorial url.
	 * @void
	 */
	public function output_shipping_settings_description( $tuto_url ) {
		$tuto_url   = Configuration_Util::get_help_center_link();
		$link_label = esc_html__( 'Go to the tutorial', 'boxtal-connect' );

		echo wp_kses(
			sprintf(
				// translators: 1) tutorian link 2) tutorial link label.
				__( 'Just one last step, it will only take a few minutes, let us guide you: <a target="_blank" href="%1$1s">%2$2s</a>', 'boxtal-connect' ),
				$tuto_url,
				$link_label
			),
			array(
				'a' => array(
					'href'   => true,
					'target' => true,
				),
			)
		);
	}

	/**
	 * Render settings page.
	 *
	 * @void
	 */
	public function render_page() {
		$plugin_settings_id = $this->plugin_settings_id;
		$plugin_tutorial_id = $this->plugin_tutorial_id;
		include_once dirname( __DIR__ ) . '/assets/views/html-settings-page.php';
	}

	/**
	 * Sanitize status option.
	 *
	 * @param string $input status value.
	 *
	 * @return string
	 */
	public function sanitize_status( $input ) {
		return 'none' === $input ? null : $input;
	}
}
