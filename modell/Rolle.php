<?php

namespace intern3;

class Rolle {

	private $id;
	private $navn;
	private $regitimer;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM rolle WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medNavn($navn) {
		$st = DB::getDB()->prepare('SELECT * FROM rolle WHERE navn=:navn;');
		$st->bindParam(':navn', $navn);
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
		$instance->navn = $rad['navn'];
		$instance->regitimer = $rad['regitimer'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getNavn() {
		return $this->navn;
	}

	public function getRegitimer() {
		return $this->regitimer;
	}

}

?>