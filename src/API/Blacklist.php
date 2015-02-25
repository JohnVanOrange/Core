<?php
namespace JohnVanOrange\API;

class Blacklist extends Base {

 public function __construct() {
  parent::__construct();
 }

 /**
  * Add to blacklist
  *
  * Add tag name to blacklist. Must be admin to access.
  *
  * @param string $name The tag value to be added to the blacklist.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
  *
  * @return array
  */

 public function add($name, $sid = NULL) {
  $user = new User;
  if (!$user->isAdmin($sid)) throw new \Exception(_('Must be an admin to access method'), 401);
  if (strlen($name) < 1 OR $name == NULL) throw new \Exception(_('Tag name cannot be empty'));
  $tag = htmlspecialchars(trim(stripslashes($name)));
  $slug = $this->text2slug($tag);
  if ($slug == '') throw new \Exception(_('Invalid tag name'), 1030);

  $res = new Resource;
  $res->add('bl', NULL, $sid, $slug);

  return [
   'message' => _('Tag added to blacklist'),
  ];
 }

 /**
  * Check blacklist
  *
  * Checks to see if tag name is listed on the blacklist.
  *
  * @param string $name The tag value to be added to the blacklist.
  *
  * @return bool
  */

 public function check($name) {
  if (strlen($name) < 1 OR $name == NULL) throw new \Exception(_('Tag name cannot be empty'));
  $tag = htmlspecialchars(trim(stripslashes($name)));
  $slug = $this->text2slug($tag);
  if ($slug == '') throw new \Exception(_('Invalid tag name'), 1030);

  $query = new \Peyote\Select('resources');
  $query->where('value', '=', $slug)
        ->where('type', '=', 'bl');
  if ($this->db->fetch($query)) return TRUE;
  return FALSE;
 }

}
