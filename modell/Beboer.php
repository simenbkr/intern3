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
	private $studieId;
	private $skoleId;
	private $klassetrinn;
	private $alkoholdepositum;
	private $rolleId;
	private $epost;
	private $romhistorikk;

	// Latskap
	private $studie;
	private $skole;
	private $romId;
	private $rom;
	private $romhistorikkObjekt;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM beboer WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medBrukerId($brukerId) {
		$st = DB::getDB()->prepare('SELECT * FROM beboer WHERE bruker_id=:brukerId;');
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
		$instance->fodselsdato = $rad['fodselsdato'];
		$instance->adresse = $rad['adresse'];
		$instance->postnummer = $rad['postnummer'];
		$instance->telefon = $rad['telefon'];
		$instance->studieId = $rad['studie_id'];
		$instance->skoleId = $rad['skole_id'];
		$instance->klassetrinn = $rad['klassetrinn'];
		$instance->alkoholdepositum = $rad['alkoholdepositum'];
		$instance->rolleId = $rad['rolle_id'];
		$instance->epost = $rad['epost'];
		$instance->romhistorikk = $rad['romhistorikk'];
		$instance->studie = null;
		$instance->skole = null;
		$instance->romId = null;
		$instance->rom = null;
		$instance->romhistorikkObjekt = null;
		return $instance;
	}

	public function erBeboer() {
		return true;
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

	public function getKlassetrinn() {
		return $this->klassetrinn;
	}

	public function getStudie() {
		if ($this->studie == null) {
			$this->studie = Studie::medId($this->studieId);
		}
		return $this->studie;
	}

	public function getSkole() {
		if ($this->skole == null) {
			$this->skole = Skole::medId($this->skoleId);
		}
		return $this->skole;
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