<?php

namespace ManageAd_Admin;

/**
 * The main plugin class.
 */
class Plugin
{

    private $loader;
    private $plugin_slug;
    private $version;
    private $option_name;

    public function __construct() {
        $this->plugin_slug = Info::SLUG;
        $this->version     = Info::VERSION;
        $this->option_name = Info::OPTION_NAME;
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php';
        $this->loader = new Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Admin($this->plugin_slug, $this->version, $this->option_name);
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'managead_assets');
        $this->loader->add_action('admin_init', $plugin_admin, 'managead_register_settings');
        $this->loader->add_action('admin_menu', $plugin_admin, 'managead_add_menus');
        $this->loader->add_action('init', $plugin_admin, 'managead_register_block');
        $this->loader->add_action('wp_ajax_managead_count_increase', $plugin_admin , 'managead_count_increase' );
        $this->loader->add_action('wp_ajax_managead_get_current_count', $plugin_admin , 'managead_get_current_count' );
        $this->loader->add_action('admin_notices', $plugin_admin, 'managead_adblock_notice');
    }

    public function run() {
        $this->loader->run();
    }
}
