<?php

/**
 *
 * Helps install a FREE SSL certificate from LetsEncrypt, fixes mixed content, insecure content by redirecting to https, and forces SSL on all pages.
 *
 * Plugin Name:       SSL Atlas - Free SSL Certificate & HTTPS Redirect
 * Plugin URI:        https://sslatlas.com
 * Description:       Helps install a free SSL certificate from LetsEncrypt, fixes mixed content, insecure content by redirecting to https, and forces SSL on all pages.
 * Version:           1.1.1
 * Author:            SSL Atlas
 * Author URI:        http://sslatlas.com
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ssl-atlas
 * Domain Path:       ssl_atlas/languages
 *
 * @author      SSL Atlas
 * @category    Plugin
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 *
 */
/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
    die( 'Access Denied' );
}
/**
 * Require external package dependencies
 */
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

if ( !function_exists( 'sa_fs' ) ) {
    // Create a helper function for easy SDK access.
    function sa_fs()
    {
        global  $sa_fs ;
        
        if ( !isset( $sa_fs ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_7432_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_7432_MULTISITE', true );
            }
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $sa_fs = fs_dynamic_init( array(
                'id'             => '7432',
                'slug'           => 'ssl-atlas',
                'type'           => 'plugin',
                'public_key'     => 'pk_26e619c9696063f04273f7422d4ef',
                'is_premium'     => false,
                'premium_suffix' => 'Premium',
                'has_addons'     => false,
                'has_paid_plans' => true,
                'trial'          => array(
                'days'               => 30,
                'is_require_payment' => false,
            ),
                'menu'           => array(
                'support' => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $sa_fs;
    }
    
    // Init Freemius.
    sa_fs();
    // Trying to customize the freemius message
    function sa_fs_custom_connect_message_on_update(
        $message,
        $user_first_name,
        $product_title,
        $user_login,
        $site_link,
        $freemius_link
    )
    {
        return sprintf(
            __( 'Hey %1$s', 'my-text-domain' ) . ',<br>' . __( 'We highly recommend that you opt-in to our security notifications. Opting in also helps us provide you fast support. We track non-sensitive diagnostic data using Freemius.', 'ssl-atlas' ),
            $user_first_name,
            '<b>' . $product_title . '</b>',
            '<b>' . $user_login . '</b>',
            $site_link,
            $freemius_link
        );
    }
    
    sa_fs()->add_filter(
        'connect_message_on_update',
        'sa_fs_custom_connect_message_on_update',
        10,
        6
    );
    // Signal that SDK was initiated.
    do_action( 'sa_fs_loaded' );
}

/**
 * Define constants used in the plugin
 */
if ( !defined( 'SSL_ATLAS_PLUGIN_VERSION' ) ) {
    define( 'SSL_ATLAS_PLUGIN_VERSION', '1.1.1' );
}
if ( !defined( 'SSL_ATLAS_DIR' ) ) {
    define( 'SSL_ATLAS_DIR', plugin_dir_path( __FILE__ ) . 'ssl_atlas/' );
}
if ( !defined( 'SSL_ATLAS_URL' ) ) {
    define( 'SSL_ATLAS_URL', plugin_dir_url( __FILE__ ) . 'ssl_atlas/' );
}
if ( !defined( 'SSL_ATLAS_BASEFILE' ) ) {
    define( 'SSL_ATLAS_BASEFILE', plugin_basename( __FILE__ ) );
}
if ( !defined( 'SSL_ATLAS_PLUGIN_ALLOW_DEV' ) ) {
    // to enable development on local environments.
    define( 'SSL_ATLAS_PLUGIN_ALLOW_DEV', false );
}
if ( !defined( 'SSL_ATLAS_PLUGIN_ALLOW_DEBUG' ) ) {
    // to enable debugging logs
    define( 'SSL_ATLAS_PLUGIN_ALLOW_DEBUG', false );
}
if ( !defined( 'SSL_ATLAS_PLUGIN_AUTH_HOST' ) ) {
    // the host of the auth plugin, with or without trailing slash.
    define( 'SSL_ATLAS_PLUGIN_AUTH_HOST', 'https://api.sslatlas.com' );
}
/**
 * Include the core file of the plugin
 */
require_once SSL_ATLAS_DIR . 'classes/class.ssl_atlas.php';
if ( !function_exists( 'ssl_atlas_init' ) ) {
    /**
     * Function to initialize the plugin.
     *
     * @return class object
     */
    function ssl_atlas_init()
    {
        /* Initialize the base class of the plugin */
        return ssl_atlas::instance();
    }

}
/**
 * Create the main object of the plugin when the plugins are loaded
 */
add_action( 'plugins_loaded', 'ssl_atlas_init' );