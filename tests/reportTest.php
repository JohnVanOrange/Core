<?php
require_once('settings.inc');

class reportTest extends PHPUnit_Framework_TestCase {


	protected function setUp(){
		$this->report = new JohnVanOrange\API\Report;
	}
	protected function tearDown(){
	}

	public function test_report_get() {
		$result = $this->report->get(3)[0];
		$this->assertEquals('Lame', $result['value'], "Unexpected report value");
	}

	public function test_report_all() {
		$result = $this->report->all()[0];
		$this->assertEquals('NSFW', $result['value'], "Unexpected report value");
	}

}