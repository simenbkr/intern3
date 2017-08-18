<?php

namespace intern3;

class Bruker {

	private $id;
	private $passord;
	private $salt;
	private $resett_tid;
	private $glemt_token;

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
		$instance->salt = $rad['salt'];
		$instance->resett_tid = $rad['dato'];
		$instance->glemt_token = $rad['glemt_token'];
		$instance->person = null;
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getSalt()
    {
        return $this->salt;
    }

	public function passordErGyldig($passord) {
		//MÅ bruke === og ikke == da == er en shitty operator i denne situasjonen. TODO test at funker.
		return $passord === $this->passord;
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

	public function antallStraffevakter() {
		return Vakt::antallStraffeVakter($this->id);
	}

	public function antallVakterSkalSitte() {
		$beboer = $this->getPerson();
		if (!$beboer->erBeboer()) {
			return 0;
		}
		$rolle = $beboer->getRolle();
		$antall = date('m') > 6 ? $rolle->getVakterH() : $rolle->getVakterV();
		$antall += $this->antallStraffevakter();
		return $antall;
	}

	public function antallVakterHarSittet() {
		return Vakt::antallHarSittetMedBrukerId($this->id);
	}

	public function antallVakterErOppsatt() {
		return Vakt::antallErOppsattMedBrukerId($this->id);
	}

	public function antallForstevakter() {
		return Vakt::antallForsteMedBrukerId($this->id);
	}

	public function antallVakterHarIgjen() {
		return Vakt::antallHarIgjenMedBrukerId($this->id, $this->antallVakterSkalSitte());
	}

	public function antallVakterIkkeOppsatt() {
		return Vakt::antallIkkeOppsattMedBrukerId($this->id, $this->antallVakterSkalSitte());
	}

	public function antallVakterIkkeBekreftet() {
		return Vakt::antallIkkeBekreftetMedBrukerId($this->id);
	}

	public function getRegisekunderMedSemester($unix = false) {
		if ($unix === false) {
			global $_SERVER;
			$unix = $_SERVER['REQUEST_TIME'];
		}
		$sum = 0;
		foreach (ArbeidListe::medBrukerIdSemester($this->id , $unix) as $arbeid) {
			if (!$arbeid->getGodkjent()) {
				continue;
			}
			$sum += $arbeid->getSekunderBrukt();
		}
		return $sum;
	}

	public function endreSalt($salt){
		$st = DB::getDB()->prepare('UPDATE bruker SET salt=:salt WHERE id=:id');
		$st->bindParam(':salt', $salt);
		$st->bindParam(':id', $this->id);
		$st->execute();
		$this->salt = $salt;
	}

	public function endrePassord($passord){
		$st = DB::getDB()->prepare('UPDATE bruker SET passord=:password WHERE id=:id');
		$st->bindParam(':password', $passord);
		$st->bindParam(':id', $this->id);
		$st->execute();
	}

	public static function byGlemtToken($token){
		$st = DB::getDB()->prepare('SELECT * FROM bruker WHERE glemt_token=:token');
		$st->bindParam(':token', $token);
		$st->execute();
		return self::init($st);
	}

	public function getResettTid(){
		return $this->resett_tid;
	}

	public function getGlemtToken(){
		return $this->glemt_token;
	}

}

?>
