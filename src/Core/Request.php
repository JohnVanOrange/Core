<?php
namespace JohnVanOrange\Core;

class Request {

	public function ip() {
		return filter_input(INPUT_SERVER, 'REMOTE_ADDR');
	}

}
