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

if (!class_exists('ssl_atlas_auth')) {

    /**
     * Class to manage the auth API interactions of ssl_atlas
     */
    class ssl_atlas_auth
    {

        // host will be derived from SSL_ATLAS_PLUGIN_AUTH_HOST
        const URL = 'api/sslatlasauth/v1/auth/stackpath/#/';

        /**
         * Add hooks and filters for admin pages
         *
         * @since 1.0
         * @static
         */
        public static function init()
        {

        }

        public static function call($action, $additional = array(), $determine_replay = true)
        {
            if (is_null($additional)) {
                $additional = array();
            }

            $urlInfo = parse_url(get_site_url());
            $domain = (isset($urlInfo['host']) ? $urlInfo['host'] : '');

            $fs_license = sa_fs()->_get_license();
            $license = '';
            if ($fs_license) {
                $license = sprintf('%d|%d|%d|%d', $fs_license->plugin_id, $fs_license->user_id, $fs_license->plan_id, $fs_license->id);
            }

            if (SSL_ATLAS_PLUGIN_ALLOW_DEV) {
                $domain = 'atlasssl.xyz';
                $license = '4586|3380497|11089|401986';

                // no license validation.
                $additional = array_merge($additional, array('________test' => 1));

                // wait infinitely.
                set_time_limit(0);
                add_filter('http_request_timeout', function () {
                    return 100;
                });
            }

            // if the last api action that was called is the same as this call,
            // mark this call as a replay
            // so that the service can decide how to handle it.
            $last_api_call = get_option('ssl_atlas_last_auth_api_call');
            if ($determine_replay && $last_api_call === $action) {
                $additional['replay'] = true;
            }

            $params = array('blocking' => true, 'timeout' => 100, 'redirection' => 5);
            $params['body'] = array_merge($additional, array(
                'domain' => $domain,
                // send the domainconnect status on requests
                'domainconnect_status' => ssl_atlas_domainconnect::isEnabled(),
                'license' => $license
            ));

            $url = str_replace('#', $action, trailingslashit(SSL_ATLAS_PLUGIN_AUTH_HOST) . self::URL);
            ssl_atlas_helper::log(sprintf('Calling %s with params %s', $url, print_r($params, true)));
            $response = json_decode(wp_remote_retrieve_body(wp_remote_post($url, $params)), true);
            ssl_atlas_helper::log(sprintf('Called %s with params %s to get %s', $url, print_r($params, true), print_r($response, true)));

            // save the last api action called.
            update_option('ssl_atlas_last_auth_api_call', $action);

            switch ($action) {
                case 'reactivate':
                    if (intval($response['wait']) === 1) {
                        update_option('ssl_atlas_settings_stage', $response['goto']);
                        switch ($response['goto']) {
                            case 'step2':
                                update_option('ssl_atlas_last_auth_api_call', 'verify_records');
                                break;
                            case 'step3':
                                update_option('ssl_atlas_last_auth_api_call', 'request_ssl');
                                update_option('ssl_atlas_cert_details', $response['details']);
                                break;
                            case 'step4':
                                if (ssl_atlas_admin::wp_config_has_stackpath_changes()) {
                                    $siteUrl = str_replace("http://", "https://", get_option('siteurl'));
                                    $homeUrl = str_replace("http://", "https://", get_option('home'));
                                    update_option('siteurl', $siteUrl);
                                    update_option('home', $homeUrl);
                                    update_option('ssl_atlas_ssl_activated', '1');
                                    update_option('ssl_atlas_ssl_activated_date', time());
                                    ssl_atlas_helper::removeLogs();
                                    update_option('ssl_atlas_settings_stage', 'settings');
                                }
                                break;
                        }
                    }
                    break;
            }

            return $response;
        }
    }

    /**
     * Calling init function and activate hooks and filters.
     */
    ssl_atlas_auth::init();

}
