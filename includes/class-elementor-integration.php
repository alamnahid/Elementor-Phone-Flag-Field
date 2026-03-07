<?php
namespace EPFF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Elementor_Integration {

    public function __construct() {
        // Enqueue assets on the frontend only.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets() {
        // Only load when Elementor frontend is active.
        if ( ! class_exists( '\Elementor\Plugin' ) ) {
            return;
        }

        $settings = get_option( 'epff_settings', array() );

        // intl-tel-input vendor library (bundled in plugin, not from CDN).
        wp_enqueue_style(
            'intl-tel-input',
            EPFF_PLUGIN_URL . 'assets/vendor/intl-tel-input/intlTelInput.min.css',
            array(),
            '26.3.1'
        );

        wp_enqueue_style(
            'epff-frontend',
            EPFF_PLUGIN_URL . 'assets/css/phone-flag-field.css',
            array( 'intl-tel-input' ),
            EPFF_VERSION
        );

        wp_enqueue_script(
            'intl-tel-input',
            EPFF_PLUGIN_URL . 'assets/vendor/intl-tel-input/intlTelInput.min.js',
            array(),
            '26.3.1',
            true
        );

        wp_enqueue_script(
            'epff-frontend',
            EPFF_PLUGIN_URL . 'assets/js/phone-flag-field.js',
            array( 'intl-tel-input', 'jquery' ),
            EPFF_VERSION,
            true
        );

        // Pass PHP settings to JavaScript securely using wp_localize_script.
wp_localize_script( 'epff-frontend', 'epffSettings', array(
    'defaultCountry'    => sanitize_text_field( $settings['default_country'] ?? 'us' ),
    'autoDetect'        => ! empty( $settings['auto_detect'] ),
    'allowedCountries'  => $this->sanitize_country_array( $settings['allowed_countries'] ?? array() ),
    'excludedCountries' => $this->sanitize_country_array( $settings['excluded_countries'] ?? array() ),
    'utilsScript'       => EPFF_PLUGIN_URL . 'assets/vendor/intl-tel-input/utils.js',
    'flagsUrl'          => EPFF_PLUGIN_URL . 'assets/vendor/intl-tel-input/img/flags.webp',
    'flags2xUrl'        => EPFF_PLUGIN_URL . 'assets/vendor/intl-tel-input/img/flags@2x.webp',
) );
    }

    /**
     * Sanitize an array of country codes (e.g. ['us', 'pk', 'in']).
     */
    private function sanitize_country_array( $countries ) {
        if ( ! is_array( $countries ) ) return array();
        return array_map( 'sanitize_text_field', $countries );
    }
}