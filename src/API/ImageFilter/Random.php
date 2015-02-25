<?php
namespace JohnVanorange\API\ImageFilter;

class Random extends Base {

 protected $count;

 public function __construct($options = NULL, $count = 1) {
  $this->count = $count;
  parent::__construct($options);
 }

 protected function sort() {
  $this->orderBy('RAND()');
 }

 protected function limit_process() {
  $this->limit($this->count);
 }

}
