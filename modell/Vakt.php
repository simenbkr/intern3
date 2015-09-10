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
		$row = $st->fetch();
		if ($row == null) {
			return null;
		}
		$instance = new self();
		$instance->id = $row['id'];
		$instance->brukerId = $row['bruker_id'];
		$instance->vakttype = $row['vakttype'];
		$instance->dato = $row['dato'];
		$instance->bekreftet = $row['bekreftet'];
		$instance->autogenerert = $row['autogenerert'];
		$instance->dobbelvakt = $row['dobbelvakt'];
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