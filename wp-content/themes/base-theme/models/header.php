<?php

class Header {
    public function __construct() {
        date_default_timezone_set('America/Los_Angeles');

        $this->name = get_bloginfo('name');
        $this->description = get_bloginfo('description');
        $this->date = date('l, F j, Y');
        $this->is_home = is_homepage();
    }
}