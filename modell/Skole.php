<?php

namespace intern3\kjerne;

class Skole {

	private $id;
	private $brukernavn;
	private $passfrase;

	public static function byId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM skole WHERE id=:id;');
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
		$instance->brukernavn = $row['brukernavn'];
		$instance->passfrase = $row['passfrase'];
		return $instance;
	}

	public function giId() {
		return $this->id;
	}

	public function giBrukernavn() {
		return $this->brukernavn;
	}

}

?>