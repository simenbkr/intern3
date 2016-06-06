<?php

// Sånn her må man gjøre for å få nøsta klasser:

namespace intern3\Epost;

class Adressat {
	private $navn;
	private $adresse;

	public function __construct($navn, $adresse) {
		$this->navn = $navn;
		$this->adresse = $adresse;
	}

	public function __toString() {
		return $this->navn . '<' . $this->adresse . '>';
	}
}

namespace intern3;

class Epost {

	private $mottakere;
	private $beskjed;

	public function __construct($beskjed) {
		$this->mottakere = array();
		$this->beskjed = $beskjed;
	}

	public function addBrukerId($brukerId) {
		$bruker = Bruker::medId($brukerId);
		if ($bruker == null) {
			return false;
		}
		$person = $bruker->getPerson();
		if ($person == null) {
			return false;
		}
		$epost = $person->getEpost();
		if ($epost == null) {
			return false;
		}
		$this->mottakere[] = new Epost\Adressat($person->getFulltNavn, $epost);
		return true;
	}

	public function addVervId($vervId) {
		$verv = Verv::medId($vervId);
		if ($verv == null) {
			return false;
		}
		do {
			$epost = $verv->getEpost();
			if ($epost == null) {
				break;
			}
			$this->mottakere[] = new Epost\Adressat($verv->getNavn(), $epost);
			return true;
		} while(false);
		$treff = false;
		foreach ($verv->getApmend() as $beboer) {
			$epost = $beboer->getEpost();
			if ($epost <> null) {
				$treff = true;
				$this->mottakere[] = new Epost\Adressat($beboer->getFulltNavn(), $epost);
			}
		}
		return $treff;
	}

	public function send() {
		// TODO: Vi har $beskjed og lista kalt $mottakere, bare å døtte dette et sted
	}
}

?>