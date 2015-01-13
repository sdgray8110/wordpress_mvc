<?php

class WP_MVC_Page {
    protected $id, $post, $encoded_page_data;

    protected function get_post() {
        $this->id = get_the_ID();
        $this->post =  get_post($this->id);
    }

    protected function set_title() {
        $this->title = $this->post->post_title;
    }

    protected function set_name() {
        if ($this->post->post_type == 'page') {
            $this->name = $this->post->post_name;
        } else {
            $this->name = $this->post->post_type;
        }
    }

    protected function set_sidebar() {
        $this->sidebar = Module::content('sidebar');
    }

    protected function set_default_content() {
        $this->content = apply_filters('the_content', $this->post->post_content);
    }

    protected function encode_page_data() {
        $this->encoded_page_data = array(
            'ajaxurl' => admin_url('admin-ajax.php')
        );

        if (method_exists($this, 'page_data_extensions')) {
            $extensions = $this->page_data_extensions();

            $this->encoded_page_data = array_merge($this->encoded_page_data, $extensions);
        }
    }

    protected function set_page_data() {
        $this->encode_page_data();
        $this->page_data = json_encode($this->encoded_page_data);
    }

    protected function get_partial_as_string($name) {
        $fmt = '%s/tmpl/partials/%s.mustache';
        $path = sprintf($fmt, get_stylesheet_directory(), $name);

        return file_get_contents($path);
    }
}