<?php
require_once('settings.inc');

class publicApiTest extends PHPUnit_Framework_TestCase {

	protected function setUp(){
	}
	protected function tearDown(){
	}

	public function test_call_success() {
		$publicApi = new JohnVanOrange\API\PublicAPI();
		$publicApi->setClass('test');
		$publicApi->setMethod('success');
		$result = $publicApi->output();
		$json = json_encode(['response' => TRUE]);
		$this->assertJsonStringEqualsJsonString($result, $json, 'JSON did not match expected result');
	}

	public function test_call_reply() {
		$publicApi = new JohnVanOrange\API\PublicAPI();
		$publicApi->setClass('test');
		$publicApi->setMethod('reply');
		$publicApi->setRequest(['text' => "Test Message"]);
		$result = $publicApi->output();
		$json = json_encode(['response' => "Test Message"]);
		$this->assertJsonStringEqualsJsonString($result, $json, 'JSON did not match expected result');
	}
}