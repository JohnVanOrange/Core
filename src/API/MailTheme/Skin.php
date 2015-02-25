<?php
namespace JohnVanorange\API\MailTheme;

class Skin extends Base {

 public static function get() {
  parent::initialize();
  self::$theme['background_color'] = "#000000";
  self::$theme['text_color'] = "#ffffff";
  self::$theme['button_color'] = "#d60b15";
  self::$theme['button_border'] = "#be0a13";
  self::$theme['button_text'] = "#ffffff";

  return self::$theme;
 }

}
