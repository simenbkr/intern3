<?php

namespace intern3;

class Bruker {

	private $id;
	private $passord;

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
		$instance->passord = $row['passord'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

}

?>