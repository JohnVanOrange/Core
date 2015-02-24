<?php
namespace JohnVanOrange\PublicAPI;

class Message extends Base {

 public function __construct() {
  parent::__construct();
 }

 /**
  * Admin message
  *
  * Send a message to site admins.
  *
  * @api
  *
  * @param string $message Text of the message to send.
  * @param string $name Name of person sending message.
  * @param string $email Email address of user sending message.
  */

 public function admin($message, $name = 'Unknown User', $email = 'not-provided@example.com') {
  $mail = new Mail();
  $data = [
   'message'     => $message,
   'name'        => $name,
   'reply_email' => $email
  ];
  $mail->sendAdminMessage('Message to Admin', 'admin_message', $data);
  if ($email != 'not-provided@example.com') {
   $mail = new Mail();
   $mail->sendMessage([$email, $name], 'Message Sent to Admin', 'admin_message_user_copy', ['message' => $message]);
  }
  return array(
   'message' => _('Message sent')
  );
 }

}
