<?php
namespace EPFF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Plugin {

    private static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

private function load_dependencies() {
    require_once EPFF_PLUGIN_DIR . 'includes/class-elementor-integration.php';
    require_once EPFF_PLUGIN_DIR . 'includes/class-form-handler.php';
    // Admin settings are loaded in the main plugin file to prevent duplication.
}

    private function init_hooks() {
        new Elementor_Integration();
        new Form_Handler();
    }
}