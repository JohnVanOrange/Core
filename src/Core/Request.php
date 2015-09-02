<?php
namespace JohnVanOrange\Core;

class Request {

	public function ip() {
		$ip = '';
		if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
		return $ip;
	}

}
