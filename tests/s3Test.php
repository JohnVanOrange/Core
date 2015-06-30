<?php
require_once('settings.inc');

class s3Test extends PHPUnit_Framework_TestCase {

  protected $s3;
  protected function setUp(){
    $this->s3 = new JohnVanOrange\API\S3(2);
  }
  protected function tearDown(){
  }

  public function test_get_bucket() {
    $bucket = $this->s3->get_bucket();
    $this->assertInternalType('string',$bucket);
  }

}
