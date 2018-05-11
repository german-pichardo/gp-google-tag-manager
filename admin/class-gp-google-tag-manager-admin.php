<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('GpGoogleTagManagerAdmin')) {
    class GpGoogleTagManagerAdmin
    {
        public $account_code;

        /**
         * Plugin initialization
         */
        public function __construct()
        {
            // Add the page to the admin menu
            add_action('admin_menu', [$this, 'add_plugin_page']);
            // Register page options
            add_action('admin_init', [$this, 'admin_page_init']);
            // Add plugin settings link
            add_filter('plugin_action_links', [$this, 'add_settings_link'], 10, 2);

            $this->account_code = (isset(get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1']) && !empty(get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1'])) ? get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1'] : false;
        }

        /**
         * Function that will add the options page under Setting Menu.
         */
        public function add_plugin_page()
        {
            // $page_title, $menu_title, $capability, $menu_slug, $callback_function
            add_options_page(
                __('Google Tag Manager Options'), // page_title
                __('Gp Google Tag Manager'), // menu_title
                'manage_options', // capability
                'gp-google-tag-manager-options', // menu_slug
                [$this, 'create_admin_page'] // function
            );
        }

        /**
         * Function that will display the options page.
         */
        public function create_admin_page()
        { ?>
            <div class="wrap">
                <h2><?php _e('Google Tag Manager settings'); ?></h2>
                <?php //settings_errors();
                ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields('gp_google_tag_manager_option_group');
                    do_settings_sections('gp-google-tag-manager-admin-sections');
                    submit_button();
                    ?>
                </form>
            </div>
        <?php }

        public function admin_page_init()
        {

            // Register Settings
            register_setting(
                'gp_google_tag_manager_option_group', // option_group
                'gp_google_tag_manager_option_name', // option_name
                [$this, 'sanitize_values'] // sanitize_callback
            );

            // Add Section for option fields
            add_settings_section(
                'section_account_code', // id
                '', // title
                [$this, 'section_account_code_callback'], // callback
                'gp-google-tag-manager-admin-sections' // page
            );

            // Add Section for labels
            add_settings_section(
                'gp_google_tag_manager_section_after_body', // id
                '', // title
                [$this, 'section_after_body_callback'], // callback
                'gp-google-tag-manager-admin-sections' // page
            );

            // Add Slug Field
            add_settings_field(
                'gp_google_tag_manager_account_code_1', // id
                __('GTM Code'), // title
                [$this, 'account_code_1_callback'], // callback
                'gp-google-tag-manager-admin-sections', // page
                'section_account_code' // section
            );
        }

        /**
         * Section slug callback
         */
        public function section_account_code_callback()
        {
            echo "<hr><h2>" . __('Gtm code') . "</h2>";
        }

        /**
         * Section label callback
         */
        public function section_after_body_callback()
        {
            if ($this->account_code) { ?>
                <hr><h2>After Body <small>(Optional)</small></h2>
                <p>The second GTM code needs to be added manually in order to use Google Tag Manager for <u>Visitors Without JavaScript</u> </p>
                <p class='description'>Copy the following snippet and paste it immediately <b><u>after</u> </b>the opening <code>body</code></p>
                <code><?php echo htmlspecialchars("<?php if (function_exists('google_tag_manager_snippet_body')) {
                        google_tag_manager_snippet_body();
                    }; ?>"); ?></code>
            <?php };
        }

        /**
         * Functions that display the fields.
         */
        public function sanitize_values($input)
        {
            $sanitary_values = [];

            if (isset($input['gp_google_tag_manager_account_code_1'])) {
                $sanitary_values['gp_google_tag_manager_account_code_1'] = sanitize_text_field($input['gp_google_tag_manager_account_code_1']);
            }

            return $sanitary_values;
        }

        /**
         * Fields individual callbacks
         */

        public function account_code_1_callback()
        {
            printf(
                '<input type="text" placeholder="' . __('GTM-XXXXXXX', 'gp-google-tag-manager-text-domain') . '" class="regular-text" name="gp_google_tag_manager_option_name[gp_google_tag_manager_account_code_1]" value="%s" id="gp_google_tag_manager_account_code_1" >',
                $this->account_code ? esc_attr($this->account_code) : ''
            );
        }

        /**
         * Functions that registers settings link on plugin description.
         */
        public function add_settings_link($links, $file)
        {
            $this_plugin = plugin_basename(__FILE__);

            if (is_plugin_active($this_plugin) && $file == $this_plugin) {
                $links[] = '<a href="' . admin_url('options-general.php?page=gp-google-tag-manager-options') . '">' . __('Settings', 'gp-google-tag-manager-text-domain') . '</a>';
            }

            return $links;

        } // end add_settings_link

    }
} // !class_exists

if (is_admin())
    $gp_google_tag_manager = new GpGoogleTagManagerAdmin();
