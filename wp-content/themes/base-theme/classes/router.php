<?php

class Router {
    protected $themePath, $m, $post;

    public function __construct($post) {
        $this->themePath = get_stylesheet_directory();
        $this->post = $post;
        $this->postHandler();
        $this->m = $this->bootstrapMustache();
        $this->content = $this->get_page();
    }

    private function postHandler() {
        if (count($_POST)) {
            $class = $this->classname($this->post->name);

            call_user_func(array($class, 'post_handler'));
            die();
        }
    }

    private function bootstrapMustache() {
        require_once($this->themePath . '/mustache.php/src/Mustache/Autoloader.php');
        Mustache_Autoloader::register();

        return new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader($this->themePath . '/tmpl')
        ));
    }

    private static function mustache() {
        $themePath = get_stylesheet_directory();

        require_once($themePath . '/mustache.php/src/Mustache/Autoloader.php');
        Mustache_Autoloader::register();

        return new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader($themePath . '/tmpl')
        ));
    }

    public static function render_template($name,$data) {
        $m = Router::mustache();
        $tpl = $m->loadTemplate($name);

        return $tpl->render($data);
    }

    private function classname($name) {
        $arr = explode('-', $name);

        foreach ($arr as &$piece) {
            $piece = ucfirst($piece);
        }

        $classname = implode('', $arr);
        $postType = 'PostType' . $name;

        if (class_exists($postType)) {
            $classname = $postType;
        } else if (!class_exists($classname)) {
            $classname = 'Generic';
        }

        return $classname;
    }

    private function get_name($name = null) {
        if ($name) {
            return $name;
        }

        if (class_exists('PostType' . $this->post->post->post_type)) {
            return $this->post->post->post_type;
        }

        return $this->post->name;
    }

    private function get_data($name) {
        $model = $this->classname($name);

        return new $model();
    }

    private function get_template($name) {
        if ($this->classname($name) == 'Generic') {
            $name = 'generic';
        }

        return $this->m->loadTemplate($name);
    }

    private function get_view($name = null) {
        $name = $this->get_name($name);
        $data = $this->get_data($name);

        if (isset($data->partials)) {
            $m = new Mustache_Engine(array(
                'loader' => new Mustache_Loader_FilesystemLoader($this->themePath . '/tmpl'),
                'partials' => $data->partials
            ));

            $tpl = $m->loadTemplate($name);
        } else {
            $tpl = $this->get_template($name);
        }

        return $tpl->render($data);
    }

    private function get_page() {
        if (is_string($this->post)) {
            return $this->get_module();
        }

        $name = $this->get_name();
        $page = array(
            'header' => $this->get_view('header'),
            'content' => $this->get_view($name),
            'footer' => $this->get_view('footer')
        );

        $template = $this->m->loadTemplate('default');

        return $template->render($page);
    }
}