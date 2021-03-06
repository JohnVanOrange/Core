<?php
namespace JohnVanOrange\API;
use Imagick;

class Image extends Base {

 private $user;

 public function __construct() {
  parent::__construct();
  $this->user = new User;
 }

 /**
  * Like image
  *
  * Like an image.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  * @param string $session Session ID for non-logged in users. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function like($image, $sid = NULL, $session = NULL) {
  if (!$image) throw new \Exception('Must provide image ID', 1040);
  $current = $this->user->current($sid);
  $query = new \Peyote\Delete('resources');
  $query->where('image', '=', $image)
        ->where('user_id', '=', $current['id'])
        ->where('type', '=', 'dislike');
  $this->db->fetch($query);
  if ($session) {
   $query = new \Peyote\Delete('resources');
   $query->where('image', '=', $image)
         ->where('unauth_user', '=', $session)
         ->where('type', '=', 'dislike');
   $this->db->fetch($query);
  }
  $res = new \JohnVanOrange\Core\Resource('like', $sid);
  $res->setImage($image);
  $res->add();
  return array(
   'message' => 'Image liked',
   'liked' => 1,
   'uid' => $image
  );
 }

 /**
  * Dislike image
  *
  * Dislike an image.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  * @param string $session Session ID for non-logged in users. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function dislike($image, $sid = NULL, $session = NULL) {
  if (!$image) throw new \Exception('Must provide image ID', 1040);
  $current = $this->user->current($sid);
  $query = new \Peyote\Delete('resources');
  $query->where('image', '=', $image)
        ->where('user_id', '=', $current['id'])
        ->where('type', '=', 'like');
  $this->db->fetch($query);
  if ($session) {
   $query = new \Peyote\Delete('resources');
   $query->where('image', '=', $image)
         ->where('unauth_user', '=', $session)
         ->where('type', '=', 'like');
   $this->db->fetch($query);
  }
  $res = new \JohnVanOrange\Core\Resource('dislike', $sid);
  $res->setImage($image);
  $res->add();
  return array(
   'message' => 'Image disliked',
   'liked' => 0,
   'uid' => $image
  );
 }

 /**
  * Save image
  *
  * Save an image for viewing later. Must be logged in to use this method.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function save($image, $sid=NULL) {
  if (!$image) throw new \Exception('Must provide image ID', 1040);
  $current = $this->user->current($sid);
  if (!$current) throw new \Exception('Must be logged in to favorite images',1020);
  $res = new \JohnVanOrange\Core\Resource('save', $sid);
  $res->setImage($image);
  $res->add();
  return array(
   'message' => 'Image favorited',
   'saved' => 1,
   'uid' => $image
  );
 }

 /**
  * Unsave image
  *
  * Stop saving a previously saved image. Must be logged in to use this method.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function unsave($image, $sid=NULL) {
  if (!$image) throw new \Exception('Must provide image ID', 1040);
  $current = $this->user->current($sid);
  if (!$current) throw new \Exception('Must be logged in to unsave images',1021);
  $query = new \Peyote\Delete('resources');
  $query->where('image', '=', $image)
        ->where('user_id', '=', $current['id'])
        ->where('type', '=', 'save');
  $this->db->fetch($query);
  return array(
   'message' => 'Image unsaved',
   'saved' => 0,
   'uid' => $image
  );
 }

 /**
  * Approve image
  *
  * Approves an image. If the image was reported, it will resolve all reports. If the image was hidden, it will now be displayed. Must be logged on as admin to access this method.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  * @param bool $nsfw If an image should be marked as approved, but NSFW, setting this to 'true' or '1' will mark the image that way.
  */

 public function approve($image, $sid=NULL, $nsfw=NULL) {
  $current = $this->user->current($sid);
  if ($current['type'] < 2) throw new \JohnVanOrange\Core\Exception\NotAllowed('Must be an admin to access method', 401);
  if ($nsfw === TRUE) $nsfw = 1;
  $query = new \Peyote\Delete('resources');
  $query->where('image', '=', $image)
        ->where('type', '=', 'report');
  $this->db->fetch($query);
  $query = new \Peyote\Update('images');
  $query->set([
               'display' => 1,
               'approved' => 1,
               'nsfw' => $nsfw
              ])
        ->where('uid', '=', $image);
  $this->db->fetch($query);
  return array(
   'message' => 'Image approved',
   'uid' => $image
  );
 }

