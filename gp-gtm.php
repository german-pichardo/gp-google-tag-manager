<?php
/**
 * Plugin Name: Gp: Google Tag Manager
 * Description: Tags before and after body (Menu->Settings->Gp Google tag Manager.
 * Version: 1.0
 * Author: German Pichardo
 * Author URI: http://www.german-pichardo.com
 * Text Domain: gp-gtm-text-domain
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if( !class_exists( 'GpGtm' )){
    class GpGtm {
        private $gp_gtm_options;
        /**
         * Plugin initialization
         */
        public function __construct() {
            // Add the page to the admin menu
            add_action( 'admin_menu', array( $this, 'gp_gtm_add_plugin_page' ) );
            // Register page options
            add_action( 'admin_init', array( $this, 'gp_gtm_page_init' ) );

        }


        /**
         * Function that will add the options page under Setting Menu.
         */
        public function gp_gtm_add_plugin_page() {
            // $page_title, $menu_title, $capability, $menu_slug, $callback_function
            add_options_page(
                __('Google Tag Manager Options'), // page_title
                __('Gp Google Tag Manager'), // menu_title
                'manage_options', // capability
                'gp-gtm-options', // menu_slug
                array( $this, 'gp_gtm_create_admin_page' ) // function
            );
        }

        /**
         * Function that will display the options page.
         */
        public function gp_gtm_create_admin_page() {
            $this->gp_gtm_options = get_option( 'gp_gtm_option_name' ); ?>

            <div class="wrap">
                <h2><?php _e( 'Google Tag Manager settings' ); ?></h2>
                <?php //settings_errors(); ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'gp_gtm_option_group' );
                    do_settings_sections( 'gp-gtm-admin-sections' );
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        public function gp_gtm_page_init() {

            // Register Settings
            register_setting(
                'gp_gtm_option_group', // option_group
                'gp_gtm_option_name', // option_name
                array( $this, 'gp_gtm_sanitize' ) // sanitize_callback
            );

            // Add Section for option fields
            add_settings_section(
                'gp_gtm_section_head_area', // id
                '', // title
                array( $this, 'gp_gtm_section_head_area_callback' ), // callback
                'gp-gtm-admin-sections' // page
            );

            // Add Section for labels
            add_settings_section(
                'gp_gtm_section_after_body', // id
                '', // title
                array( $this, 'gp_gtm_section_after_body_callback' ), // callback
                'gp-gtm-admin-sections' // page
            );

            // Add Slug Field
            add_settings_field(
                'gp_gtm_head_area_1', // id
                __('GTM Code'), // title
                array( $this, 'gp_gtm_head_area_1_callback' ), // callback
                'gp-gtm-admin-sections', // page
                'gp_gtm_section_head_area' // section
            );

            // Add Singular name Field
            add_settings_field(
                'gp_gtm_after_body_2', // id
                __('GTM noscript'), // title
                array( $this, 'gp_gtm_after_body_2_callback' ), // callback
                'gp-gtm-admin-sections', // page
                'gp_gtm_section_after_body' // section
            );

        }

        /**
         * Section slug callback
         */
        public function gp_gtm_section_head_area_callback() {
            echo "<hr><h2>".__(  'Head Area' )."</h2>";
        }
        /**
         * Section label callback
         */
        public function gp_gtm_section_after_body_callback() {
            echo "<hr><h2>".__(  'After Body' )."</h2>";
        }


        /**
         * Functions that display the fields.
         */
        public function gp_gtm_sanitize($input) {
            $sanitary_values = array();

            if ( isset( $input['gp_gtm_head_area_1'] ) ) {
                $sanitary_values['gp_gtm_head_area_1'] = esc_textarea( $input['gp_gtm_head_area_1'] );
            }

            if ( isset( $input['gp_gtm_after_body_2'] ) ) {
                $sanitary_values['gp_gtm_after_body_2'] = esc_textarea( $input['gp_gtm_after_body_2'] );
            }
            return $sanitary_values;
        }

        /**
         * Fields individual callbacks
         */

        public function gp_gtm_head_area_1_callback() {
            printf(
                '<textarea class="large-text code" rows="8" name="gp_gtm_option_name[gp_gtm_head_area_1]" id="gp_gtm_head_area_1" placeholder="' .__( 'Paste your code here', 'gp-gtm-text-domain' ) . '">%s</textarea>',
                isset( $this->gp_gtm_options['gp_gtm_head_area_1'] ) ? esc_attr( $this->gp_gtm_options['gp_gtm_head_area_1']) : ''
            );
        }

        public function gp_gtm_after_body_2_callback() {
            printf(
                '<textarea class="large-text code" rows="8" name="gp_gtm_option_name[gp_gtm_after_body_2]" id="gp_gtm_after_body_2" placeholder="' .__( 'Paste your code here', 'gp-gtm-text-domain' ) . '">%s</textarea>',
                isset( $this->gp_gtm_options['gp_gtm_after_body_2'] ) ? esc_attr( $this->gp_gtm_options['gp_gtm_after_body_2']) : ''
            );
        }

    }
} // !class_exists

if ( is_admin() )
    $gp_gtm = new GpGtm();
//
//   $gp_gtm_options = get_option( 'gp_gtm_option_name' ); // Array of All Options
//   $gp_gtm_head_area_1 = $gp_gtm_options['gp_gtm_head_area_1']; // Head
//   $gp_gtm_after_body_2 = $gp_gtm_options['gp_gtm_after_body_2']; // After body
