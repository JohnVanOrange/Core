<?php
namespace JohnVanOrange\PublicAPI\MailTheme;

class Pretty extends Base {

 public static function get() {
  parent::initialize();
  self::$theme['background_color'] = "#ed5853";
  self::$theme['text_color'] = "#ffffff";
  self::$theme['button_color'] = "#e5f268";
  self::$theme['button_border'] = "#e1f051";
  self::$theme['button_text'] = "#333333";

  return self::$theme;
 }

}
