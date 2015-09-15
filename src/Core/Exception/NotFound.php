<?php
namespace JohnVanOrange\Core\Exception;

class NotFound extends \JohnVanOrange\Core\Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		$this->setHttpStatus(404);
		parent::__construct($message, $code, $previous);
	}

}
