<?php

class WP_MVC_Config {
	private $config, $environment;

	public function __construct() {
		$this->setup();
		$this->setupDatabase();
		$this->setupWP();
		$this->setupAssets();
	}

	private function setup() {
		$json = file_get_contents(__DIR__ . '/config.json');
		$this->config = json_decode($json);

		foreach ($this->config as $key => $val) {
			$this->$key = $val;
		}

		$this->setEnvironment();
	}

	private function setEnvironment() {
		foreach ($this->config->environments as $name => $environment) {
			if ($_SERVER['SERVER_NAME'] == $environment->hostname) {
				$this->environment = $environment;
			}
		}
	}

	private function setupDatabase() {
		foreach($this->config->globalMapping->db as $key => $value) {
			define($value, $this->environment->db->$key);
		}
	}

	private function setupWP() {
		foreach($this->config->globalMapping->wp as $key => $value) {
			define($value, $this->environment->wp->$key);
		}
	}

	private function setupAssets() {
		foreach($this->config->globalMapping->assets as $key => $value) {
			define($value, $this->environment->assets->$key);
		}
	}
}

new WP_MVC_Config();