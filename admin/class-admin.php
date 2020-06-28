<?php

namespace ManageAd_Admin;

/**
 * The code used in the admin.
 */
class Admin
{
    private $plugin_slug;
    private $version;
    private $option_name;
    private $settings;
    private $settings_group;

    public function __construct($plugin_slug, $version, $option_name)
    {
        $this->plugin_slug = $plugin_slug;
        $this->version = $version;
        $this->option_name = $option_name;
        $this->settings = get_option($this->option_name);
        $this->settings_group = $this->option_name . '_group';
    }

    /**
     * Generate settings fields by passing an array of data (see the render method).
     *
     * @param array $field_args The array that helps build the settings fields
     * @param array $settings The settings array from the options table
     *
     * @return string The settings fields' HTML to be output in the view
     */
    private function managead_custom_settings_fields($field_args, $settings)
    {
        $output = '
            <div class="managead_fields_wrapper">
                <div class="managead_ad_list">';
            if (isset($settings['managead_ad_title'])) {
                foreach ($settings['managead_ad_title'] as $key => $value) {
                    $output .= '
                        <div class="managead_field">
                            <div class="managead_field_title"><h3>' . $value . '</h3></div>
                            <div class="managead_field_body">
                                <div class="managead_field_body_title">
                                    <span>' . __('Ad Title', 'managead') . '</span>
                                    <input type="text" name="' . $this->option_name . '[managead_ad_title][' . $key . ']" value="' . $value . '" required>
                                </div>
                                <div class="managead_field_body_code">
                                    <span>' . __('Ad Code', 'managead') . '</span>
                                    <textarea id="" name="' . $this->option_name . '[managead_ad_code][' . $key . ']" rows="10" required>' . $settings['managead_ad_code'][$key] . '</textarea>
                                </div>
                                <a href="javascript:void(0)" class="button delete_ad">' . __('Delete', 'managead') . '</a>
                            </div>
                        </div>
                    ';
                }
            }

        $output .= '
                </div>            
                <script id="managead_field_template" type="text/template">
                    <div class="managead_field">
                        <div class="managead_field_title"><h3>' . __('New Ad', 'managead') . '</h3></div>
                        <div class="managead_field_body">
                            <div class="managead_field_body_title">
                                <span>' . __('Ad Title', 'managead') . '</span>
                                <input type="text" name="' . $this->option_name . '[managead_ad_title][%id%]" required>
                            </div>
                            <div class="managead_field_body_code">
                                <span>' . __('Ad Code', 'managead') . '</span>
                                <textarea id="" name="' . $this->option_name . '[managead_ad_code][%id%]" rows="10" required></textarea>
                            </div>
                        </div>
                    </div>
                </script>
                    
                <a href="javascript:void(0)" class="managead_new_add">' . __('Add New Ad', 'managead') . '</a>
            </div>
            ';

        return $output;
    }

    public function managead_adblock_notice() {
        ?>
        <style>
            @keyframes fadein{
                0% { opacity:0; }
                66% { opacity:0; }
                100% { opacity:1; }
            }

            @-webkit-keyframes fadein{
                0% { opacity:0; }
                66% { opacity:0; }
                100% { opacity:1; }
            }
            #managead-adblock-notice {
                animation: 2s ease 0s normal forwards 1 fadein;
            }
        </style>
        <div id="managead-adblock-notice" class="notice notice-error">
            <h2>ManageAd</h2>
            <p>It seems like your AdBlocker is conflicting with the plugin. Or you may be getting this error because of Gutenberg is not active in your website. </p>
            <p>Please make sure that Gutenberg is active and that the AdBlocker is closed.</p>
        </div>
        <?php
    }

    public function managead_assets()
    {
        if ( function_exists( 'register_block_type' ) ) {
            wp_enqueue_script($this->plugin_slug . '-adblocker-removal', plugin_dir_url(__FILE__) . 'js/managead-adblocker-removal.js', ['jquery'], $this->version, true);
        }
    }

    public function managead_register_settings()
    {
        register_setting($this->settings_group, $this->option_name);
    }

    public function managead_render_block($attributes)
    {
        $adCode = $this->settings['managead_ad_code'][$attributes['adKey']];
        $html = '<div class="managead_element" style="margin-top:5px; margin-bottom:5px;">' . $adCode . '</div>';

        return $html;
    }

    public function managead_get_current_count()
    {
        echo intval(get_option('managead_block_count'));
        wp_die();
    }

    public function managead_count_increase()
    {
        $count = intval(get_option('managead_block_count')) + 1;
        update_option('managead_block_count', $count);
    }

    public function managead_register_block()
    {
        wp_register_script(
            'managead-block',
            plugins_url('block.build.js', __FILE__),
            array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components')
        );

        if ( function_exists( 'register_block_type' ) ) {
            register_block_type('managead/block', array(
                'editor_script' => 'managead-block',
                'render_callback' => [$this, 'managead_render_block']
            ));
        }

        wp_localize_script('managead-block', 'ad_list', $this->settings);
    }

    public function managead_add_menus()
    {
        $plugin_name = Info::get_plugin_title();
        add_submenu_page(
            'options-general.php',
            $plugin_name,
            $plugin_name,
            'manage_options',
            $this->plugin_slug,
            [$this, 'managead_render']
        );
    }

    /**
     * Render the view using MVC pattern.
     */
    public function managead_render()
    {
        if (!get_option('managead_block_count')) {
            update_option('managead_block_count', 0);
        }


        wp_localize_script($this->plugin_slug, 'managead_block_count', array('ajax_url' => admin_url('admin-ajax.php')));

        $cssCode = wp_remote_get(plugin_dir_url(__FILE__) . 'css/managead-admin.css');
        $jsCode = wp_remote_get(plugin_dir_url(__FILE__) . 'js/managead-admin.js');
        echo '<style>'.$cssCode['body'].'</style>';
        echo '<script> var managead_block_count = {ajax_url: "'.admin_url('admin-ajax.php').'"}; '.$jsCode['body'].'</script>';


        // Generate the settings fields
        $field_args = [
            [
                'label' => 'managead_ad_title',
                'slug' => 'managead_ad_title',
                'type' => 'text'
            ],
            [
                'label' => 'managead_ad_code',
                'slug' => 'managead_ad_code',
                'type' => 'textarea'
            ]
        ];

        // Model
        $settings = $this->settings;

        // Controller
        $fields = $this->managead_custom_settings_fields($field_args, $settings);
        $settings_group = $this->settings_group;
        $heading = Info::get_plugin_title();
        $submit_text = esc_attr__('Submit', 'managead');

        // View
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/view.php';
    }
}
