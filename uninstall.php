<?php
// Security: only run when WordPress triggers uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove all plugin options from the database.
delete_option( 'epff_settings' );