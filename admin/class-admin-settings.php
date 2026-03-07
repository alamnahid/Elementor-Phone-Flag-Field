<?php
namespace EPFF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_Settings {

    private $option_name = 'epff_settings';

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    public function add_settings_page() {
        add_options_page(
            esc_html__( 'Phone Flag Field Settings', 'elementor-phone-flag-field' ),
            esc_html__( 'Phone Flag Field', 'elementor-phone-flag-field' ),
            'manage_options', // Only administrators.
            'epff-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting(
            'epff_settings_group',
            $this->option_name,
            array( $this, 'sanitize_settings' )
        );

        add_settings_section(
            'epff_general_section',
            esc_html__( 'General Settings', 'elementor-phone-flag-field' ),
            '__return_false',
            'epff-settings'
        );

        add_settings_field(
            'default_country',
            esc_html__( 'Default Country', 'elementor-phone-flag-field' ),
            array( $this, 'render_default_country_field' ),
            'epff-settings',
            'epff_general_section'
        );

        add_settings_field(
            'auto_detect',
            esc_html__( 'Auto-Detect Visitor Country', 'elementor-phone-flag-field' ),
            array( $this, 'render_auto_detect_field' ),
            'epff-settings',
            'epff_general_section'
        );

        add_settings_field(
            'allowed_countries',
            esc_html__( 'Allowed Countries (leave empty for all)', 'elementor-phone-flag-field' ),
            array( $this, 'render_allowed_countries_field' ),
            'epff-settings',
            'epff_general_section'
        );

        add_settings_field(
            'excluded_countries',
            esc_html__( 'Excluded Countries', 'elementor-phone-flag-field' ),
            array( $this, 'render_excluded_countries_field' ),
            'epff-settings',
            'epff_general_section'
        );
    }

    /**
     * Sanitize all settings before saving to the database.
     * This is critical for security.
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        // Default country: must be a 2-letter code.
        $sanitized['default_country'] = isset( $input['default_country'] )
            ? strtolower( sanitize_text_field( $input['default_country'] ) )
            : 'us';

        // Auto detect: boolean.
        $sanitized['auto_detect'] = ! empty( $input['auto_detect'] );

        // Country lists: comma-separated codes → sanitized array.
        $sanitized['allowed_countries']  = $this->parse_country_codes( $input['allowed_countries'] ?? '' );
        $sanitized['excluded_countries'] = $this->parse_country_codes( $input['excluded_countries'] ?? '' );

        return $sanitized;
    }

    /**
     * Parse "US, PK, IN" style input into a clean array: ['us', 'pk', 'in'].
     */
    private function parse_country_codes( $raw ) {
        if ( empty( $raw ) ) return array();
        $codes = explode( ',', sanitize_text_field( $raw ) );
        $codes = array_map( 'trim', $codes );
        $codes = array_map( 'strtolower', $codes );
        $codes = array_filter( $codes, function( $code ) {
            return preg_match( '/^[a-z]{2}$/', $code ); // Only valid 2-letter ISO codes.
        });
        return array_values( $codes );
    }

    public function render_default_country_field() {
        $settings = get_option( $this->option_name, array() );
        $value    = esc_attr( $settings['default_country'] ?? 'us' );
        printf(
            '<input type="text" name="%s[default_country]" value="%s" class="regular-text" placeholder="us" maxlength="2" />
            <p class="description">%s</p>',
            esc_attr( $this->option_name ),
            $value,
            esc_html__( 'Enter a 2-letter country code, e.g. us, pk, gb', 'elementor-phone-flag-field' )
        );
    }

    public function render_auto_detect_field() {
        $settings = get_option( $this->option_name, array() );
        $checked  = ! empty( $settings['auto_detect'] ) ? 'checked' : '';
        printf(
            '<label><input type="checkbox" name="%s[auto_detect]" value="1" %s /> %s</label>
            <p class="description">%s</p>',
            esc_attr( $this->option_name ),
            $checked,
            esc_html__( 'Enable', 'elementor-phone-flag-field' ),
            esc_html__( 'Automatically detect the visitor\'s country using their IP address.', 'elementor-phone-flag-field' )
        );
    }

    public function render_allowed_countries_field() {
        $settings = get_option( $this->option_name, array() );
        $value    = esc_attr( implode( ', ', array_map( 'strtoupper', $settings['allowed_countries'] ?? array() ) ) );
        printf(
            '<input type="text" name="%s[allowed_countries]" value="%s" class="large-text" placeholder="US, PK, IN, GB" />
            <p class="description">%s</p>',
            esc_attr( $this->option_name ),
            $value,
            esc_html__( 'Comma-separated country codes. Leave blank to allow all countries.', 'elementor-phone-flag-field' )
        );
    }

    public function render_excluded_countries_field() {
        $settings = get_option( $this->option_name, array() );
        $value    = esc_attr( implode( ', ', array_map( 'strtoupper', $settings['excluded_countries'] ?? array() ) ) );
        printf(
            '<input type="text" name="%s[excluded_countries]" value="%s" class="large-text" placeholder="KP, IR" />
            <p class="description">%s</p>',
            esc_attr( $this->option_name ),
            $value,
            esc_html__( 'Comma-separated country codes to hide from the dropdown.', 'elementor-phone-flag-field' )
        );
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        require_once EPFF_PLUGIN_DIR . 'admin/partials/settings-page.html.php';
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'settings_page_epff-settings' !== $hook ) return;

        wp_enqueue_style(
            'epff-admin',
            EPFF_PLUGIN_URL . 'assets/css/admin-settings.css',
            array(),
            EPFF_VERSION
        );
    }
}