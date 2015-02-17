<?php
require_once('settings.inc');

class imageTest extends PHPUnit_Framework_TestCase {

 protected $image;
 protected $user;
 protected $imageurl;
 protected function setUp(){
  $this->image = new JohnVanOrange\core\Image();
  $this->user = new JohnVanOrange\core\User();
  $this->imageurl = 'http://jvo.io/icons/orange_slice/64.png';
 }
 protected function tearDown(){
  unset($this->user);
 }


 /**** addFromURL ****/
 /**** remove ****/
 public function test_addfromurl_remove() {
  $image = $this->image->addFromURL($this->imageurl);
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
   $user = $this->user->login('testuser', 'testpass')['sid'];
   $image = $this->image->addFromURL($this->imageurl, NULL, $user);
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
   $image = $this->image->addFromURL($this->imageurl);
   $this->assertArrayHasKey('uid', $image, 'UID missing from results.');
   try {
     $this->image->remove($image['uid']);
   }
   catch (Exception $e) {
    return;
   }
   $this->fail('An expected exception has not been raised.');
 }

 /**** like ****/
 public function test_like() { //TODO: do this both authed and unauthed
  $image = $this->image->addFromURL($this->imageurl);
  $user = $this->user->login('testuser', 'testpass')['sid'];
  $this->image->like($image['uid'], $user);
  $result = $this->image->get($image['uid'], $user);
  $this->assertArrayHasKey('like', $result['data'], 'Like data missing from results.');
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->remove($image['uid'], $admin);
 }

 /**** dislike ****/
 public function test_dislike() { //TODO: do this both authed and unauthed
  $image = $this->image->addFromURL($this->imageurl);
  $user = $this->user->login('testuser', 'testpass')['sid'];
  $this->image->dislike($image['uid'], $user);
  $result = $this->image->get($image['uid'], $user);
  $this->assertArrayHasKey('dislike', $result['data'], 'Like data missing from results.');
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->image->remove($image['uid'], $admin);
 }

}