 /**
  * Can User Remove
  *
  * Check to see if a user has permissions to remove an image.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function canRemove($image, $sid=NULL) {
   $current_id = $this->user->current($sid)['id'];
   $data = $this->get($image, $sid);
   $uploader = 'invalid';
   if (isset($data['uploader']['id'])) $uploader = $data['uploader']['id'];
   if (!$this->user->isAdmin($sid) AND ($current_id != $uploader)) return FALSE;
   return TRUE;
 }

 /**
  * Remove image
  *
  * Removes an image and all data associated with it. Only the user that uploaded the image or an admin can use this method.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function remove($image, $sid=NULL) {
  if (!$this->canRemove($image, $sid)) throw new \JohnVanOrange\Core\Exception\NotAllowed('You don\'t have permission to remove this image', 401);
  $data = $this->get($image, $sid);
  //clean up resources
  $query = new \Peyote\Delete('resources');
  $query->where('image', '=', $image);
  $this->db->fetch($query);
  //clean up media resources
  $query = new \Peyote\Delete('media');
  $query->where('uid', '=', $image);
  $this->db->fetch($query);
  //remove image in db
  $query = new \Peyote\Delete('images');
  $query->where('uid', '=', $image);
  $this->db->fetch($query);
  //remove images
  $types = [
    'primary',
    'thumb'
  ];
  foreach ($types as $type) {
    $i = $data['media'][$type];
    switch ($i['storage_type']) {
      case 'local':
        unlink(ROOT_DIR . $i['file']);
        break;
      case 's3':
        $s3 = new S3($i['storage_id']);
        $s3->deleteObject($s3->get_bucket(), $i['file']);
        break;
    }
  }
  return array(
   'message' => 'Image removed',
   'uid' => $image
  );
 }

 /**
 * Cleanup Image
 *
 * Removes actual image and resources and media linked to it.  This used by the remove method and for failures in the add method.
 *
 * @param string $uid The 6-digit id of an image.
 * @param string $file Full path of the image file to be removed.
 * @param string $thumbfile Full path of the thumbnail file to be removed.
 */

 private function cleanup_image($uid, $file, $thumbfile) {
    //clean up resources
    $query = new \Peyote\Delete('resources');
    $query->where('image', '=', $uid);
    $this->db->fetch($query);
    //clean up media resources
    $query = new \Peyote\Delete('media');
    $query->where('uid', '=', $uid);
    $this->db->fetch($query);
    //remove image in db
    $query = new \Peyote\Delete('images');
    $query->where('uid', '=', $uid);
    $this->db->fetch($query);
    //remove image
    unlink($file);
    unlink($thumbfile);
 }

 /**
  * Add image from upload
  *
  * Allows uploading images.
  *
  * @api
  *
  * @param mixed $image An image uploaded as multi-part form data. Must be JPEG, PNG, or GIF format.
  * @param string $c_link An optional external link to comments for the image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
  */

 public function add($image, $c_link = NULL, $sid= NULL) {
  if (!$this->allow_upload()) throw new \Exception('Adding images is currently disabled due to site maintanence');
  $filename = md5(mt_rand());
  $path = ROOT_DIR.'/media/'.$filename;
  if (isset($image['tmp_name'])) move_uploaded_file($image['tmp_name'], $path);
  return $this->processAdd($path, $c_link, $sid);
 }

 /**
  * Process added image
  *
  * Once add() or addFromURL() have stored the image, this method completes adding it to the system
  *
  * @param string $path Location the image is stored. Must be JPEG, PNG, or GIF format.
  * @param string $c_link An optional external link to comments for the image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
  */

