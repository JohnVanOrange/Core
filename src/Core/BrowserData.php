<?php
namespace JohnVanOrange\Core;

class BrowserData {

	public function post( $value ) {
		if ( isset($_POST[$value]) ) {
			return $_POST[$value];
		} else {
			return null;
		}
	}

	public function get( $value ) {
		if ( isset($_GET[$value]) ) {
			return $_GET[$value];
		} else {
			return null;
		}
	}

	public function cookie( $value ) {
		if ( isset($_COOKIE[$value]) ) {
			return $_COOKIE[$value];
		} else {
			return null;
		}
	}

	public function server( $value ) {
		if ( isset($_SERVER[$value]) ) {
			return $_SERVER[$value];
		} else {
			return null;
		}
	}

}