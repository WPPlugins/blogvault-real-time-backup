<?php

if (!defined('ABSPATH')) exit;
if (!class_exists('BVWPAPI')) :
	class BVWPAPI {
		public $settings;

		public function __construct($settings) {
			$this->settings = $settings;
		}

		public function pingbv($method, $body, $public = false) {
			if ($public) {
				$this->create_request_params($method, $body, $public);
			} else {
				$api_public_key = $this->settings->getOption('bvApiPublic');
				if (!empty($api_public_key) && (strlen($api_public_key) >= 32)) {
					$this->create_request_params($method, $body, $api_public_key);
				}
			}
		}

		public function create_request_params($method, $body, $pubkey) {
			$account = BVAccount::find($this->settings, $pubkey);
			$url = $account->authenticatedUrl($method);
			$this->http_request($url, $body);
		}

		public function http_request($url, $body, $headers = array()) {
			$_body = array(
				'method' => 'POST',
				'timeout' => 15,
				'body' => $body
			);
			if (!empty($headers)) {
				$_body['headers'] = $headers;
			}
			return wp_remote_post($url, $_body);
		}
	}
endif;