<?php

class PageHelper {
    protected $themePath, $post, $name;

    public function __construct($name =  null, $type = 'post') {
        $method = 'get_'. $type;
        $this->name = $name;
        $this->themePath = get_stylesheet_directory();
        $this->post = call_user_func(array($this, $method));
    }

    private function get_post() {
        $pageData = new stdClass();
        $pageData->id = get_the_ID();
        $pageData->post = $pageData->id ? get_post($pageData->id) : '';
        $pageData->name = $this->name ? $this->name : $pageData->post->post_name ;

        return $pageData;
    }

    private function get_archive() {
        $pageData = (object) array(
            'name' => $this->name . '-archive'
        );

        return $pageData;
    }

    public function content() {
        $router = new Router($this->post);

        echo $router->content;
    }

    public function render() {
        if (count($_POST)) {
            $this->content();
        } else {
            $this->full_content();
        }
    }

    private function full_content() {
        get_header();
        $this->content();
        get_footer();
    }
}