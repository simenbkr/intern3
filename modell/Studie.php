<?php

namespace intern3;

class Studie {

	private $id;
	private $navn;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM studie WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medNavn($navn) {
		$st = DB::getDB()->prepare('SELECT * FROM studie WHERE navn=:navn;');
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
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getNavn() {
		return $this->navn;
	}

    public static function nyttStudie($studienavn)
    {
        $st = DB::getDB()->prepare('INSERT INTO studie (navn) VALUES (:studie);');
        $st->bindParam(':studie', $studienavn);
        $st->execute();

        return null;
    }

    public static function finnesStudie($studienavn) {
        $st = DB::getDB()->prepare("SELECT count(*) as cnt FROM studie WHERE navn LIKE LOWER(:navn)");
        $st->execute([ 'navn' => $studienavn]);

        return $st->fetch()['cnt'] > 0;
    }

}

?>