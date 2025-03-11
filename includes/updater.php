<?php
if (!defined('ABSPATH')) exit;

// Load Plugin Update Checker Library
require_once ABSPATH . 'wp-admin/includes/plugin.php';

class QRG_Updater {
    private $plugin_slug;
    private $plugin_data;
    private $repo_url;
    private $version;
    private $cache_key = 'qrg_plugin_update';
    private $cache_lifetime = 43200; // 12 hours

    public function __construct() {
        $this->plugin_slug = plugin_basename(__FILE__);
        $this->repo_url = 'https://api.github.com/repos/vaibhav-pratap/qr-code-generator/releases/latest';
        $this->plugin_data = get_plugin_data(QRG_PLUGIN_DIR . 'qr-codes-generator.php');
        $this->version = $this->plugin_data['Version'];

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    // Check for Updates
    public function check_for_update($transient) {
        if (empty($transient->checked)) return $transient;

        $remote = get_transient($this->cache_key);
        if (!$remote) {
            $response = wp_remote_get($this->repo_url, ['timeout' => 10, 'headers' => ['Accept' => 'application/json']]);
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) return $transient;

            $remote = json_decode(wp_remote_retrieve_body($response));
            set_transient($this->cache_key, $remote, $this->cache_lifetime);
        }

        if (isset($remote->tag_name) && version_compare($this->version, $remote->tag_name, '<')) {
            $transient->response[$this->plugin_slug] = (object) [
                'slug'        => $this->plugin_slug,
                'new_version' => $remote->tag_name,
                'package'     => $remote->assets[0]->browser_download_url ?? '',
                'url'         => 'https://github.com/vaibhav-pratap/qr-code-generator',
            ];
        }

        return $transient;
    }

    // Plugin Info for WordPress Plugin Page
    public function plugin_info($res, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== $this->plugin_slug) return $res;

        $remote = get_transient($this->cache_key);
        if (!$remote) return $res;

        return (object) [
            'name'          => 'QR Codes Generator for WordPress',
            'slug'          => $this->plugin_slug,
            'version'       => $remote->tag_name,
            'author'        => '<a href="https://github.com/vaibhav-pratap">Vaibhav Singh</a>',
            'homepage'      => 'https://github.com/vaibhav-pratap/qr-code-generator',
            'download_link' => $remote->assets[0]->browser_download_url ?? '',
            'sections'      => [
                'description' => 'Automatically generates QR codes for WooCommerce Products, Orders, Posts & Pages.',
                'changelog'   => file_get_contents(QRG_PLUGIN_DIR . 'CHANGELOG.txt'),
            ],
        ];
    }
}

// Initialize Updater
new QRG_Updater();
?>
