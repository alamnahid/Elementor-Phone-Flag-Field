<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap epff-settings-wrap">
    <h1>
        📞 <?php esc_html_e( 'Phone Flag Field for Elementor', 'nahid-phone-flag-field' ); ?>
    </h1>

    <?php settings_errors( 'epff_settings_group' ); ?>

    <div class="epff-settings-layout">
        <div class="epff-settings-main">
            <form method="post" action="options.php">
                <?php
                settings_fields( 'epff_settings_group' );
                do_settings_sections( 'epff-settings' );
                submit_button( esc_html__( 'Save Settings', 'nahid-phone-flag-field' ) );
                ?>
            </form>
        </div>

        <div class="epff-settings-sidebar">
            <div class="epff-card">
                <h3><?php esc_html_e( 'How to Use', 'nahid-phone-flag-field' ); ?></h3>
                <ol>
                    <li><?php esc_html_e( 'Open any Elementor page with a Form widget.', 'nahid-phone-flag-field' ); ?></li>
                    <li><?php esc_html_e( 'Add a "Tel" field to the form.', 'nahid-phone-flag-field' ); ?></li>
                    <li><?php esc_html_e( 'The flag + country code selector appears automatically.', 'nahid-phone-flag-field' ); ?></li>
                </ol>
            </div>
            <div class="epff-card">
                <h3><?php esc_html_e( 'Country Code Examples', 'nahid-phone-flag-field' ); ?></h3>
                <p><?php esc_html_e( 'US = United States, PK = Pakistan, IN = India, GB = United Kingdom', 'nahid-phone-flag-field' ); ?></p>
            </div>
        </div>
    </div>
</div>