 private function processAdd($path, $c_link=NULL, $sid = NULL) {
  $setting = new Setting;
  $web_root = $setting->get('web_root');
  $info = getimagesize($path);
  if (!$info) {
   unlink($path);
   throw new \JohnVanOrange\Core\Exception\Invalid('Not a valid image',1100);
  }
  $filetypepart = explode('/',$info['mime']);
  $type = end($filetypepart);
  if ($type === 'gif') {
    unlink($path);
    throw new \Exception('GIF format not currently allowed to be uploaded');
  }
  $fullfilename = $path.'.'.$type;
  rename($path,$fullfilename);
  $filenamepart = explode('/',$fullfilename);
  $filename = end($filenamepart);
  $namepart = explode('.',$filename);
  $uid = $this->getUID();
  $isAnimated = $this->isAnimated($filename, TRUE);
  if ($isAnimated) {
   $animated = 1;
  }
  else {
   $animated = 0;
  }
  $query = new \Peyote\Insert('images');
  $query->columns(['uid', 'c_link', 'animated'])
        ->values([$uid, $c_link, $animated]);
  $s = $this->db->prepare($query->compile());
  $s->execute($query->getParams());//need to verify this was successful
  //media resources
  $media = new Media;
  $media->add($uid, '/media/' . $filename);
  //create thumbnail
  $thumb = $this->scale($uid);
  $thumbfilename = ROOT_DIR.'/media/thumbs/'.$filename;
  file_put_contents($thumbfilename, $thumb);
  $media->add($uid, '/media/thumbs/' . $filename, 'thumb');
  //check for duplicates
  $media_results = $media->get($uid);
  $query = new \Peyote\Select('media');
  $query->columns('uid', 'hash')
        ->where('hash', '=', $media_results['primary']['hash'])
        ->where('uid', '!=', $uid)
        ->limit(1);
  $result = $this->db->fetch($query);
  if ($result) {
   $this->cleanup_image($uid, $fullfilename, $thumbfilename);
   $dupimage = $this->get($result[0]['uid']);
   return array(
    'url' => $web_root . $dupimage['uid'],
    'uid' => $dupimage['uid'],
    'image' => $web_root . $dupimage['media']['primary']['file'],
    'thumb' => $web_root . $dupimage['media']['thumb']['file'],
    'message' => 'Duplicate image'
   );
  }
  else {
   //upload resource
   $res = new \JohnVanOrange\Core\Resource('upload', $sid);
   $res->setImage($uid)->setPublic();
   $res->add();
   return array(
    'url' => $web_root . $uid,
    'uid' => $uid,
    'image' => $web_root . 'media/' . $filename,
    'thumb' => $web_root . 'media/thumbs/' . $filename,
    'message' => 'Image added'
   );
  }
 }

 /**
  * Add image from URL
  *
  * Allows adding images to site from remote URL's.
  *
  * @api
  *
  * @param string $url Full URL to an image to be added to the site. Must be JPEG, PNG, or GIF format.
  * @param string $c_link An optional external link to comments for the image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
  */

 public function addFromURL($url, $c_link=NULL, $sid = NULL) {
  if (!$this->allow_upload()) throw new \Exception('Adding images is currently disabled due to site maintanence');
  $image = $this->remoteFetch($url);
  $filename = md5(mt_rand().$url);
  $newpath = ROOT_DIR.'/media/'.$filename;
  file_put_contents($newpath,$image);
  return $this->processAdd($newpath, $c_link, $sid);
 }

 /**
  * Report image
  *
  * Allows reporting of problematic images so they may undergo review.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param int $type Number value representing the reason type which can be found in report/all
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie.
  */

 public function report($image, $type, $sid = NULL) {
  if (!isset($image)) throw new \Exception('No image specified');
  if (!isset($type)) throw new \Exception('No report type specified');
  //Add report
  $res = new \JohnVanOrange\Core\Resource('report', $sid);
  $res->setImage($image)->setValue($type);
  $res->add();
  //Hide image
  $query = new \Peyote\Update('images');
  $query->set(['display' => 0])
        ->where('uid', '=', $image);
  $this->db->fetch($query);
  $setting = new Setting;
  $message = new Mail();
  $data = [
   'image' => $image
  ];
  $message->sendAdminMessage('New Reported Image for '. $setting->get('site_name'), 'reported_image', $data);
  return array(
   'message' => 'Image Reported',
   'uid' => $image
  );
 }

