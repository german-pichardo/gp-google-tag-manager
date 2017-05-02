<?php
/**
 * Plugin Name: Gp: Google Tag Manager
 * Description: Tags before and after body (Menu->Settings->Gp Google tag Manager.
 * Version: 1.0
 * Author: German Pichardo
 * Author URI: http://www.german-pichardo.com
 * Text Domain: gp-google-tag-manager-text-domain
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if( !class_exists( 'GpGoogleTagManager' )){
    class GpGoogleTagManager {
        private $gp_google_tag_manager_options;
        /**
         * Plugin initialization
         */
        public function __construct() {
            // Add the page to the admin menu
            add_action( 'admin_menu', array( $this, 'gp_google_tag_manager_add_plugin_page' ) );
            // Register page options
            add_action( 'admin_init', array( $this, 'gp_google_tag_manager_page_init' ) );
            // Add plugin settings link
            add_filter( 'plugin_action_links', array( $this, 'gp_google_tag_manager_add_settings_link' ), 10, 2 );

        }


        /**
         * Function that will add the options page under Setting Menu.
         */
        public function gp_google_tag_manager_add_plugin_page() {
            // $page_title, $menu_title, $capability, $menu_slug, $callback_function
            add_options_page(
                __('Google Tag Manager Options'), // page_title
                __('Gp Google Tag Manager'), // menu_title
                'manage_options', // capability
                'gp-google-tag-manager-options', // menu_slug
                array( $this, 'gp_google_tag_manager_create_admin_page' ) // function
            );
        }

        /**
         * Function that will display the options page.
         */
        public function gp_google_tag_manager_create_admin_page() {
            $this->gp_google_tag_manager_options = get_option( 'gp_google_tag_manager_option_name' ); ?>

            <div class="wrap">
                <h2><?php _e( 'Google Tag Manager settings' ); ?></h2>
                <?php //settings_errors(); ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields( 'gp_google_tag_manager_option_group' );
                    do_settings_sections( 'gp-google-tag-manager-admin-sections' );
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        public function gp_google_tag_manager_page_init() {

            // Register Settings
            register_setting(
                'gp_google_tag_manager_option_group', // option_group
                'gp_google_tag_manager_option_name', // option_name
                array( $this, 'gp_google_tag_manager_sanitize' ) // sanitize_callback
            );

            // Add Section for option fields
            add_settings_section(
                'gp_google_tag_manager_section_account_code', // id
                '', // title
                array( $this, 'gp_google_tag_manager_section_account_code_callback' ), // callback
                'gp-google-tag-manager-admin-sections' // page
            );

            // Add Section for labels
            add_settings_section(
                'gp_google_tag_manager_section_after_body', // id
                '', // title
                array( $this, 'gp_google_tag_manager_section_after_body_callback' ), // callback
                'gp-google-tag-manager-admin-sections' // page
            );

            // Add Slug Field
            add_settings_field(
                'gp_google_tag_manager_account_code_1', // id
                __('GTM Code'), // title
                array( $this, 'gp_google_tag_manager_account_code_1_callback' ), // callback
                'gp-google-tag-manager-admin-sections', // page
                'gp_google_tag_manager_section_account_code' // section
            );


        }

        /**
         * Section slug callback
         */
        public function gp_google_tag_manager_section_account_code_callback() {
            echo "<hr><h2>".__(  'Gtm code' )."</h2>";
        }
        /**
         * Section label callback
         */
        public function gp_google_tag_manager_section_after_body_callback() {
            { if(empty($this->gp_google_tag_manager_options = get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1'])) return; ?>
                <hr><h2>After Body</h2>
                <p class='description'>Copy the following snippet and paste it immediately after the opening <code>body</code> </p>
                <code><?php echo htmlspecialchars("<?php if (function_exists('google_tag_manager_js_2')) {
                        google_tag_manager_js_2();
                    }; ?>");?></code>
            <?php };
        }


        /**
         * Functions that display the fields.
         */
        public function gp_google_tag_manager_sanitize($input) {
            $sanitary_values = array();

            if ( isset( $input['gp_google_tag_manager_account_code_1'] ) ) {
                $sanitary_values['gp_google_tag_manager_account_code_1'] = sanitize_text_field( $input['gp_google_tag_manager_account_code_1'] );
            }

            return $sanitary_values;
        }

        /**
         * Fields individual callbacks
         */

        public function gp_google_tag_manager_account_code_1_callback() {
            printf(
                '<input type="text" placeholder="' .__( 'GTM-XXXXXXX', 'gp-google-tag-manager-text-domain' ) . '" class="regular-text" name="gp_google_tag_manager_option_name[gp_google_tag_manager_account_code_1]" value="%s" id="gp_google_tag_manager_account_code_1" >',
                isset( $this->gp_google_tag_manager_options['gp_google_tag_manager_account_code_1'] ) ? esc_attr( $this->gp_google_tag_manager_options['gp_google_tag_manager_account_code_1']) : ''
            );
        }

        /**
         * Functions that registers settings link on plugin description.
         */
        public function gp_google_tag_manager_add_settings_link( $links , $file){
            $this_plugin = plugin_basename(__FILE__);

            if ( is_plugin_active($this_plugin) && $file == $this_plugin ) {
                $links[] = '<a href="' . admin_url( 'options-general.php?page=gp-google-tag-manager-options' ) . '">' . __( 'Settings', 'gp-google-tag-manager-text-domain' ) . '</a>';
            }

            return $links;

        } // end gp_google_tag_manager_add_settings_link

    }
} // !class_exists

if ( is_admin() )
    $gp_google_tag_manager = new GpGoogleTagManager();

/**************************************************
 * Front-End
 * ************************************************/

$gp_google_tag_manager_options = get_option('gp_google_tag_manager_option_name'); // Array of All Options
$gp_google_tag_manager_account_code_1 = $gp_google_tag_manager_options['gp_google_tag_manager_account_code_1']; // Code
// Head tag
function google_tag_manager_js_1(){
    global $gp_google_tag_manager_account_code_1;
    if ($gp_google_tag_manager_account_code_1) { ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?php echo $gp_google_tag_manager_account_code_1;  ?>');</script>
        <!-- End Google Tag Manager -->
    <?php }
}
add_action('wp_head', 'google_tag_manager_js_1', 1); // Wp hook to inject into head

// After body tag
function google_tag_manager_js_2_callback(){
    global $gp_google_tag_manager_account_code_1;

    if ($gp_google_tag_manager_account_code_1) { ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $gp_google_tag_manager_account_code_1;  ?>"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php }

}
add_action('google_tag_manager_js_2_action', 'google_tag_manager_js_2_callback');

// After Body action
// Needs to create an action and a function to inset it manually after body
function google_tag_manager_js_2() {
    do_action('google_tag_manager_js_2_action');
}