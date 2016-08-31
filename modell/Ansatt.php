<?php

namespace intern3;

class Ansatt implements Person {

	private $id;
	private $brukerId;
	private $fornavn;
	private $mellomnavn;
	private $etternavn;
	private $epost;

	// Latskap
  private $bruker;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM ansatt WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medBrukerId($brukerId) {
		$st = DB::getDB()->prepare('SELECT * FROM ansatt WHERE bruker_id=:brukerId;');
		$st->bindParam(':brukerId', $brukerId);
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
		$instance->brukerId = $rad['bruker_id'];
		$instance->fornavn = $rad['fornavn'];
		$instance->mellomnavn = $rad['mellomnavn'];
		$instance->etternavn = $rad['etternavn'];
		$instance->epost = $rad['epost'];
    $instance->bruker = null;
		return $instance;
	}

	public function erBeboer() {
		return false;
	}

	public function getId() {
		return $this->id;
	}

	public function getBrukerId() {
		return $this->brukerId;
	}

	public function getFornavn() {
		return $this->fornavn;
	}

	public function getMellomnavn() {
		return $this->mellomnavn;
	}

	public function getEtternavn() {
		return $this->etternavn;
	}

	public function getFulltNavn() {
		return trim(preg_replace('/[\s]{2,}/', ' ', $this->fornavn . ' ' . $this->mellomnavn . ' ' . $this->etternavn));
	}

	public function getEpost() {
		return $this->epost;
	}
}

?>
