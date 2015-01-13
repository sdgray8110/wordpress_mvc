<?php

class BaseFooter {
	public function bottom() {
		echo Module::content('footerBottom');
	}
}

class FooterBottom {
	public function __construct() {

	}
}