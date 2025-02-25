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

if ( !class_exists( 'ssl_atlas_scripts' ) ) {
    /**
     * Class to manage the scripts and styles for SSL Atlas
     */
    class ssl_atlas_scripts
    {
        /**
         * Add hooks and filters to enqueue scripts and styles needed for SSL Atlas
         *
         * @since 1.0
         * @static
         */
        public static function init()
        {
            $page = ( isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '' );
            if ( $page == 'ssl_atlas' ) {
                add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
            }
            if ( $page == 'ssl_atlas' ) {
                add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts_no_conflict', 11 );
            }
            add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts_for_notice' );
        }
        
        /**
         * Hook to add scripts and styles for SSL Atlas admin
         *
         * @since 1.0
         * @static
         */
        public static function admin_enqueue_scripts()
        {
            wp_enqueue_style(
                'ssl-atlas-font-css',
                SSL_ATLAS_URL . 'css/fonts.css',
                array(),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_style(
                'ssl-atlas-bootstrap-css',
                SSL_ATLAS_URL . 'css/bootstrap.min.css',
                array(),
                SSL_ATLAS_PLUGIN_VERSION
            );
            //			wp_enqueue_style( 'ssl-atlas-fontawesome-css', SSL_ATLAS_URL . 'css/font-awesome.min.css', array(),
            //				SSL_ATLAS_PLUGIN_VERSION );
            wp_enqueue_style(
                'ssl-atlas-build-css',
                SSL_ATLAS_URL . 'css/build.css',
                array(),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_style(
                'ssl-atlas-bootstrap-toggle-css',
                SSL_ATLAS_URL . 'css/bootstrap-toggle.min.css',
                array(),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_style(
                'ssl-atlas-style-css',
                SSL_ATLAS_URL . 'css/style.css',
                array(),
                SSL_ATLAS_PLUGIN_VERSION
            );
            //			wp_enqueue_style( 'ssl-atlas-google-fonts', 'https://fonts.googleapis.com/css?family=Roboto&display=swap',
            //				false, SSL_ATLAS_PLUGIN_VERSION );
            wp_enqueue_script( 'jQuery' );
            wp_enqueue_script(
                'ssl-atlas-jquery-validate-js',
                SSL_ATLAS_URL . 'js/jquery.validate.js',
                array( 'jquery' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'ssl-atlas-bootstrap-js',
                SSL_ATLAS_URL . 'js/bootstrap.min.js',
                array( 'jquery' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'ssl-atlas-bootstrap-toggle-js',
                SSL_ATLAS_URL . 'js/bootstrap-toggle.min.js',
                array( 'jquery' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'ssl-atlas-main-js',
                SSL_ATLAS_URL . 'js/main.js',
                array( 'jquery', 'clipboard' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_localize_script( 'ssl-atlas-main-js', 'params', array(
                'l10n' => array(
                'copied_success' => __( 'Copied successfully.', 'ssl-atlas' ),
                'copied_failure' => __( 'Failed to copy.', 'ssl-atlas' ),
            ),
            ) );
        }
        
        /**
         * Hook to add scripts and styles for SSL Atlas admin
         *
         * @since 1.0
         * @static
         */
        public static function admin_enqueue_scripts_no_conflict()
        {
            wp_enqueue_script(
                'ssl-atlas-jquery-no-conflict',
                SSL_ATLAS_URL . 'js/jquery.no-conflict.js',
                array( 'jquery' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'ssl-atlas-donutty-jquery.min',
                SSL_ATLAS_URL . 'js/donutty-jquery.min.js',
                array( 'jquery' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
        }
        
        /**
         * Function for print the js required for removing notice
         *
         * @since 1.7
         */
        public static function admin_enqueue_scripts_for_notice()
        {
            wp_enqueue_script(
                'ssl-atlas-notice-js',
                SSL_ATLAS_URL . 'js/ssl-atlas-notice.js',
                array( 'jquery' ),
                SSL_ATLAS_PLUGIN_VERSION
            );
            wp_localize_script( 'ssl-atlas-notice-js', 'ssl_atlas_notice_nonce', array(
                'nonce' => wp_create_nonce( 'ssl_atlas_notice_nonce_action' ),
            ) );
        }
    
    }
    /**
     * Calling init function and activate hooks and filters.
     */
    ssl_atlas_scripts::init();
}
