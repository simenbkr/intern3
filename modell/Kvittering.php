<?php

namespace intern3;

class Kvittering {
	private $id;
	private $rom_id;
	private $bruker_id;
	private $tid_oppretta;
	
	private $db;

	public function __construct () {
		$this->db = DB::getDB();
	}
	private function init($pdo_statement) {
		$st = $pdo_statement;
		$st->execute();
		if($st->rowCount() > 0)
		{
			$rad = $st->fetch();
			
			$this->id                 = $rad['id'];
			$this->rom_id             = $rad['rom_id'];
			$this->bruker_id          = $rad['bruker_id'];
			$this->tid_oppretta       = $rad['tid_oppretta'];
		}
	}

	public static function medId($id) {
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM kvittering WHERE id = :id');
		$st->bindParam(':id' , $id);

		$instans->init($st);
		return $instans;
	}

	public static function medRomId($id) {
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM kvittering WHERE rom_id = :id');
		$st->bindParam(':id' , $id);

		$instans->init($st);
		return $instans;
	}

	public static function detteSemesterMedRomId($id) {
		$instans = new self();
		$st = $instans->db->prepare('SELECT * FROM kvittering WHERE rom_id = :id AND (tid_oppretta LIKE :m1 OR tid_oppretta LIKE :m2 OR tid_oppretta LIKE :m3 OR tid_oppretta LIKE :m4 OR tid_oppretta LIKE :m5 OR tid_oppretta LIKE :m6);');
		$st->bindParam(':id' , $id);
		$ar = date('Y');
		$s = date('m') > 6 ? 6 : 0;
		$p = array();
		for ($i = 1; $i <= 6; $i++) {
			$m = $i + $s;
			$m = $ar . '-' . (strlen($m) < 2 ? '0' : '') . $m . '-%';
			$p[':m' . $i] = $m;
		}
		foreach ($p as $k => &$v) {
			$st->bindParam($k, $v);
		}
		$instans->init($st);
		return $instans;
	}

	public function getId () {
		return $this->id;
	}

	public function getRomId () {
		return $this->rom_id;
	}
	public function getRom () {
		return Rom::medId($this->rom_id);
	}

	public function getBrukerId () {
		return $this->bruker_id;
	}
	public function getBruker() {
		return Bruker::medId($this->bruker_id);
	}

	public function getTidOppretta() {
		return $this->tid_oppretta;
	}
}

?>
