<?php

namespace intern3;

class Rom {

	private $id;
	private $navn;
	private $type;

	public static function medId($id) : Rom {
		$st = DB::getDB()->prepare('SELECT * FROM rom WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}

	public static function medNavn($navn) : Rom {
		$st = DB::getDB()->prepare('SELECT * FROM rom WHERE navn=:navn;');
		$st->bindParam(':navn', $navn);
		$st->execute();
		return self::init($st);
	}

	private static function init(\PDOStatement $st) : Rom {
		$rad = $st->fetch();
		if ($rad == null) {
			return null;
		}
		$instance = new self();
		$instance->id = $rad['id'];
		$instance->navn = $rad['navn'];
		$instance->type = Romtype::medId($rad['romtype_id']);
		return $instance;
	}

	public function getId()  : int {
		return $this->id;
	}

	public function getNavn() : string {
		return $this->navn;
	}

	public function getType() : Romtype {
        return $this->type;
    }

}

?>