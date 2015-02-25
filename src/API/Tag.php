<?php
namespace JohnVanOrange\API;

class Tag extends Base {

 private $res;

 public function __construct() {
  parent::__construct();
  $this->res = new Resource;
 }

 /**
  * Add tag
  *
  * Add tags to images
  *
  * @api
  *
  * @param string $name The tag value to be added to the image.
  * @param string $image 6-digit image ID.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
  */

 public function add($name, $image, $sid = NULL) {
  if (strlen($name) < 1 OR $name == NULL) throw new \Exception(_('Tag name cannot be empty'));
  if (strlen($image) !== 6) throw new \Exception(_('Invalid image UID'));
  $tag = htmlspecialchars(trim(stripslashes($name)));
  $slug = $this->text2slug($tag);
  if ($slug == '') throw new \Exception(_('Invalid tag name'), 1030);
  $blacklist = new Blacklist;
  if ($blacklist->check($slug)) throw new \Exception(_('Tag has been banned'), 1031);
  $query = new \Peyote\Select('tag_list');
  $query->columns('id')
        ->where('basename', '=', $slug);
  $result = $this->db->fetch($query);
  $tag_id = NULL;
  if (isset($result[0]['id'])) $tag_id = $result[0]['id'];
  if(!isset($tag_id)) {
   $tag_id = $this->addtoList($tag);
  }
  //check for dupe
  $query = new \Peyote\Select('resources');
  $query->where('image', '=', $image)
        ->where('value', '=', $tag_id)
        ->where('type', '=', 'tag');
  $result = $this->db->fetch($query);
  if ($result) throw new \Exception(_('Tag already exists'));
  $this->res->add('tag', $image, $sid, $tag_id, TRUE);

  return [
   'message' => _('Tag added'),
   'tags' => $this->get($image)
  ];
 }

 /**
  * Get tags
  *
  * List the tags for an image
  *
  * @api
  *
  * @param string $value The 6-digit UID for an image. Also can accept a tag basename if the search_by parameter is set to basename.
  * @param string $search_by Valid values are 'image' or 'basename'.
  */

 public function get($value, $search_by='image') {
  $setting = new Setting;
  if (!$value) throw new \Exception(_('Tag value not set'), 404);
  switch ($search_by) {
   case 'name':
    $search_by = 'basename';
    $value = $this->text2slug($value);
   case 'basename':
   case 'image':
    break;
   default:
    $search_by = 'image';
  break;
  }
  $query = new \Peyote\Select('resources');
  $query->columns('tag_list.name, tag_list.id, basename, uid')
        ->join('tag_list', 'inner')
        ->on('tag_list.id', '=', 'resources.value')
        ->join('images', 'inner')
        ->on('images.uid', '=', 'resources.image')
        ->where($search_by, '=', $value)
        ->where('resources.type', '=', 'tag');
  $results = $this->db->fetch($query);
  if (!$results && $search_by == 'basename') throw new \Exception(_('No images with specified tag'), 404);
  foreach ($results as $i => $r) {
   $url = parse_url($setting->get('web_root'));
   $results[$i]['url'] = '/t/'.$r['basename'];
  }
  return $results;
 }

 /**
  * All images with tag
  *
  * Get all images that have a certain tag.
  *
  * @api
  *
  * @param string $tag The basename value for a particular tag.
  */

 public function all($tag) {
  $i = $this->image = new Image;
  $images = $this->get($tag, 'basename');
  $results = array();
  foreach($images as $image) {
   try {
    $results[] = $i->get($image['uid']);
   }
   catch(\Exception $e) {
    if ($e->getCode() != 403) {
     throw new \Exception($e);
    }
   }
  }
  return $results;
 }

 /**
  * Tag suggest
  *
  * This is used to find suggested tags by giving it parts of text, and it will return the 10 closest matches for existing tags.
  *
  * @api
  *
  * @param string $term Partial text that is used to query for existing tag names.
  */

 public function suggest($term) {
  $query = new \Peyote\Select('tag_list');
  $query->columns('name')
        ->where('name', 'LIKE', '%'.$term.'%')
        ->limit(10);
  $results = $this->db->fetch($query);
  foreach ($results as $r) {
   $return[] = stripslashes($r['name']);
  }
  return $return;
 }

