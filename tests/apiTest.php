<?php
require_once('settings.inc');

class apiTest extends PHPUnit_Framework_TestCase {

	protected $api;
	protected function setUp(){
		$this->api = new JohnVanOrange\API\API();
	}
	protected function tearDown(){
	}

	public function test_call_success() {
		$result = $this->api->call('test/success');
		$this->assertTrue($result, 'API call did not return expected TRUE result');
	}

	public function test_call_no_method_exists() {
		try {
			$this->api->call('test/not_a_method');
		}
		catch (ReflectionException $e) {
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

	public function test_call_no_class_exists() {
		try {
			$this->api->call('not_a_class/not_a_method');
		}
		catch (Exception $e) {
			return;
		}
		$this->fail('An expected exception has not been raised.');
	}

}