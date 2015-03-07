<?php

namespace intern3\kjerne;

class Studie {

	private $id;
	private $skoleId;
	private $navn;

	public static function byId($id) {
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

	public function giId() {
		return $this->id;
	}

	public function giSkoleId() {
		return $this->skoleId;
	}

	public function giNnavn() {
		return $this->navn;
	}

}

?>