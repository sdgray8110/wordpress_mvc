<?php

class Home extends WP_MVC_Page {
    public function __construct() {
        $this->set_page_data();
        $this->enqueueJS();
        $this->get_post();
        $this->set_title();
        $this->set_name();
        $this->set_sidebar();
        $this->set_default_content();
    }

    private function enqueueJS() {
        wp_register_script('home', get_stylesheet_directory_uri() . '/js/home.js');
        wp_enqueue_script('home');
    }

    protected function page_data_extensions() {
        return array(

        );
    }
}