 /**
  * Top tags
  *
  * Returns the top 200 used tags and a count of the number of times they are used.
  *
  * @api
  */

 public function top() {
  $query = new \Peyote\Select('resources');
  $query->columns('tag_list.name, tag_list.basename, COUNT(*) as count')
        ->join('tag_list', 'inner')
        ->on('tag_list.id', '=', 'resources.value')
        ->where('resources.type', '=', 'tag')
        ->groupBy('resources.value')
        ->orderBy('COUNT(*)', 'DESC')
        ->limit(200);
  $results = $this->db->fetch($query);
  return $results;
 }

 /**
 * Remove tag
 *
 * Removes a tag from an image. Must be an admin to access.
 *
 * @api
 *
 * @param int $id Tag id.
 * @param string $uid 6-digit image id.
 * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
 */

 public function remove($id, $uid, $sid = NULL) {
  $user = new User;
  $current = $user->current($sid);
  if ($current['type'] < 2) throw new \Exception(_('Must be an admin to access method'), 401);

  $query = new \Peyote\Delete('resources');
  $query->where('image', '=', $uid)
        ->where('value', '=', $id)
        ->where('type', '=', 'tag');
  $this->db->fetch($query);

  return [
   'message' => _('Removed tag from image')
  ];
 }

 /**
 * Remove tag from all images
 *
 * Completely removes a specific tag from all images it is on. Must be an admin to access.
 *
 * @api
 *
 * @param int $id Tag id.
 * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
 */

 public function removeAll($id, $sid = NULL) {
  $user = new User;
  $current = $user->current($sid);
  if ($current['type'] < 2) throw new \Exception(_('Must be an admin to access method'), 401);

  $query = new \Peyote\Delete('resources');
  $query->where('value', '=', $id)
        ->where('type', '=', 'tag');
  $this->db->fetch($query);

  $query = new \Peyote\Delete('tag_list');
  $query->where('id', '=', $id);
  $this->db->fetch($query);

  return [
   'message' => _('Completely removed tag')
  ];
 }

 /**
 * Rename tag
 *
 * Changes the display name of a tag. The basename will not change. Must be admin to access.
 *
 * @api
 *
 * @param int $id Tag id.
 * @param string $name New tag name.
 * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
 */

 public function rename($id, $name, $sid = NULL) {
  $user = new User;
  $current = $user->current($sid);
  if ($current['type'] < 2) throw new \Exception(_('Must be an admin to access method'), 401);

  $query = new \Peyote\Update('tag_list');
  $query->set(['name' => $name])
        ->where('id', '=', $id);
  $this->db->fetch($query);

  return [
   'message' => _('Tag name updated')
  ];
 }

 private function addtoList($name) {
  $basename = $this->text2slug($name);
  $query = new \Peyote\Select('tag_list');
  $query->columns('id')
        ->where('basename', '=', $basename);
  $result = $this->db->fetch($query);
  if ($result) return $result[0]['id'];
  $query = new \Peyote\Insert('tag_list');
  $query->columns(['name','basename'])
        ->values([$name, $basename]);
  $s = $this->db->prepare($query->compile());
  $s->execute($query->getParams());
  return $this->db->lastInsertId();
 }

 /**
 * Report tag
 *
 * Allows reporting of problematic tags so they may undergo review.
 *
 * @api
 *
 * @param string $tag_id The numeric id of an tag.
 * @param int $type Number value representing the reason type which can be found in report/all
 * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
 */

 public function report($tag_id, $type, $sid = NULL) {
  $setting = new Setting;
  $site_name = $setting->get('web_root');
  if (!isset($tag_id)) throw new \Exception(_('No tag specified'));
  if (!isset($type)) throw new \Exception(_('No report type specified'));
  //Add report
  $this->res->add('treport', NULL, $sid, $type, FALSE, $tag_id);
  //TODO: need a way to retrieve tag data by tag_id
  $message = new Mail();
  $data = [];
  $message->sendAdminMessage('New Reported Tag for '. $site_name, 'reported_tag', $data);
  return array(
   'message' => _('Tag Reported')
  );
 }

}
