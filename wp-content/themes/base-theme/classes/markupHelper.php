<?php

class markupHelper {
    private static function init() {
        require_once(get_stylesheet_directory() . '/mustache.php/src/Mustache/Autoloader.php');
        Mustache_Autoloader::register();
    }

    public static function render_template($template_name, $data) {
        markupHelper::init();
        $themePath = get_stylesheet_directory();
        $m = new Mustache_Engine(array(
            'loader' => new Mustache_Loader_FilesystemLoader($themePath . '/tmpl')
        ));

        $template = $m->loadTemplate($template_name);

        return $template->render($data);
    }
}