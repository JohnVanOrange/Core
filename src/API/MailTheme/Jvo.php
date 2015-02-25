<?php
namespace JohnVanOrange\API\MailTheme;

class Jvo extends Base {

 public static function get() {
  parent::initialize();
  self::$theme['background_color'] = "#4d688d";
  self::$theme['text_color'] = "#f3f3f3";
  self::$theme['button_color'] = "#ff8300";
  self::$theme['button_border'] = "#e67600";
  self::$theme['button_text'] = "#f3f3f3";

  return self::$theme;
 }

}
