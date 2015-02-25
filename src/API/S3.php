<?php
namespace JohnVanorange\API;

use \S3 as AmazonS3;

class S3 extends AmazonS3 {

  private $db, $data;

  public function __construct() {
    $this->db = new DB('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
    $query = new \Peyote\Select('storage');
    $query->where('type', '=', 's3')
          ->limit(1);
    $this->data = $this->db->fetch($query)[0];
    parent::__construct($this->data['access_key'], $this->data['secret_key'], false, $this->data['endpoint']);
  }

  public function get_bucket() {
    return $this->data['bucket'];
  }

}
