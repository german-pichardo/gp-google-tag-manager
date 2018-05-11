<?php
/**
 * Plugin Name: Gp: Google Tag Manager
 * Description: Tags before and after body (Menu->Settings->Gp Google Tag Manager.
 * Version: 1.1.0
 * Author: German Pichardo
 * Author URI: http://www.german-pichardo.com
 * Text Domain: gp-google-tag-manager-text-domain
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

require_once plugin_dir_path(__FILE__) . 'admin/class-gp-google-tag-manager-admin.php'; // Admin

require_once plugin_dir_path(__FILE__) . 'front/class-gp-google-tag-manager-front.php'; // Front
