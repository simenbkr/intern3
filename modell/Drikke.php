<?php

namespace intern3;

class Drikke {

	private $id;
	private $navn;
	private $pris;
	private $vin;

	public static function medId($id) {
		$st = DB::getDB()->prepare('SELECT * FROM drikke WHERE id=:id;');
		$st->bindParam(':id', $id);
		$st->execute();
		return self::init($st);
	}
	public static function medNavn($navn) {
		$st = DB::getDB()->prepare('SELECT * FROM drikke WHERE navn=:navn;');
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
		$instance->pris = $rad['pris'];
		$instance->vin = $rad['vin'];
		return $instance;
	}

	public function getId() {
		return $this->id;
	}

	public function getNavn() {
		return $this->navn;
	}

	public function getPris() {
		return $this->pris;
	}

	public function erVin() {
		return $this->vin;
	}

	public static function alle(){
	    $st = DB::getDB()->prepare('SELECT * FROM drikke');
        $st->execute();
        $drikkene = array();
        for($i = 0; $i < $st->rowCount(); $i++){
            $drikkene[] = self::init($st);
        }
        return $drikkene;
    }

    public function oppdaterDrikkePris($pris){
        $st = DB::getDB()->prepare('UPDATE drikke SET pris=:pris WHERE id=:id');
        $st->bindParam(':pris', $pris);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

}

?>