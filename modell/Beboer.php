<?php

namespace intern3;

class Beboer {

	private $id;
	private $brukerId;
	private $fornavn;
	private $mellomnavn;
	private $etternavn;
	private $fodselsdato;
	private $adresse;
	private $postnummer;
	private $telefon;
	private $studie_id;
	private $klassetrinn;
	private $alkoholdepositum;
	private $rolle_id;
	private $epost;
	private $romhistorikk;

	// Latskap
	private $romId;
	private $rom;
	private $romhistorikkObjekt;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM beboer WHERE id=:id;');
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
		$instance->brukerId = $row['bruker_id'];
		$instance->fornavn = $row['fornavn'];
		$instance->mellomnavn = $row['mellomnavn'];
		$instance->etternavn = $row['etternavn'];
		$instance->fodselsdato = $row['fodselsdato'];
		$instance->adresse = $row['adresse'];
		$instance->postnummer = $row['postnummer'];
		$instance->telefon = $row['telefon'];
		$instance->studie_id = $row['studie_id'];
		$instance->klassetrinn = $row['klassetrinn'];
		$instance->alkoholdepositum = $row['alkoholdepositum'];
		$instance->rolle_id = $row['rolle_id'];
		$instance->epost = $row['epost'];
		$instance->romhistorikk = $row['romhistorikk'];
		$instance->romId = null;
		$instance->rom = null;
		return $instance;
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

	public function getTelefon() {
		return $this->telefon;
	}

	public function getEpost() {
		return $this->epost;
	}

	public function getRomId() {
		if ($this->romId == null) {
			$this->romId = $this->getRomhistorikk()->getAktivRomId();
		}
		return $this->romId;
	}

	public function getRom() {
		if ($this->rom == null) {
			$this->rom = Rom::medId($this->getRomId());
		}
		return $this->rom;
	}

	public function getRomhistorikk() {
		if ($this->romhistorikkObjekt == null) {
			$this->romhistorikkObjekt = Romhistorikk::fraJson($this->romhistorikk);
		}
		return $this->romhistorikkObjekt;
	}

}

?>