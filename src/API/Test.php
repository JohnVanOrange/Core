<?php
namespace JohnVanOrange\API;

class Test extends Base {

	public function __construct() {
		parent::__construct();
	}

	/**
		* Success
		*
		* Verify a successful API call
		*
		* @api
		*
		*/

	public function success() {
		return TRUE;
	}

}