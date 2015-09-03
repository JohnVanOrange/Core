<?php
namespace JohnVanOrange\Core;

class Resource {
	private $type;
	private $image = NULL;
	private $tag = NULL;
	private $sid = NULL;
	private $value = NULL;
	private $public = FALSE;
	private $user = NULL;
	private $unauth_user = NULL;

	public function __construct( $type = NULL, $sid = NULL ) {
		if ($type) $this->setType($type);
		if ($sid) $this->setSID($sid);
	}

	public function setType( $type ) {
		$this->type = $type;
		return $this;
	}

	public function setImage( $image ) {
		$this->image = $image;
		return $this;
	}

	public function setTag( $tag ) {
		$this->tag = $tag;
		return $this;
	}

	public function setSID( $sid ) {
		$this->sid = $sid;
		return $this;
	}

	public function setValue( $value ) {
		$this->value = $value;
		return $this;
	}

	public function setPublic( $public = TRUE ) {
		$this->public = $public;
		return $this;
	}

	public function add() {
		$this->loadUserData();

		$req = new \JohnVanOrange\Core\Request;
		$data = [
			'ip' => $req->ip(),
			'image' => $this->image,
			'user_id' => $this->user,
			'value' => $this->value,
			'type' => $this->type,
			'tag_id' => $this->tag,
			'unauth_user' => $this->unauth_user
		];
		if ($this->public) $data['public'] = 1;
		$query = new \Peyote\Insert('resources');
		$query->columns(array_keys($data))
					->values(array_values($data));
		$db = new \JohnVanOrange\API\DB('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);//TODO: This should be refactored
		return $db->fetch($query);
	}

	private function loadUserData() {
		if ($this->sid) {
			$this->loadAuthDetails($this->sid);
		}
		else {
			$user = new \JohnVanOrange\API\User;
			$this->unauth_user = $user->unAuthUser();
		}
	}

	private function loadAuthDetails($sid) {
		$user = new \JohnVanOrange\API\User;
		$current = $user->current($sid);
		if (isset($current['id'])) {
			$this->user = $current['id'];
		}
	}
}
