<?php

class BaseHeader {
	public function top() {
		echo Module::content('headerTop');
	}

	public function bottom() {
		echo Module::content('headerBottom');
	}
}

class HeaderTop {
	public function __construct() {
		$this->language = get_bloginfo('language');
		$this->charset = get_bloginfo( 'charset' );
		$this->title = wp_title( '|', false, 'right' );
		$this->pingback_url = get_bloginfo( 'pingback_url' );
		$this->stylesheet_url = $this->get_stylesheet();
	}

	private function get_stylesheet() {
		$fmt = '%s/%s';
		return sprintf($fmt, get_stylesheet_directory_uri(), BASE_STYLESHEET);
	}
}

class HeaderBottom {
	public function __construct() {
		$classes = get_body_class();
		$this->bodyClass = implode(' ', $classes);
	}
}