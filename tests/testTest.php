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

	public function test_reply() {
		$result = $this->test->reply(['text' => "Hello World"]);
		$this->assertEquals("Hello World", $result['text'], 'Reply did not match');
	}

}