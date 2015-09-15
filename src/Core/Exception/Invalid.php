<?php
namespace JohnVanOrange\Core\Exception;

class Invalid extends \JohnVanOrange\Core\Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}

}