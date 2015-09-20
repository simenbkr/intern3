<?php

namespace intern3;

class Bruker {

	private $id;
	private $passord;

	// Latskap
	private $person;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM bruker WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medEpost($epost) {
		if (!$epost) {
			return null;
		}
		$st = DB::getDB()->prepare('(SELECT br.* FROM beboer AS be,bruker AS br WHERE be.bruker_id=br.id AND be.epost=:epost) UNION (SELECT br.* FROM bruker AS br,ansatt AS an WHERE an.bruker_id=br.id AND an.epost=:epost);');
		$st->bindParam(':epost', $epost);
		$st->execute();
		return self::init($st);
	}
	private static function init(\PDOStatement $st) {
		$rad = $st->fetch();
		if ($rad == null) {
			return null;
		}
		$instance = new self();
		$instance->id = $rad['id'];
		$instance->passord = $rad['passord'];
		$instance->person = null;
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function passordErGyldig($passord) {
		return $passord == $this->passord;
	}

	public function getPerson() {
		if ($this->person <> null) {
			return $this->person;
		}
		$this->person = Beboer::medBrukerId($this->id);
		if ($this->person == null) {
			$this->person = Ansatt::medBrukerId($this->id);
		}
		return $this->person;
	}

}

?>