<?php

namespace intern3;

class Drikke {

	private $id;
	private $navn;
	private $pris;
	private $vin;
    private $aktiv;
    private $farge;

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
        $instance->aktiv = $rad['aktiv'];
        $instance->farge = $rad['farge'];
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

	public function getAktiv(){
	    return $this->aktiv == 1;
    }

    public function getFarge(){
        return $this->farge;
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

    public static function aktive(){
        $st = DB::getDB()->prepare('SELECT * FROM drikke WHERE aktiv=1');
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

    public function setAktiv(){
        $st = DB::getDB()->prepare('UPDATE drikke SET aktiv=0 WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function setInaktiv(){
        $st = DB::getDB()->prepare('UPDATE drikke set aktiv=1 WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function oppdaterFarge($farge){
        $st = DB::getDB()->prepare('UPDATE drikke SET farge=:farge WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->bindParam(':farge', $farge);
        $st->execute();
    }

    public function harBlittDrukketSiden($dato, $drikke_id){
        $st = DB::getDB()->prepare('SELECT * FROM alt_journal WHERE dato>:dato');
        $st->bindParam(':dato', $dato);
        $st->execute();

        $alt_journaler = array();

        for($i = 0; $i < $st->rowCount(); $i++){
            $alt_journaler[] = AltJournal::init($st);
        }

        foreach($alt_journaler as $journal){
            if($journal->drukketDenneVakta($drikke_id)){
                return true;
            }
        }
        return false;
    }

}

?>