<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

if (!class_exists('GpGoogleTagManagerFront')) {
    class GpGoogleTagManagerFront
    {
        public $account_code;

        /**
         * Plugin initialization
         */
        public function __construct()
        {
            add_action('wp_head', [$this, 'snippet_head'], 1); // Wp hook to inject into head
            add_action('google_tag_manager_snippet_body_action', [$this, 'snippet_body']);
            $this->account_code = (isset(get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1']) && !empty(get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1'])) ? get_option('gp_google_tag_manager_option_name')['gp_google_tag_manager_account_code_1'] : false;
        }

        public function snippet_head()
        {
            if ($this->account_code) { ?>
                <!-- Google Tag Manager -->
                <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','<?php echo $this->account_code;  ?>');</script>
                <!-- End Google Tag Manager -->
            <?php }
        }

        // After body tag
        public function snippet_body()
        {
            if  ($this->account_code) { ?>
                <!-- Google Tag Manager (noscript) -->
                <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo $this->account_code;  ?>"
                                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
                <!-- End Google Tag Manager (noscript) -->
            <?php }

        }
    }
} // !class_exists

if (!is_admin())
    $gp_google_tag_manager = new GpGoogleTagManagerFront();

// After Body action
// Google Tag Manager for Visitors Without JavaScript
function google_tag_manager_snippet_body() {
    do_action('google_tag_manager_snippet_body_action');
}
