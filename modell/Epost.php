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

	public function getAdresse(){
	    return $this->adresse;
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

	public function getMottakere(){
	    return $this->mottakere;
    }

    public function getMessage(){
        return $this->beskjed;
    }

	public function send($tittel = null) {
		// TODO: Vi har $beskjed og lista kalt $mottakere, bare å døtte dette et sted
        /*NOTAT: Dette må KUN sendes fra servere hvor det er konfigurert slik at PHP
        sender gjennom Postfix, og som har korrekt SPF-record, riktig oppsatt DKIM, DMARC, Reverse PTR
        og (helst) TLS mellom SMTP-servere.
        Per 2016-10-04 er DOBBEL satt opp slik.
        */
        $headers = "From: Internsida Singsaker <no-reply@mail.singsaker.no>" . "\r\n";
        $headers .= "Reply-To: no-reply@mail.singsaker.no\r\n";
        $headers .= "Return-Path: no-reply@mail.singsaker.no\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Date: " . date('H:i:s, d M Y') . " UTC +0000\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        $tittelen = (isset($tittel) && $tittel != null) ? $tittel : "[INTERN] Du har fått en melding fra intern.singsaker.no";
        foreach($this->getMottakere() as $mottaker){
            mail($mottaker->getAdresse(),$tittelen,$this->getMessage(),$headers);
        }


	}
}

?>