<?php
require_once('settings.inc');

class tagTest extends PHPUnit_Framework_TestCase {

 protected $tag;
 protected $image;
 protected $user;
 protected $setting;
 protected function setUp(){
  $this->image = new JohnVanOrange\API\Image();
  $this->tag = new JohnVanOrange\API\Tag();
  $this->user = new JohnVanOrange\API\User();
  $this->setting = new JohnVanOrange\API\Setting();
 }
 protected function tearDown(){
 }
 
 /****add****/ //TODO: create for invalid name, invalid uid and, banned tags
 public function test_add() {
  //setup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->setting->update('tags_need_auth', 0, $admin);
  //test
  $image_url = 'http://jvo.io/icons/lips/30.png';
  $image = $this->image->addFromURL($image_url);
  $tag = $this->tag->add('random tag', $image['uid']);
  $this->assertEquals('random tag', $tag['tags'][0]['name'], 'Add tag results don\'t list newly added tag.');
  //cleanup
  $this->image->remove($image['uid'], $admin);
 }

 public function test_add_authreq() {
  //setup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->setting->update('tags_need_auth', 1, $admin);
  //test
  $image_url = 'http://jvo.io/icons/lips/30.png';
  $image = $this->image->addFromURL($image_url);
  $user = $this->user->login('testuser', 'testpass')['sid'];
  $tag = $this->tag->add('random tag', $image['uid'], $user);
  $this->assertEquals('random tag', $tag['tags'][0]['name'], 'Add tag results don\'t list newly added tag.');
  //cleanup
  $this->image->remove($image['uid'], $admin);
 }

 public function test_add_authreq_fail() {
  //setup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->setting->update('tags_need_auth', 1, $admin);
  //test
  $image_url = 'http://jvo.io/icons/lips/30.png';
  $image = $this->image->addFromURL($image_url);
  try {
    $tag = $this->tag->add('random tag', $image['uid']);
  }
  catch (Exception $e) {
    //cleanup
    $this->image->remove($image['uid'], $admin);
    return;
  }
  $this->fail('An expected exception has not been raised.');
 }
 
 public function test_add_dupe() {
  //setup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->setting->update('tags_need_auth', 0, $admin);
  //test
  $image_url = 'http://jvo.io/icons/lips/30.png';
  $image = $this->image->addFromURL($image_url);
  $this->tag->add('random tag', $image['uid']);
   try {
     $this->tag->add('random tag', $image['uid']);
   }
   catch (Exception $e) {
    //cleanup
    $this->image->remove($image['uid'], $admin);
    return;
   }
   $this->fail('An expected exception has not been raised.');
 }
 
 
 /****get****/ //TODO get by basename/name
 public function test_get_image() {
  //setup
  $admin = $this->user->login('adminuser', 'testpass')['sid'];
  $this->setting->update('tags_need_auth', 0, $admin);
  //test
  $image_url = 'http://jvo.io/icons/lips/30.png';
  $image = $this->image->addFromURL($image_url);
  $this->tag->add('other tag', $image['uid']);
  $tags = $this->tag->get($image['uid']);
  $this->assertEquals('other tag', $tags[0]['name'], 'Get tag results don\'t list newly added tag.');
  //cleanup
  $this->image->remove($image['uid'], $admin);
 }
 
 
}