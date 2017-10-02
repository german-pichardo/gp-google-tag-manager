<?php
/**
 * Plugin Name: Gp: Google Tag Manager
 * Description: Tags before and after body (Menu->Settings->Gp Google Tag Manager.
 * Version: 1.1
 * Author: German Pichardo
 * Author URI: http://www.german-pichardo.com
 * Text Domain: gp-google-tag-manager
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if( !class_exists( 'GpGoogleTagManager' )){
    class GpGoogleTagManager {
        private $gp_gtm_option;
        /**
         * Plugin initialization
         */
        public function __construct() {
            // Add the page to the admin menu
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            // Register page options
            add_action( 'admin_init', array( $this, 'page_init' ) );
            // Add plugin settings link
            add_filter( 'plugin_action_links', array( $this, 'add_settings_link' ), 10, 2 );

        }


        /**
         * Function that will add the options page under Setting Menu.
         */
        public function add_plugin_page() {
            // $page_title, $menu_title, $capability, $menu_slug, $callback_function
            add_options_page(
                __('Google Tag Manager Options'), // page_title
                __('Gp Google Tag Manager'), // menu_title
                'manage_options', // capability
                'gp-google-tag-manager', // menu_slug
                array( $this, 'create_admin_page' ) // function
            );
        }

        /**
         * Function that will display the options page.
         */
        public function create_admin_page() {
            $this->gp_gtm_option = get_option( 'gp_google_tag_manager_option' ); ?>

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

        public function page_init() {

            // Register Settings
            register_setting(
                'gp_google_tag_manager_option_group', // option_group
                'gp_google_tag_manager_option', // option_name
                array( $this, 'sanitize_field' ) // sanitize_callback
            );

            // Add Section for option fields
            add_settings_section(
                'section_account_code', // id
                '', // title
                array( $this, 'section_account_code_callback' ), // callback
                'gp-google-tag-manager-admin-sections' // page
            );

            // Add Section for labels
            add_settings_section(
                'gp_google_tag_manager_section_after_body', // id
                '', // title
                array( $this, 'section_after_body_callback' ), // callback
                'gp-google-tag-manager-admin-sections' // page
            );

            // Add Slug Field
            add_settings_field(
                'account_code_1', // id
                __('GTM Code'), // title
                array( $this, 'account_code_1_callback' ), // callback
                'gp-google-tag-manager-admin-sections', // page
                'section_account_code' // section
            );


        }

        /**
         * Section slug callback
         */
        public function section_account_code_callback() {
            echo "<hr><h2>".__(  'Gtm code' )."</h2>";
        }
        /**
         * Section label callback
         */
        public function section_after_body_callback() {
            { if(empty($this->gp_gtm_option = get_option('gp_google_tag_manager_option')['account_code_1'])) return; ?>
                <hr><h2><?php _e('After Body') ?></h2>
                <p><?php _e('The second GTM code needs to be added manually') ?> </p>
                <p class='description'><?php _e('Copy the following snippet and paste it immediately <b><u>after</u> </b>the opening <code>body</code> ') ?></p>
                <p><b><?php _e('PHP:') ?></b></p>
                <code><?php echo htmlspecialchars("<?php if (function_exists('google_tag_manager_snippet_body')) {
                        google_tag_manager_snippet_body();
                    }; ?>");?></code>

                <p><b><?php _e('TWIG:') ?></b></p>
                <code><?php echo htmlspecialchars("
                {% if fn('function_exists','google_tag_manager_snippet_body') %}
                    {{ fn('google_tag_manager_snippet_body') }}
                {% endif %}");?></code>
            <?php };
        }


        /**
         * Functions that display the fields.
         */
        public function sanitize_field($input) {
            $sanitary_values = array();

            if ( isset( $input['account_code_1'] ) ) {
                $sanitary_values['account_code_1'] = sanitize_text_field( $input['account_code_1'] );
            }

            return $sanitary_values;
        }

        /**
         * Fields individual callbacks
         */

        public function account_code_1_callback() {
            printf(
                '<input type="text" placeholder="' .__( 'GTM-XXXXXXX', 'gp-google-tag-manager' ) . '" class="regular-text" name="gp_google_tag_manager_option[account_code_1]" value="%s" id="account_code_1" >',
                isset( $this->gp_gtm_option['account_code_1'] ) ? esc_attr( $this->gp_gtm_option['account_code_1']) : ''
            );
        }

        /**
         * Functions that registers settings link on plugin description.
         */
        public function add_settings_link( $links , $file){
            $this_plugin = plugin_basename(__FILE__);

            if ( is_plugin_active($this_plugin) && $file == $this_plugin ) {
                $links[] = '<a href="' . admin_url( 'options-general.php?page=gp-google-tag-manager' ) . '">' . __( 'Settings', 'gp-google-tag-manager' ) . '</a>';
            }

            return $links;

        } // end add_settings_link

    }
} // !class_exists

if ( is_admin() )
    $gp_google_tag_manager = new GpGoogleTagManager();

/**************************************************
 * Front-End
 * ************************************************/

$gp_gtm_option = get_option('gp_google_tag_manager_option'); // Array of All Options
$account_code_1 = $gp_gtm_option['account_code_1']; // Code
// Head tag
function gp_gtm_snippet_head(){
    global $account_code_1;
    if ($account_code_1) { ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','<?php echo $account_code_1;  ?>');</script>
        <!-- End Google Tag Manager -->
    <?php }
}
add_action('wp_head', 'gp_gtm_snippet_head', 1); // Wp hook to inject into head

// After body tag
function gp_gtm_snippet_body_callback(){
    global $account_code_1;

    if ($account_code_1) { ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $account_code_1;  ?>"
                          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php }

}
add_action('gp_gtm_snippet_body_action', 'gp_gtm_snippet_body_callback');

// After Body action
// Needs to create an action and a function to inset it manually after body
function google_tag_manager_snippet_body() {
    do_action('gp_gtm_snippet_body_action');
}
