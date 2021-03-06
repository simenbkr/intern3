<?php

namespace intern3;

class Romtype {

	private $id;
	private $navn;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM romtype WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}

	public static function medNavn($navn) {
        $st = DB::getDB()->prepare('SELECT * FROM romtype WHERE navn=:navn;');
        $st->bindParam(':navn', $navn);
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
		$instance->navn = $row['navn'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getNavn() {
		return $this->navn;
	}

}

?>