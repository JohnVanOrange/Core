<?php
namespace JohnVanOrange\core;

class Mail extends \Swift_Message {
 
 private $mailer, $message;

 public function __construct() {
  $transport = \Swift_MailTransport::newInstance();
  $this->mailer = \Swift_Mailer::newInstance($transport);
  $this->message = $this::newInstance();
 }

 public function __destruct() {
 }
 
 public function sendMessage($to, $subject, $template, $data) {
  $setting = new Setting;
  $toEmail = $to[0];
  $toName = NULL;
  if (isset($to[1])) $toName = $to[1];
  
  $text_body = $this->loadTemplate($template, $data);
  $html_body = $this->loadTemplate($template, $data, 'html');
  
  $this->message->setFrom(SITE_EMAIL, $setting->get('site_name'))
                ->setTo($toEmail, $toName)
                ->setSubject($subject)
                ->setBody($text_body);
  
  if (isset($html_body)) $this->message->addPart($html_body, 'text/html');
  
  return $this->send();
 }
 
 public function sendAdminMessage($subject, $template, $data) {
  $setting = new Setting;
  $site_name = $setting->get('site_name');
  return $this->sendMessage([ADMIN_EMAIL, $site_name . ' Admin'], $subject, $template, $data);
 }
 
 public function setTo($to, $name = NULL) {
  return $this->message->setTo($to, $name);
 }
 
 public function setFrom($from, $name = NULL) {
  return $this->message->setFrom($from, $name);
 }
 
 public function setSubject($subject) {
  return $this->message->setSubject($subject);
 }
 
 public function setBody($body, $contentType = NULL) {
  return $this->message->setBody($body, $contentType);
 }
 
 public function send() {
  return $this->mailer->send($this->message);
 }
 
 private function loadTemplate($template, $data, $type = 'text') {
  $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../mail_templates/'.$type);
  $twig = new \Twig_Environment($loader);
  try {
   $template = $twig->loadTemplate($template . '.twig');
  }
  catch(\Exception $e) { //when failing to load template, better to have a blank message
   return null; 
  }
  
  $setting = new Setting;
  $themeclass = 'JohnVanOrange\core\MailTheme\\' . ucfirst($setting->get('theme'));
  $data['theme'] = $themeclass::get();
  
  return $template->render($data);
 }
 
}
