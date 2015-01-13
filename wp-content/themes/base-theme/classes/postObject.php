<?php
class PostObject {
    public function __construct() {
        $this->build();
    }

    private function build() {
        foreach ($_POST as $key => $value) {
            $this->$key = $this->clean($value);
        }
    }

    private function clean($str) {
        if (!is_array($str)) {
            $str = @trim($str);
            $str = stripslashes($str);
        }

        return $str;
    }
}