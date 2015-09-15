<?php
namespace JohnVanOrange\Core\Exception;

class Forbidden extends \JohnVanOrange\Core\Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		$this->setHttpStatus(403);
		parent::__construct($message, $code, $previous);
	}

}
