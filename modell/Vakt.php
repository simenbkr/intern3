<?php

namespace intern3;

class Vakt {

	private $id;
	private $brukerId;
	private $vakttype;
	private $dato;
	private $bekreftet;
	private $autogenerert;
	private $dobbelvakt;

	// Latskap
	private $bruker;
	private $vaktbytte;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM vakt WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medDatoVakttype($dato, $vakttype) {
		$st = DB::getDB()->prepare('SELECT * FROM vakt WHERE dato=:dato AND vakttype=:vakttype;');
		$st->bindParam(':dato', $dato);
		$st->bindParam(':vakttype', $vakttype);
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
		$instance->vakttype = $rad['vakttype'];
		$instance->dato = $rad['dato'];
		$instance->bekreftet = $rad['bekreftet'];
		$instance->autogenerert = $rad['autogenerert'];
		$instance->dobbelvakt = $rad['dobbelvakt'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getBrukerId() {
		return $this->brukerId;
	}

	public function getBruker() {
		if ($this->bruker == null) {
			$this->bruker = Bruker::medId($this->brukerId);
		}
		return $this->bruker;
	}

	public function getVakttype() {
		return $this->vakttype;
	}

	public function getDato() {
		return $this->dato;
	}

	public function erLedig() {
		return $this->brukerId==0 && $this->autogenerert;
	}

	public function getVaktbytte() {
		if ($this->vaktbytte == null) {
			$this->vaktbytte = Vaktbytte::medVaktId($this->id);
		}
		return $this->vaktbytte;
	}

	public function vilBytte() {
		return $this->getVaktbytte() <> null;
	}

  public function erFerdig() {
    $tid = strtotime($this->getDato());
    $tid = strtotime('midnight', $tid);
    switch ($this->getVakttype()) {
      case '1':
        $tid = strtotime('+8 hour', $tid);
        break;
      case '2':
        $tid = strtotime('+13 hour', $tid);
        break;
      case '3':
        $tid = strtotime('+19 hour', $tid);
        break;
      case '4':
        $tid = strtotime('+1 hour', $tid);
        break;
    }
    return $tid <= $_SERVER['REQUEST_TIME'];
  }
}

?>
