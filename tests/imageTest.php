<?php
require_once('settings.inc');

class imageTest extends PHPUnit_Framework_TestCase {

 protected $image;
 protected $user;
 protected function setUp(){
  $this->image = new JohnVanOrange\API\Image();
  $this->user = new JohnVanOrange\API\User();
 }
 protected function tearDown(){
  unset($this->user);
 }


 /**** addFromURL ****/
 
  /**** random ****/
  public function test_random() {
   $image_url = 'https://jvo.io/icons/orange_slice/16.png';
   $image = $this->image->addFromURL($image_url);
   $random = $this->image->random();
   $this->assertArrayHasKey('uid', $random, 'UID missing from results.');
   //cleanup
   $admin = $this->user->login('adminuser', 'testpass')['sid'];
   $this->image->remove($image['uid'], $admin);
  }
 
 /**** remove ****/
 public function test_addfromurl_remove() {
  $image_url = 'https://jvo.io/icons/orange_slice/114.png';
  $image = $this->image->addFromURL($image_url);
  $this->assertArrayHasKey('uid', $image, 'UID missing from results.');
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->remove($image['uid'], $admin);
  try {
   $this->image->get($image['uid']);
  }
  catch (Exception $e) {
   return;
  }
  $this->fail('An expected exception has not been raised.');
 }

 public function test_remove_sameuser() {
   $image_url = 'https://jvo.io/icons/orange_slice/36.png';
   $user = $this->user->login('testuser', 'testpass')['sid'];
   $image = $this->image->addFromURL($image_url, NULL, $user);
   $this->assertArrayHasKey('uid', $image, 'UID missing from results.');
   $this->image->remove($image['uid'], $user);
   try {
     $this->image->get($image['uid']);
   }
   catch (Exception $e) {
     return;
   }
   $this->fail('An expected exception has not been raised.');
 }

 public function test_remove_nologin() {
   $image_url = 'https://jvo.io/icons/orange_slice/32.png';
   $image = $this->image->addFromURL($image_url);
   $this->assertArrayHasKey('uid', $image, 'UID missing from results.');
   try {
     $this->image->remove($image['uid']);
   }
   catch (Exception $e) {
    return;
   }
   $this->fail('An expected exception has not been raised.');
 }

 //there was an issue where reported images couldn't be removed
 public function test_remove_when_reported() {
  $image_url = 'https://jvo.io/icons/orange_slice/152.png';
  $image = $this->image->addFromURL($image_url);
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->report($image['uid'], '1');
  $this->image->remove($image['uid'], $admin);
  try {
   $this->image->get($image['uid']);
  }
  catch (Exception $e) {
   return;
  }
  $this->fail('An expected exception has not been raised.');
 }

 /**** like ****/
 public function test_like() { //TODO: do this both authed and unauthed
  $image_url = 'https://jvo.io/icons/orange_slice/16.png';
  $image = $this->image->addFromURL($image_url);
  $user = $this->user->login('testuser', 'testpass')['sid'];
  $this->image->like($image['uid'], $user);
  $result = $this->image->get($image['uid'], $user);
  $this->assertArrayHasKey('like', $result['data'], 'Like data missing from results.');
  //cleanup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->remove($image['uid'], $admin);
 }

 /**** dislike ****/
 public function test_dislike() { //TODO: do this both authed and unauthed
  $image_url = 'https://jvo.io/icons/orange_slice/30.png';
  $image = $this->image->addFromURL($image_url);
  $user = $this->user->login('testuser', 'testpass')['sid'];
  $this->image->dislike($image['uid'], $user);
  $result = $this->image->get($image['uid'], $user);
  $this->assertArrayHasKey('dislike', $result['data'], 'Like data missing from results.');
  //cleanup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->remove($image['uid'], $admin);
 }

 /**** save and unsave ****/
 public function test_save() {
  $image_url = 'https://jvo.io/icons/orange_slice/16.png';
  $image = $this->image->addFromURL($image_url);
  $user = $this->user->login('testuser', 'testpass')['sid'];
  $this->image->save($image['uid'], $user);
  $result = $this->image->get($image['uid'], $user);
  $this->assertArrayHasKey('save', $result['data'], 'Save data missing from results.');
  $this->image->unsave($image['uid'], $user);
  $result = $this->image->get($image['uid'], $user);
  $this->assertArrayNotHasKey('data', $result, 'Save data should be missing from results.');
  //cleanup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->remove($image['uid'], $admin);
 }

  /**** stats ****/
 public function test_stats() {
  $stats = $this->image->stats();
  $this->assertArrayHasKey('images', $stats, 'Image data missing from stats.');
  $this->assertArrayHasKey('reports', $stats, 'Report data missing from stats.');
  $this->assertArrayHasKey('approved', $stats, 'Approved data missing from stats.');
 }

 /**** approve ****/
 public function test_approve() {
  $image_url = 'https://jvo.io/icons/orange_slice/114.png';
  $image = $this->image->addFromURL($image_url);
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->approve($image['uid'], $admin);
  $result = $this->image->get($image['uid'], $admin);
  $this->assertEquals('1', $result['approved'], 'Approve status incorrect.');
 }

}
