<?php
require_once('settings.inc');

class testTest extends PHPUnit_Framework_TestCase {

	protected $test;
	protected function setUp(){
		$this->test = new JohnVanOrange\API\Test();
	}
	protected function tearDown(){
	}

	public function test_success() {
		$result = $this->test->success();
		$this->assertTrue($result, 'Success did not return TRUE');
	}

}