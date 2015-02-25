<?php
namespace JohnVanOrange\API\MailTheme;

class Base {

 protected static $theme;

 public static function initialize() {
  $setting = new \JohnVanOrange\API\Setting;

  self::$theme['web_root'] = $setting->get('web_root');
  self::$theme['site_name'] = $setting->get('site_name');
  self::$theme['name'] = $setting->get('theme');
  self::$theme['remote_addr'] = $_SERVER['REMOTE_ADDR'];
 }

}
