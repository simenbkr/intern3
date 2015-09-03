<?php

namespace intern3;

class Studie {

	private $id;
	private $skoleId;
	private $navn;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM studie WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	private static function init(\PDOStatement $st) {
		$row = $st->fetch();
		if ($row == null) {
			return null;
		}
		$instance = new self();
		$instance->id = $row['id'];
		$instance->skoleId = $row['skole_id'];
		$instance->navn = $row['navn'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getSkoleId() {
		return $this->skoleId;
	}

	public function getNavn() {
		return $this->navn;
	}

}

?>