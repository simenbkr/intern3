<?php

/* Obs: krysseliste er json fra db, mens KryssListe er en liste av Kryss. */

// Sånn her må man gjøre for å få nøsta klasser:

namespace intern3\Krysseliste;

class Kryss {
	public $tid;
	public $fakturert;
	public $antall;

	public function __construct($tid, $antall, $fakturert) {
		$this->tid = $tid;
		$this->fakturert = $fakturert;
		$this->antall = $antall;
	}
}

namespace intern3;

class Krysseliste {

	private $id;
	private $beboerId;
	private $drikkeId;
	private $krysseliste;

	// Latskap
	private $kryssListe = null;
	private $beboer = null;
	private $drikke = null;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM krysseliste WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medBeboerId($beboerId) {
		// Burde egentlig vært i KrysselisteListe som ennå ikke finnes.
		$st = DB::getDB()->prepare('SELECT id FROM krysseliste WHERE beboer_id=:beboerId;');
		$st->bindParam(':beboerId', $beboerId);
		$st->execute();
		$krysselisteListe = array();
		while ($rad = $st->fetch()) {
			$krysselisteListe[] = self::medId($rad['id']);
		}
		return $krysselisteListe;
	}
	public static function medBeboerDrikkeId($beboerId, $drikkeId) {
		$st = DB::getDB()->prepare('SELECT * FROM krysseliste WHERE beboer_id=:beboerId AND drikke_id=:drikkeId;');
		$st->bindParam(':beboerId', $beboerId);
		$st->bindParam(':drikkeId', $drikkeId);
		$st->execute();
		$instance = self::init($st);
		if ($instance == null) {
			$instance = new self();
			$instance->id = 0;
			$instance->beboerId = $beboerId;
			$instance->drikkeId = $drikkeId;
			$instance->krysseliste = '[]';
		}
		return $instance;
	}
	private static function init(\PDOStatement $st) {
		$rad = $st->fetch();
		$instance = new self();
		if ($rad == null) {
			return null;
		}
		$instance->id = $rad['id'];
		$instance->beboerId = $rad['beboer_id'];
		$instance->drikkeId = $rad['drikke_id'];
		$instance->krysseliste = $rad['krysseliste'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getBeboerId() {
		return $this->beboerId;
	}

	public function getBeboer() {
		if ($this->beboer == null) {
			$this->beboer = Beboer::medId($this->beboerId);
		}
		return $this->beboer;
	}

	public function getDrikkeId() {
		return $this->drikkeId;
	}

	public function getDrikke() {
		if ($this->drikke == null) {
			$this->drikke = Drikke::medId($this->drikkeId);
		}
		return $this->drikke;
	}

	public function getKryssListe() {
		if ($this->kryssListe == null) {
			$this->kryssListe = array();
			$jsonArray = json_decode($this->krysseliste);
			if (!is_array($jsonArray)) {
				$jsonArray = array();
			}
			foreach ($jsonArray as $strukt) {
				$this->kryssListe[] = new Krysseliste\Kryss(
					$strukt->tid,
					$strukt->fakturert,
					$strukt->antall
				);
			}
		}
		return $this->kryssListe;
	}

	public function addKryss($antall, $unixTid = false, $fakturert = false) {
		if ($unixTid === false) {
			$unixTid = $_SERVER['REQUEST_TIME'];
		}
		$this->settInnKryss(new Krysseliste\Kryss(
			date('Y-m-d H:i:s', $unixTid),
			$fakturert ? 1 : 0,
			$antall
		));
	}

	private function settInnKryss($kryss) {
		$this->getKryssListe();
		$this->kryssListe[] = $kryss;
	}

	public function getJson() {
		return json_encode($this->kryssListe);
	}

	public function oppdater() {
		$this->krysseliste = $this->getJson();
		if ($this->id == 0) {
			return $this->opprett();
		}
		$st = DB::getDB()->prepare('UPDATE krysseliste SET krysseliste=:krysseliste
WHERE beboer_id=:beboerId AND drikke_id=:drikkeId;');
		$st->bindParam(':beboerId', $this->beboerId);
		$st->bindParam(':drikkeId', $this->drikkeId);
		$st->bindParam(':krysseliste', $this->krysseliste);
		$st->execute();
	}

	private function opprett() {
		$st = DB::getDB()->prepare('INSERT INTO krysseliste(
	beboer_id,drikke_id,krysseliste
) VALUES(
	:beboerId,:drikkeId,:krysseliste
);');
		$st->bindParam(':beboerId', $this->beboerId);
		$st->bindParam(':drikkeId', $this->drikkeId);
		$st->bindParam(':krysseliste', $this->krysseliste);
		$st->execute();
		$this->id = DB::getDB()->lastInsertId();
	}

}

?>