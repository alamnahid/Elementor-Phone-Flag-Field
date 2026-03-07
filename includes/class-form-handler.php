<?php
namespace EPFF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Form_Handler {

    public function __construct() {
        // Hook into Elementor Pro's form submission BEFORE it processes the record.
        add_action( 'elementor_pro/forms/pre_render', array( $this, 'maybe_modify_tel_field' ), 10, 2 );

        // Filter the submitted value to combine dial code + number.
        add_filter( 'elementor_pro/forms/record/get_formatted_data', array( $this, 'merge_dial_code_into_field' ), 10, 2 );
    }

    /**
     * When the form renders, we tag Tel fields so our JS can find them.
     */
    public function maybe_modify_tel_field( $widget, $args ) {
        // JS handles the actual DOM modification; PHP just marks it's active.
    }

    /**
     * Merge the hidden dial code field into the phone field value.
     * Our JS submits two fields: the original tel field + a hidden "epff_dial_{id}" field.
     */
    public function merge_dial_code_into_field( $formatted_data, $record ) {
        $fields = $record->get( 'fields' );

        foreach ( $fields as $field_id => $field ) {
            if ( 'tel' !== $field['type'] ) {
                continue;
            }

            $dial_code_key = 'epff_dial_' . $field_id;

            if ( isset( $_POST[ $dial_code_key ] ) ) {
                $dial_code = sanitize_text_field( wp_unslash( $_POST[ $dial_code_key ] ) );

                // Validate: dial codes are + followed by 1–4 digits.
                if ( preg_match( '/^\+[0-9]{1,4}$/', $dial_code ) ) {
                    $formatted_data[ $field_id ]['value'] = $dial_code . ' ' . $field['value'];
                }
            }
        }

        return $formatted_data;
    }
}