<?php

namespace intern3;

class Verv {

	private $id;
	private $navn;
	private $utvalg;
	private $epost;

	// Latskap
	private $apmend = null;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM verv WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medNavn($navn) {
		$st = DB::getDB()->prepare('SELECT * FROM verv WHERE navn=:navn;');
		$st->bindParam(':navn', $navn);
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
		$instance->navn = $rad['navn'];
		$instance->utvalg = $rad['utvalg'];
		$instance->epost = $rad['epost'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getNavn() {
		return $this->navn;
	}

	public function erUtvalg() {
		return $this->utvalg;
	}

	public function getEpost() {
		return $this->epost;
	}

	public function getApmend() {
		if ($this->apmend == null) {
			$this->apmend = BeboerListe::medVervId($this->id);
		}
		return $this->apmend;
	}

}

?>