 /**
  * Random reported image
  *
  * Retrieves a random image that has been reported by users. Must be logged in as an admin to access this method.
  *
  * @api
  *
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function reported($sid=NULL) {
  $current = $this->user->current($sid);
  if ($current['type'] < 2) throw new \JohnVanOrange\Core\Exception\NotAllowed('Must be an admin to access method', 401);
  $query = new \Peyote\Select('resources');
  $query->where('type', '=', 'report')
        ->orderBy('RAND()')
        ->limit(1);
  $report_result = $this->db->fetch($query);
  $image_result = $this->get($report_result[0]['image'], $sid);
  if (!$image_result) throw new \JohnVanOrange\Core\Exception\NotFound('No image result', 404);
  return $image_result;
 }

 /**
  * Random unapproved image
  *
  * Retrieves a random unapproved image. Must be logged in as admin to access this method.
  *
  * @api
  *
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function unapproved($sid=NULL) {
  $current = $this->user->current($sid);
  if ($current['type'] < 2) throw new \JohnVanOrange\Core\Exception\NotAllowed('Must be an admin to access method', 401);
  $query = new \Peyote\Select('images');
  $query->columns('uid')
        ->where('approved', '=', 0)
        ->orderBy('RAND()')
        ->limit(1);
  $image = $this->db->fetch($query);
  $image_result = $this->get($image[0]['uid']);
  if (!$image_result) throw new \JohnVanOrange\Core\Exception\NotFound('No image result', 404);
  return $image_result;
 }

 /**
  * Random image
  *
  * Retrieves a random image.
  *
  * @api
  *
  * @param array $filter Array of options to filter image results.  Currently available options include format, animated, nsfw, approved and uploader.
  * @param int $count Number of results to return.
  */

 public function random($filter = NULL, $count = 1) {
  if (!is_object($filter)) $filter = json_decode($filter, TRUE);
  $query = new ImageFilter\Random($filter, $count);
  $results = $this->db->fetch($query);
  if (!$results) throw new \Exception('No image results');
  foreach ($results as $result) {
   $image[] = $this->get($result['uid']);
  }
  if ($count == 1) {
   $image = $image[0];
   $image['response'] = $image['uid']; //backwards compatibility
  }
  return $image;
 }

  /**
  * Recently added images
  *
  * Displays a list of images recently added
  *
  * @api
  *
  * @param int $count Number of results to display
  */

 public function recent($count = 25) {
  $query = new \Peyote\Select('resources');
  $query->where('type', '=', 'upload')
        ->orderBy('created', 'DESC')
        ->limit($count);
  $results = $this->db->fetch($query);
  $image = new Image();
  foreach ($results as $result) {
   try {
    $return[] = $image->get_slim($result['image']);
   }
   catch(\Exception $e) {
    if ($e->getCode() != 403) {
     throw new \Exception($e);
    }
   }
  }
  return $return;
 }

  /**
  * Recently liked images
  *
  * Displays a list of images recently liked
  *
  * @api
  *
  * @param int $count Number of results to display
  */

 public function recentLikes($count = 25) {
  $query = new \Peyote\Select('resources');
  $query->columns('image')
        ->where('type', '=', 'like')
        ->orderBy('created', 'DESC')
        ->groupBy('image')
        ->limit($count);
  $results = $this->db->fetch($query);
  $image = new Image();
  foreach ($results as $result) {
   try {
    $return[] = $image->get_slim($result['image']);
   }
   catch(\Exception $e) {
    if ($e->getCode() != 403) {
     throw new \Exception($e);
    }
   }
  }
  return $return;
 }

 /**
  * Get image with minimal data
  *
  * Retrieve information about an image. Only provides basic image and media data. Designed for other methods that require minimal data about an image.
  *
  * @param string $image The 6-digit id of an image.
  */
 public function get_slim($image) {
  $query = new \Peyote\Select('images');
  $query->where('uid', '=', $image)
        ->limit(1);
  $result = $this->db->fetch($query);
  if (!$result) throw new \JohnVanOrange\Core\Exception\NotFound('Image not found', 404);
  $result = $result[0];
  $media = new Media;
  $result['media'] = $media->get($image);
  if (!$result['display']) throw new \JohnVanOrange\Core\Exception\Forbidden('Image removed', 403); //need to be modified to allow admins if intergrated into image/get
  $setting = new Setting;
  $result['page_url'] = $setting->get('web_root') . $result['uid'];
  return $result;
 }

 /**
  * Get image
  *
  * Retrieve information about an image.
  *
  * @api
  *
  * @param string $image The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  * @param string $session Session ID for non-logged in users. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  * @param bool $brazzify Should Brazzzify.me url be returned
  */

