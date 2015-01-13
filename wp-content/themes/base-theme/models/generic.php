<?php

class Generic extends WP_MVC_Page {
    public function __construct() {
        $this->set_page_data();
        $this->get_post();
        $this->set_title();
        $this->set_name();
        $this->set_sidebar();
        $this->set_default_content();
    }
}