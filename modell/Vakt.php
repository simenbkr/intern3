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

	public function erLedig() {
		// Fiks denne
		return false;
	}

}

?>