 public function get($image, $sid = NULL, $session = NULL, $brazzify = FALSE) {
  $setting = new Setting;
  $current = $this->user->current($sid);
  //Get image data
  $query = new \Peyote\Select('images');
  $query->where('uid', '=', $image)
        ->limit(1);
  $result = $this->db->fetch($query);
  //See if there was a result
  if (!$result) { //check for merged image
   $query = new \Peyote\Select('resources');
   $query->columns('image')
         ->where('value', '=', $image)
         ->where('type', '=', 'merge');
   $result = $this->db->fetch($query);
   if ($result) {
    return ['merged_to' => $result[0]['image']];
   } else {
    throw new \JohnVanOrange\Core\Exception\NotFound('Image not found', 404);
   }
  }
  $result = $result[0];
  //Verify image isn't supposed to be hidden
  if (!$result['display'] AND !$this->user->isAdmin($sid)) throw new \JohnVanOrange\Core\Exception\Forbidden('Image removed', 403);
  //Get media data
  $media = new Media;
  $result['media'] = $media->get($image);
  //Backwards compatibilty
  $result['hash'] = $result['media']['primary']['hash'];
  $result['type'] = $result['media']['primary']['format'];
  $result['width'] = $result['media']['primary']['width'];
  $result['height'] = $result['media']['primary']['height'];
  $siteURL = $this->siteURL();
  $result['image_url'] = $siteURL['scheme'] .'://' . $siteURL['host']. $result['media']['primary']['file'];
  if (isset($result['media']['thumb']['file'])) $result['thumb_url'] = $siteURL['scheme'] .'://' . $siteURL['host']. $result['media']['thumb']['file'];
  //this can probably stay after the BC is removed
  $result['page_url'] = $setting->get('web_root') . $result['uid'];
  //Get tags
  $tag = new Tag;
  $tag_result = $tag->get($result['uid']);
  if (isset($tag_result)) $result['tags'] = $tag_result;
  //Get uploader
  $query = new \Peyote\Select('resources');
  $query->where('image', '=', $result['uid'])
        ->where('type', '=', 'upload');
  $upload = $this->db->fetch($query);
  if (isset($upload[0]['user_id'])) {
   $result['uploader'] = $this->user->get($upload[0]['user_id']);
  }
  if (isset($upload[0]['created'])) {
   $result['added'] = $upload[0]['created'];
  }
  //Get resources
  if ($current) { //this could probably be simplified so only the initial query is in the if block
   $query = new \Peyote\Select('resources');
   $query->where('image' ,'=', $result['uid'])
         ->where('user_id', '=', $current['id']);
   $resources = $this->db->fetch($query);
   $data = NULL;
   foreach ($resources as $r) {
    $data[$r['type']] = $r;
   }
   if ($data) $result['data'] = $data;
   if (isset($data['save'])) $result['saved'] = 1;
 } elseif ($session) {
   $query = new \Peyote\Select('resources');
   $query->where('image' ,'=', $result['uid'])
         ->where('unauth_user', '=', $session);
   $resources = $this->db->fetch($query);
   $data = NULL;
   foreach ($resources as $r) {
    $data[$r['type']] = $r;
   }
   if ($data) $result['data'] = $data;
   if (isset($data['save'])) $result['saved'] = 1;
  }
  //Page title
  $result['page_title'] = $setting->get('site_name');
  if (isset($result['tags'][0])) {
   $title_text = ' - ';
   foreach ($result['tags'] as $tag) {
    $title_text .= $tag['name'] . ', ';
   }
   $result['page_title'] .= rtrim($title_text, ', ');
  }
  if ($this->user->isAdmin($sid)) {
   //Get report data
   $query = new \Peyote\Select('resources');
   $query->where('type', '=', 'report')
         ->where('image', '=', $result['uid'])
         ->limit(1);
   $report_result = $this->db->fetch($query);
   if ($report_result) {
    $report = new Report;
    $report_type = $report->get($report_result[0]['value']);
    $report_result[0]['value'] = $report_type[0]['value'];
    $result['report'] = $report_result[0];
   }
  }
  //Brazzify.me
  if ($brazzify) {
   $result['brazzify_url'] = json_decode($this->remoteFetch('http://i.brazzify.me/api.php?logo=brazzers&remote_url='.$result['media']['primary']['url']),1)['url'];
  }
  $result['favs'] = $this->image_stat($result['uid'], 'save');
  $result['likes'] = $this->image_stat($result['uid'], 'like');
  return $result;
 }

