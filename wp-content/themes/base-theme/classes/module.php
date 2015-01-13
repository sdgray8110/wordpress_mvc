<?php

class Module {
    public static function content($name, $extensions = array()) {
        return Module::get_view($name, $extensions);
    }

    private static function get_view($name, $extensions = array()) {
        $data = Module::get_data($name, $extensions);
        $partials = isset($data->partials) ? $data->partials : null;
        $tpl = Module::get_template($name, $partials);

        return $tpl->render($data);
    }

    private static function get_template($name, $partials) {
        $m = Module::m($partials);

        return $m->loadTemplate($name);
    }

    private static function get_data($name, $extensions = array()) {
        $model = Module::classname($name);

        return new $model($extensions);
    }

    private static function m($partials, $tplPath = null) {
        $tplPath = $tplPath ? $tplPath : '/tmpl/modules';
        $themePath = get_stylesheet_directory();

        require_once($themePath . '/mustache.php/src/Mustache/Autoloader.php');
        Mustache_Autoloader::register();
        $args = array(
            'loader' => new Mustache_Loader_FilesystemLoader($themePath . $tplPath)
        );

        if ($partials) {
            $args['partials'] = $partials;
        }

        return new Mustache_Engine($args);
    }

    private static function classname($name) {
        $classname = ucfirst($name);

        return $classname;
    }

    public static function render_submodule_partial($name, $data) {
        $tplPath = '/tmpl/modules/partials';
        $m = Module::m(null, $tplPath);
        $tpl = $m->loadTemplate($name);

        return $tpl->render($data);
    }

    public static function render_partial($name, $data) {
        $tplPath = '/tmpl/partials';
        $m = Module::m(null, $tplPath);
        $tpl = $m->loadTemplate($name);

        return $tpl->render($data);
    }

    public static function get_partial_as_string($name) {
        $fmt = '%s/tmpl/partials/%s.mustache';
        $path = sprintf($fmt, get_stylesheet_directory(), $name);

        return file_get_contents($path);
    }
}