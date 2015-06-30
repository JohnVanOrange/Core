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

	/**
	* Reply
	*
	* Replies back with the text sent
	*
	* @api
	*
	* @param string $text Text that is sent back in the response.
	*/

	public function reply($text) {
		return $text;
	}

}