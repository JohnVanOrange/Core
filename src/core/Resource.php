<?php
namespace JohnVanOrange\core;

class Resource extends Base {
 
 private $user;

 public function __construct() {
  parent::__construct();
  $this->user = new User;
 }

 public function add($type, $image = NULL, $sid = NULL, $value = NULL, $public = FALSE, $tag_id = NULL) {
  $current = $this->user->current($sid);
  $user_id = NULL;
  $unauth_user = NULL;
  if (isset($current['id'])) {
   $user_id = $current['id'];
  } else {
   $unauth_user = $this->user->unAuthUser();
  }
  
  $ip = '';
  if (isset($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
  $data = [
   'ip' => $ip,
   'image' => $image,
   'user_id' => $user_id,
   'value' => $value,
   'type' => $type,
   'tag_id' => $tag_id,
   'unauth_user' => $unauth_user
  ];
  if ($public) $data['public'] = 1;
  $query = new \Peyote\Insert('resources');
  $query->columns(array_keys($data))
        ->values(array_values($data));
  return $this->db->fetch($query);
 }
 
 public function merge($to, $from) {
  if (!$this->user->isAdmin()) throw new \Exception(_('Must be an admin to access method'), 401);
  $query = new \Peyote\Update('resources');
  $query->set(['image' => $to])
        ->where('image', '=', $from);
  $this->db->fetch($query);
  return TRUE;
 }
 
}