 private function image_stat($image, $stat) {
  $query = new \Peyote\Select('resources');
  $query->columns('COUNT(*)')
        ->where('type', '=', $stat)
        ->where('image', '=', $image);
  return $this->db->fetch($query)[0]['COUNT(*)'];
 }

 private function getUID($length = 6) {
  do {
   $uid = $this->generateUID($length);
   $query = new \Peyote\Select('images');
   $query->columns('uid')
         ->where('uid', '=', $uid)
         ->limit(1);
   $not_unique = $this->db->fetch($query);
  } while (count($not_unique));
  return $uid;
 }

 /**
  * Get stats
  *
  * Returns the total number of images, reported images and approved images.
  *
  * @api
  */

 public function stats() {
  $result = [];
  $query = new \Peyote\Select('images');
  $query->columns('COUNT(*)');
  $result['images'] = $this->db->fetch($query)[0]['COUNT(*)'];
  $query = new \Peyote\Select('resources');
  $query->columns('COUNT(*)')
        ->where('type', '=', 'report');
  $result['reports'] = $this->db->fetch($query)[0]['COUNT(*)'];
  $query = new \Peyote\Select('images');
  $query->columns('COUNT(*)')
        ->where('approved', '=', 1);
  $result['approved'] = $this->db->fetch($query)[0]['COUNT(*)'];
  return $result;
 }

 public function scale($image, $width = 240, $height = 160) {
  $imagedata = $this->get($image);
  $image = new \Imagick(ROOT_DIR . $imagedata['media']['primary']['file']);

  $image = $image->coalesceImages();

  foreach ($image as $frame) {
   $frame->thumbnailImage($width,$height,TRUE);
   $frame->setImagePage($width, $height, 0, 0);
  }
  //header('Content-type: '.$image->getImageMimeType());
  return $image->getImagesBlob();
 }

 /**
  * Merge images
  *
  * Merge two images into one, merging any assoicated resources. Must be logged in as admin to use this method.
  *
  * @api
  *
  * @param string $image1 The 6-digit id of an image.
  * @param string $image2 The 6-digit id of an image.
  * @param string $sid Session ID that is provided when logged in. This is also set as a cookie. If sid cookie headers are sent, this value is not required.
  */

 public function merge($image1, $image2, $sid=NULL) {
  $image = new Image();
  if (!$this->user->isAdmin($sid)) throw new \JohnVanOrange\Core\Exception\NotAllowed('Must be an admin to access method', 401);
  $image1 = $image->get($image1, $sid);
  $image2 = $image->get($image2, $sid);
  $primary = $image1; $sec = $image2;
  //check total image area, assume the largest is the one to keep
  if (($image2['height']*$image2['width']) > ($image2['height']*$image2['width'])) {
   $primary = $image2;
   $sec = $image1;
  }
  $res = new Resource;
  $res->merge($primary['uid'], $sec['uid']);
  $res = new \JohnVanOrange\Core\Resource('merge', $sid);
  $res->setImage($primary['uid'])->setValue($sec['uid'])->setPublic();
  $res->add();
  $this->remove($sec['uid']);
  return [
   'message' => 'Images merged',
   'uid' => $primary['uid'],
   'url' => $primary['page_url'],
   'thumb' => $primary['thumb_url']
  ];
 }

  /**
  * Is Animated
  *
  * Checks to see if image is animated
  *
  * @param string $image The 6-digit id of an image or image filename.
  * @param bool $search_by_filename If true, use filename instead of UID.
  */

 public function isAnimated($image, $search_by_filename = FALSE) {
  if (!$search_by_filename) {
   $imagedata = $this->get($image);
   $image = $imagedata['media']['primary']['file'];
  }
  $image = new Imagick(ROOT_DIR.'/media/'.$image);

  $animated = FALSE;
  $framecount = 0;
  foreach ($image as $frame) {
   $framecount++;
  }

  if ($framecount > 1) $animated = TRUE;
  return $animated;
 }

 private function allow_upload() {
  $setting = new Setting;
  $disable_upload = $setting->get('disable_upload');
  if ($disable_upload) {
   return FALSE;
  }
  return TRUE;
 }

}
