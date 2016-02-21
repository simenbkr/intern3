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
	private $rolle;
	private $vervListe;
	private $utvalgVervListe;
  private $bruker;

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
    $instance->bruker = null;
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

	public function getFodselsdato() {
		return $this->fodselsdato;
	}

	public function getFodselsar() {
		return substr($this->fodselsdato, 0, 4);
	}

	public function getAlderIAr() {
		return date('Y') - substr($this->fodselsdato, 0, 4);
	}

	public function getAlder() {
		return $this->getAlderIAr() - (($_SERVER['REQUEST_TIME'] - mktime(0, 0, 0, substr($this->fodselsdato, 5, 2), substr($this->fodselsdato, 8, 2))) < 0 ? 1 : 0);
	}

	public function getAdresse() {
		return $this->adresse;
	}

	public function getPostnummer() {
		return $this->postnummer;
	}

	public function getTelefon() {
		if (strlen($this->telefon) > 8) {
			return substr($this->telefon, 0, strlen($this->telefon) - 8) . ' ' . substr($this->telefon, -8);
		}
		return $this->telefon;
	}

	public function getKlassetrinn() {
		return $this->klassetrinn;
	}

	public function getStudieId() {
		return $this->studieId;
	}

	public function getStudie() {
		if ($this->studie == null) {
			$this->studie = Studie::medId($this->studieId);
		}
		return $this->studie;
	}

	public function getSkoleId() {
		return $this->skoleId;
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

	public function getRolleId() {
		return $this->rolleId;
	}

	public function getRolle() {
		if ($this->rolle == null) {
			$this->rolle = Rolle::medId($this->rolleId);
		}
		return $this->rolle;
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

	public function harVakt() {
		return $this->getRolle()->getRegitimer() < 48;
	}

	public function getVervListe() {
		if ($this->vervListe == null) {
			$this->vervListe = VervListe::medBeboerId($this->id);
		}
		return $this->vervListe;
	}

	public function harUtvalgVerv() {
		//return count($this->getUtvalgVervListe()) > 0;
    return true;
	}

	public function getUtvalgVervListe() {
		if ($this->utvalgVervListe == null) {
			$this->utvalgVervListe = VervListe::utvalgMedBeboerId($this->id);
		}
		return $this->utvalgVervListe;
	}

  // public function getBruker() {
  //   if ($this->bruker == null && $this->brukerId != 0) {
  //     $this->bruker = Bruker::medId($this->id);
  //   }
  //   return $this->bruker;
  // }

  public function antallStraffevakter() {
    return Straffevakt::antallMedBrukerId($this->getBrukerId());
  }

  public function antallVakterSkalSitte() {
    // Dette må fikses
    $rolleId = $this->getRolleId();
    if ($rolleId == 1) {
      $antall = 5;
    }
    else if ($rolleId == 2) {
      $antall = 8;
    }
    else if ($rolleId == 3) {
      $antall = 0;
    }
    if (date("m") < '07' && $antall > 0) {
      $antall += 1;
    }
    $antall += $this->antallStraffevakter();
    return $antall;
  }

  public function antallVakterHarSittet() {
    return Vakt::antallHarSittetMedBrukerId($this->getBrukerId());
  }

  public function antallVakterErOppsatt() {
    return Vakt::antallErOppsattMedBrukerId($this->getBrukerId());
  }

  public function antallVakterHarIgjen() {
    return Vakt::antallHarIgjenMedBrukerId($this->getBrukerId(), $this->antallVakterSkalSitte());
  }

  public function antallVakterIkkeOppsatt() {
    return Vakt::antallIkkeOppsattMedBrukerId($this->getBrukerId(), $this->antallVakterSkalSitte());
  }

  public function antallVakterIkkeBekreftet() {
    return Vakt::antallIkkeBekreftetMedBrukerId($this->getBrukerId());
  }
}

?>
