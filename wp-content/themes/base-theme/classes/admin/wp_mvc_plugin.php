<?php

class WP_MVC_Plugin {
    public function is_admin_page() {
        return $_SERVER['SCRIPT_NAME'] == '/wp-admin/admin.php' && $_GET['page'] == $this->name;
    }

    public function css() {
        if ($this->viewingPage) {
            wp_register_style($this->name, WP_CONTENT_URL . '/plugins/'.$this->name.'/css/'.$this->name.'.css');
            wp_enqueue_style($this->name);
        }
    }

    public function js() {
        if ($this->viewingPage) {
            wp_register_script($this->name, WP_CONTENT_URL . '/plugins/'.$this->name.'/js/'.$this->name.'.js');
            wp_enqueue_script($this->name);
        }

        if ($this->is_admin_page()) {
            wp_register_script('validation', WP_CONTENT_URL . '/plugins/'.$this->name.'/js/validation.js');
            wp_enqueue_script('validation');
        }
    }

    public function get_settings_page() {
        include(WP_CONTENT_DIR . '/plugins/'.$this->name.'/pages/settings.php');
    }

    public function add_actions($callback = null) {
        add_action('admin_menu', array(&$this, 'add_admin_menu'));

        if ($callback != null) {
            call_user_func($callback);
        }
    }

    public static function bootstrapMustache($name) {
        $themeDir = get_stylesheet_directory();

        require_once($themeDir . '/mustache.php/src/Mustache/Autoloader.php');
        Mustache_Autoloader::register();

        return new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader(WP_CONTENT_DIR . '/plugins/'.$name.'/templates')
        ));
    }
}