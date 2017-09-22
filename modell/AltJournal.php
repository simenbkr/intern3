<?php

/*
 * For å gjøre dette dynamisk er tanken følgende:
 * Vi har et status-objekt som inneholder flere mindre objekter av typen
 *Array(
 *'drikkeId' => id,
 *'mottatt' => mottatt,
 *'avlevert' => avlevert,
 *'pafyll' => pafyll
 *'utavskap' => utavskap)
 *
 *
 *
 *
 */

namespace intern3;

class AltJournal {

    private $id;
    private $bruker_id;
    private $vakt_nr;
    private $bruker;
    private $dato;
    private $status;

    public static function init(\PDOStatement $st) {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->bruker_id = $rad['bruker_id'];
        $instance->vakt_nr = $rad['vakt'];
        $instance->dato = $rad['dato'];
        $instance->status = $rad['status'];
        return $instance;
    }

    public static function medId($id){
        $st = DB::getDB()->prepare('SELECT * FROM alt_journal WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public function getId(){
        return $this->id;
    }

    public function getBrukerId(){
        return $this->bruker_id;
    }

    public function setBrukerId($brukerid){
        $this->bruker_id = $brukerid;
        $this->oppdaterAlt();
    }

    public function getBruker(){
        if(($bruker = Bruker::medId($this->bruker_id)) != null){
            return $bruker;
        }
        return Bruker::medId(Ansatt::getSisteAnsatt()->getBrukerId());
    }

    public function getVaktnr(){
        return $this->vakt_nr;
    }

    public function setVaktnr($vaktnr){
        $this->vakt_nr = $vaktnr;
        $this->oppdaterAlt();
    }

    public function getDato(){
        return $this->dato;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getStatusAsArray(){
        return json_decode($this->status, true);
    }

    public function oppdaterAlt(){
        $st = DB::getDB()->prepare('UPDATE alt_journal SET bruker_id=:bruker_id,vakt=:vakt,status=:status WHERE id=:id');
        $st->bindParam(':bruker_id', $this->bruker_id);
        $st->bindParam(':vakt', $this->vakt_nr);
        $st->bindParam(':status', $this->status);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function oppdaterStatus($status){
        $st = DB::getDB()->prepare('UPDATE alt_journal SET status=:status WHERE id=:id');
        $st->bindParam(':status', $status);
        $st->bindParam(':id', $id);
        $st->execute();
    }

    public function getStatusByDrikkeId($id){
        foreach($this->getStatusAsArray() as $objekt){
            if ($objekt['drikkeId'] == $id){
                return $objekt;
            }
        }
        return null;
    }

    public function updateObject($object){
        $status_array = $this->getStatusAsArray();
        $new_status = array($object);
        foreach($status_array as $objektet){
            if($object['drikkeId'] == $objektet['drikkeId']){
                continue;
            }
            $new_status[] = $objektet;
        }
        $this->updateStatus(json_encode($new_status,true));
    }

    private function updateStatus($status){
        $this->status = $status;
        $st = DB::getDB()->prepare('UPDATE alt_journal SET status=:status WHERE id=:id');
        $st->bindParam(':status', $this->status);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public static function getLatest(){
        $st = DB::getDB()->prepare('SELECT * FROM alt_journal ORDER BY id DESC LIMIT 1');
        $st->execute();
        return self::init($st);
    }

    public static function getSecondLatest(){
        $st = DB::getDB()->prepare('SELECT * FROM alt_journal ORDER BY id DESC LIMIT 2');
        $st->execute();
        $st->fetch();
        return self::init($st);
    }

    public static function avsluttVakt(AltJournal $vakt){
        $vaktnr = $vakt->getVaktnr() % 4 + 1;
        $dato = date('Y-m-d');
        $neste_vakt = Vakt::medDatoVakttype($dato, $vaktnr);
        $bruker_id = $neste_vakt->getBrukerId();
        if($bruker_id == null || Bruker::medId($bruker_id) == null) {
            $bruker_id = Ansatt::getSisteAnsatt()->getBrukerId();
        }
        $st = DB::getDB()->prepare('INSERT INTO alt_journal (bruker_id,vakt,status) VALUES(:bruker_id,:vakt,:status)');
        $st->bindParam(':bruker_id', $bruker_id);
        $vaktnr = $vakt->getVaktnr() % 4 + 1;
        $st->bindParam(':vakt', $vaktnr);
        $vakt->calcAvlevert();
        $st->bindParam(':status', json_encode($vakt->createFinishingStatus(), true));
        $st->execute();
    }

    public function createFinishingStatus(){
        $final = array();
        foreach($this->getStatusAsArray() as $objektet){
            $underobjekt = array(
                'drikkeId' => $objektet['drikkeId'],
                'mottatt' => $objektet['avlevert'],
                'avlevert' => $objektet['avlevert'],
                'pafyll' => 0,
                'utavskap' => 0
            );
            $final[] = $underobjekt;
        }
        return $final;
    }

    public function calcAvlevert(){
        $new_status = array();
        foreach($this->getStatusAsArray() as $obj){
            $obj['avlevert'] = $obj['mottatt'] + $obj['pafyll'] - $obj['utavskap'];
            $new_status[] = $obj;
        }
        $this->updateStatus(json_encode($new_status,true));
    }

    public function drukketDenneVakta($drikke_id){
        foreach($this->getStatusAsArray() as $obj){
            if($obj['drikkeId'] == $drikke_id && $obj['utavskap'] > 0){
                return true;
            }
        }
        return false;
    }

    //public function drukketDenneVakta($drikke_id){
     //   foreach($this->getStatusAsArray() as $obj){
     //       if($obj['drikkeId'] == $drikke_id && $obj['mottatt'] != $obj['avlevert']/*&& $obj['utavskap'] > 0 && $obj['mottatt'] > 0*/){
     //           return true;
      //      }
    //    }
   //     return false;
   // }
}
?>