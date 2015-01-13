<?php

class GlobalSettings {
    private $fields = array('sv_debug', 'paypal_email', 'sandbox_paypal_email', 'individual_fee', 'business_fee', 'facebook_url', 'strava_url', "club_address", "club_city", "club_state", "club_zip");

    public function __construct() {
        $this->add_actions();
        $this->enqueue_js();
        $this->enqueue_css();
    }

    public function is_global_settings_page() {
        return $_SERVER['SCRIPT_NAME'] == '/wp-admin/admin.php' && $_GET['page'] == 'global-settings';
    }

    public function add_menus() {
        add_options_page( 'Global Settings', 'Global Settings', 'manage_options', 'global-settings', array(&$this,'global_settings_page'));
    }

    public function global_settings_page() {
        $m = $this->mustache();
        $template = $m->loadTemplate('/admin/global_options');

        echo $template->render($this->get_options());
    }

    private function add_actions() {
        add_action( 'admin_menu', array(&$this,'add_menus'));
        add_action('wp_ajax_update_global_settings', array(&$this,'update_global_settings'));
    }

    public function update_global_settings() {
        unset($_POST['action']);

        foreach ($_POST as $key => $value) {
            update_option($key,$value);
        }

        $data = $this->get_options();
        $data['message'] = 'Settings Updated.';

        echo json_encode($data);

        die();
    }


    private function get_options() {
        $data = array();

        foreach ($this->fields as &$field) {
            $data[$field] = get_option($field);
        }

        return $data;
    }

    private function enqueue_js() {
        if (is_admin()) {
            wp_register_script('validation', get_stylesheet_directory_uri() . '/js/admin/validation.js');
            wp_register_script('serializeObject', get_stylesheet_directory_uri() . '/js/serializeObject.js');
            wp_register_script('global_settings', get_stylesheet_directory_uri() . '/js/admin/global_settings.js');

            wp_enqueue_script('validation');
            wp_enqueue_script('serializeObject');
            wp_enqueue_script('global_settings');
        }
    }

    private function enqueue_css() {
        wp_register_style('global_settings', get_stylesheet_directory_uri() . '/css/admin/global_settings.css');
        wp_enqueue_style('global_settings');
    }

    private function mustache() {
        $themePath = get_stylesheet_directory();

        require_once($themePath . '/mustache.php/src/Mustache/Autoloader.php');
        Mustache_Autoloader::register();

        return new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader($themePath . '/tmpl')
        ));
    }
}