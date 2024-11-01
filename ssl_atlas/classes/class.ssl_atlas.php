<?php

/**
 *
 * Helps install a free SSL certificate from LetsEncrypt, fixes mixed content, insecure content by redirecting to https, and forces SSL on all pages.
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * Plugin Name:       Free SSL Certificate & HTTPS Redirector for WordPress - SSL Atlas
 * Plugin URI:        https://sslatlas.com
 * Description:       Helps install a free SSL certificate from LetsEncrypt, fixes mixed content, insecure content by redirecting to https, and forces SSL on all pages.
 * Version:           1.9.6
 * Author:            SSL
 * Author URI:        http://sslatlas.com
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ssl-atlas
 * Domain Path:       ssl_atlas/languages
 *
 * @author      SSL
 * @category    Plugin
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 */

if (!class_exists('ssl_atlas')) {
    /**
     * Base class of the plugin
     */
    class ssl_atlas
    {
        /**
         * @var ssl_atlas the single instance of the class
         * @since 1.0
         */
        protected static $instance = null;

        /**
         * Instantiates the plugin and include all the files needed for the plugin.
         */
        function __construct()
        {
            self::include_plugin_files();
            self::check_update();
        }

        /**
         * Main SSL Atlas Plugin instance
         *
         * Ensures only one instance of SSL Atlas is loaded or can be loaded.
         *
         * @return SSL Atlas - Main instance
         * @since 1.0
         * @static
         */
        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Include all the files needed for the plugin.
         */
        private static function include_plugin_files()
        {
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_domainconnect.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_cloudflare_fix.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_certificate.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_admin.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_helper.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_messages.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_scripts.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_https.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_scheduled.php');
            require_once(SSL_ATLAS_DIR . 'classes/class.ssl_atlas_auth.php');
            require_once(SSL_ATLAS_DIR . 'lib/LEFunctions.php');
            require_once(SSL_ATLAS_DIR . 'lib/LEConnector.php');
            require_once(SSL_ATLAS_DIR . 'lib/LEAccount.php');
            require_once(SSL_ATLAS_DIR . 'lib/LEAuthorization.php');
            require_once(SSL_ATLAS_DIR . 'lib/LEClient.php');
            require_once(SSL_ATLAS_DIR . 'lib/LEOrder.php');
            // Step 1 for cPanel Free & Paid are same, requires LogMeIn
            require_once(SSL_ATLAS_DIR . 'lib/cPanel/cPanel.php');
        }

        /**
         * Check if current plugin version is higher then 1.6.1 (for including both PRO and FREE) , then we need to proceed couple actions.
         * Rename all htaccess related file names to correct htaccess name
         *
         * @since 1.9.5
         */
        private static function check_update()
        {
            if (version_compare('1.6', SSL_ATLAS_PLUGIN_VERSION) == -1) {

                if (file_exists(SSL_ATLAS_DIR . 'keys/keys.htaccess')) {
                    // rename it
                    @rename(SSL_ATLAS_DIR . 'keys/keys.htaccess', SSL_ATLAS_DIR . 'keys/.htaccess');
                }

                if (file_exists(SSL_ATLAS_DIR . 'keys/__account.htaccess')) {
                    // rename it
                    @rename(SSL_ATLAS_DIR . 'keys/__account.htaccess', SSL_ATLAS_DIR . 'keys/.htaccess');
                }

                if (file_exists(SSL_ATLAS_DIR . 'keys/__account/__account.htaccess')) {
                    // rename it
                    @rename(SSL_ATLAS_DIR . 'keys/__account/__account.htaccess',
                        SSL_ATLAS_DIR . 'keys/__account/.htaccess');
                }
            }
        }
    }
}
