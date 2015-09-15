<?php
namespace JohnVanOrange\Core\Exception;

class NotAllowed extends \JohnVanOrange\Core\Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		$this->setHttpStatus(401);
		parent::__construct($message, $code, $previous);
	}

}
