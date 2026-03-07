<?php
/**
 * Plugin Name:       Phone Flag Field for Elementor
 * Plugin URI:        https://nahidalam.com/phone-flag-field
 * Description:       Adds international phone flag + country code selector to Elementor Pro Tel fields with auto-detection, search, and admin controls.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Nahid
 * Author URI:        https://nahidalam.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       elementor-phone-flag-field
 * Domain Path:       /languages
 */

// Prevent direct access — ALWAYS include this.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'EPFF_VERSION',     '1.0.0' );
define( 'EPFF_PLUGIN_FILE', __FILE__ );
define( 'EPFF_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'EPFF_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

// Make is_plugin_active() available early — needed before admin loads.
if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Check if Elementor Pro is present in ANY form.
 * Works with official, nulled, or renamed versions.
 */
function epff_check_dependencies() {
    $elementor_pro_active = (
        did_action( 'elementor_pro/init' )                    ||
        class_exists( '\ElementorPro\Plugin' )                ||
        class_exists( '\ElementorPro\Modules\Forms\Module' )  ||
        defined( 'ELEMENTOR_PRO_VERSION' )                    ||
        is_plugin_active( 'elementor-pro/elementor-pro.php' )
    );

    if ( ! $elementor_pro_active ) {
        add_action( 'admin_notices', 'epff_missing_elementor_notice' );
        return false;
    }

    return true;
}

/**
 * Admin notice shown when Elementor Pro is not detected.
 */
function epff_missing_elementor_notice() {
    $message = sprintf(
        /* translators: %s: Link to Elementor Pro */
        esc_html__( 'Phone Flag Field for Elementor requires Elementor Pro to be installed and active. %s', 'elementor-phone-flag-field' ),
        '<a href="https://elementor.com/pro/" target="_blank">' . esc_html__( 'Get Elementor Pro', 'elementor-phone-flag-field' ) . '</a>'
    );
    echo '<div class="notice notice-error"><p>' . wp_kses_post( $message ) . '</p></div>';
}

/**
 * Main initialization — runs after all plugins are loaded.
 *
 * Admin settings ALWAYS load so the Settings page is accessible.
 * Frontend integration only loads if Elementor Pro is detected.
 */
function epff_init() {

    // ALWAYS load admin settings — but use a static flag to prevent double init.
    if ( is_admin() && ! did_action( 'epff_admin_loaded' ) ) {
        if ( ! class_exists( '\EPFF\Admin_Settings' ) ) {
            require_once EPFF_PLUGIN_DIR . 'admin/class-admin-settings.php';
        }
        new \EPFF\Admin_Settings();
        do_action( 'epff_admin_loaded' );
    }

    // Frontend + form integration only loads with Elementor Pro.
    if ( ! epff_check_dependencies() ) {
        return;
    }

    if ( ! class_exists( '\EPFF\Plugin' ) ) {
        require_once EPFF_PLUGIN_DIR . 'includes/class-plugin.php';
    }

    \EPFF\Plugin::get_instance();
}
add_action( 'plugins_loaded', 'epff_init' );

/**
 * Activation hook — saves default plugin settings to database.
 * Only runs once when the plugin is first activated.
 */
function epff_activate() {
    $defaults = array(
        'default_country'    => 'us',
        'allowed_countries'  => array(), // empty = all countries allowed
        'excluded_countries' => array(),
        'auto_detect'        => true,
    );

    // Only add if not already set — preserves existing settings on reactivation.
    if ( ! get_option( 'epff_settings' ) ) {
        add_option( 'epff_settings', $defaults );
    }
}
register_activation_hook( __FILE__, 'epff_activate' );

/**
 * Deactivation hook — runs when plugin is deactivated.
 * Settings are kept intentionally so they survive deactivate/reactivate.
 */
function epff_deactivate() {
    // Nothing to do on deactivation.
    // Settings are preserved in the database.
}
register_deactivation_hook( __FILE__, 'epff_deactivate' );

/**
 * Load plugin textdomain for translations.
 */
function epff_load_textdomain() {
    load_plugin_textdomain(
        'elementor-phone-flag-field',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
add_action( 'init', 'epff_load_textdomain' );