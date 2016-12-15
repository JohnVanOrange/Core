<?php
require_once('settings.inc');

class imageFilterTest extends PHPUnit_Framework_TestCase {

	protected $db;
	protected $image;
	protected $user;
	protected function setUp(){
		$this->db = new JohnVanOrange\API\DB('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
		$this->image = new JohnVanOrange\API\Image();
		$this->user = new JohnVanOrange\API\User();
	}
	protected function tearDown(){
	}

	public function test_count() {
		//setup
		$user = $this->user->login('testuser', 'testpass')['sid'];
		$image_url = 'https://jvo.io/icons/lips/16.png';
		$image1 = $this->image->addFromURL($image_url, NULL, $user);
		$image_url = 'https://jvo.io/icons/lips/32.png';
		$image2 = $this->image->addFromURL($image_url, NULL, $user);
		$image_url = 'https://jvo.io/icons/lips/36.png';
		$image3 = $this->image->addFromURL($image_url, NULL, $user);
		$image_url = 'https://jvo.io/icons/lips/64.png';
		$image4 = $this->image->addFromURL($image_url, NULL, $user);
		//test
		$options = [
			'limit' => 3
		];
		$query = new JohnVanOrange\API\ImageFilter\Base($options);
		$results = $this->db->fetch($query);
		$this->assertEquals(3, count($results), 'Incorrect number of results');
		//cleanup
		$this->image->remove($image1['uid'], $user);
		$this->image->remove($image2['uid'], $user);
		$this->image->remove($image3['uid'], $user);
		$this->image->remove($image4['uid'], $user);
	}

}