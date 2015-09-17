<?php

namespace intern3;

class Utleie {

	private $id;
	private $dato;
	private $navn;
	private $beboer1_id;
	private $beboer2_id;
	private $beboer3_id;
	private $rom;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM utleie WHERE id=:id;');
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
		$instance->dato = $row['dato'];
		$instance->navn = $row['navn'];
		$instance->beboer1_id = $row['beboer1_id'];
		$instance->beboer2_id = $row['beboer2_id'];
		$instance->beboer3_id = $row['beboer3_id'];
		$instance->rom = $row['rom'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getDato() {
		return $this->dato;
	}

	public function getNavn() {
		return $this->navn;
	}

	public function getBeboer1_id() {
		return $this->beboer1_id;
	}

	public function getBeboer2_id() {
		return $this->beboer2_id;
	}

	public function getBeboer3_id() {
		return $this->beboer3_id;
	}

	public function getRom() {
		return $this->rom;
	}
}
?>
