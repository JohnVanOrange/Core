<?php
namespace JohnVanOrange\Core;

class Exception extends \Exception {

	protected $http_status = 200;

	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}

	public function getHttpStatus() {
		return $this->http_status;
	}

	protected function setHttpStatus( $status ) {
		$this->http_status = $status;
	}

}
