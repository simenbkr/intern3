<?php

namespace intern3;

class Vaktbytte {

	private $id;
	private $vaktId;
	private $gisBort;
	private $forslag;
	private $merknad;

	// Latskap
	private $vakt;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM vaktbytte WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medVaktId($vaktId) {
		$st = DB::getDB()->prepare('SELECT * FROM vaktbytte WHERE vakt_id=:vaktId;');
		$st->bindParam(':vaktId', $vaktId);
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
		$instance->vaktId = $rad['vakt_id'];
		$instance->gisBort = $rad['gisbort'];
		$instance->forslag = $rad['forslag'];
		$instance->merknad = $rad['merknad'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getVaktId() {
		return $this->vaktId;
	}

	public function getVakt() {
		if ($this->vakt == null) {
			$this->vakt = Vakt::medId($this->vaktId);
		}
		return $this->vakt;
	}

	public function getGisBort() {
		return $this->gisBort;
	}

	// public function getForslag() {
	// 	return $this->forslag;
	// }

	public function getMerknad() {
		return $this->merknad;
	}

}

?>
