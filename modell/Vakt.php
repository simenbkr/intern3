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

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM vakt WHERE id=:id;');
		$st->bindParam(':id', $id);
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

}

?>