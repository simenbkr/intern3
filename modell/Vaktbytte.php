<?php

namespace intern3;

class Vaktbytte
{

    private $id;
    private $vaktId;
    private $gisBort;
    private $forslag;
    private $forslagVaktListe;
    private $merknad;
    private $harPassord;
    private $passord;

    // Latskap
    private $vakt;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vaktbytte WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medVaktId($vaktId)
    {
        $st = DB::getDB()->prepare('SELECT * FROM vaktbytte WHERE vakt_id=:vaktId;');
        $st->bindParam(':vaktId', $vaktId);
        $st->execute();
        return self::init($st);
    }

    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->vaktId = $rad['vakt_id'];
        $instance->gisBort = $rad['gisbort'];
        $instance->forslag = $rad['forslag'] != null && strlen($rad['forslag']) ? explode(',',$rad['forslag']) : null;
        if($instance->forslag != null){
            $instance->forslagVaktListe = array();
            foreach($instance->forslag as $id){
                $instance->forslagVaktListe[] = Vakt::medId($id);
            }
        } else {
            $instance->forslag = null;
            $instance->forslagVaktListe = null;
        }
        $instance->merknad = $rad['merknad'];
        $instance->harPassord = $rad['har_passord'] == 0 ? false : true;
        $instance->passord = $instance->harPassord ? $rad['passord'] : null;
        return $instance;
    }

    public static function nyttVaktBytte($vaktId, $gisBort, $merknad, $harPassord, $passord){
        $gisBort = ($gisBort == 0 ? 0 : 1);
        $harPassord = ($harPassord == 0 ? 0 : 1);
        $passord = $harPassord == 0 ? null : isset($passord) ? $passord : "";
        $merknad = isset($merknad) ? $merknad : "";
        $st = DB::getDB()->prepare('INSERT INTO vaktbytte (vakt_id,gisbort,har_passord,passord,merknad) VALUES(
        :vaktId,:gisBort,:har_passord,:passord,:merknad)');
        $st->bindParam(':vaktId', $vaktId);
        $st->bindParam(':gisBort', $gisBort);
        $st->bindParam(':merknad', $merknad);
        $st->bindParam(':har_passord', $harPassord);
        $st->bindParam(':passord', $passord);
        $st->execute();
    }

    public static function slettEgetVaktBytte($vaktBytteId, $vaktId){
        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id AND vakt_id=:vakt_id');
        $st->bindParam(':id', $vaktBytteId);
        $st->bindParam(':vakt_id', $vaktId);
        $st->execute();
    }

    public function slett(){

        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();
        unset($this);
    }

    public static function taVakt($vaktId, $bruker_id){
        $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id, bekreftet=1,autogenerert=0,bytte=0 WHERE id=:id');
        $st->bindParam(':bruker_id', $bruker_id);
        $st->bindParam(':id', $vaktId);
        $st->execute();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getVaktId()
    {
        return $this->vaktId;
    }

    public function getVakt()
    {
        if ($this->vakt == null) {
            $this->vakt = Vakt::medId($this->vaktId);
        }
        return $this->vakt;
    }

    public function getGisBort()
    {
        return $this->gisBort;
    }

    public function getForslagIder()
    {
        return $this->forslag;
    }

    public function getForslagVakter(){
        return $this->forslagVaktListe;
    }

    public function getMerknad()
    {
        return $this->merknad;
    }

    public function harPassord(){
        return $this->harPassord;
    }

    public function stemmerPassord($pass){
        return $pass == $this->passord || trim($pass) == $this->passord;
    }

    public function erVaktMedIByttet($vaktId){
        return in_array(Vakt::medId($vaktId), $this->forslagVaktListe);
    }

    public function slettForslag($vakt_id){
        if(in_array($vakt_id, $this->forslag)){
            $nye_forslag = array();
            foreach($this->forslag as $id){
                if ($id != $vakt_id){
                    $nye_forslag[] = $id;
                }
            }
            $nye_forslag = implode(',', array_filter($nye_forslag));
            $st = DB::getDB()->prepare('UPDATE vaktbytte SET forslag=:forslag WHERE id=:id');
            $st->bindParam(':forslag', $nye_forslag);
            $st->bindParam(':id', $this->id);
            $st->execute();
        }
    }

    public static function getAlleUtgatte(){

        $date = date('Y-m-d');

        $sql = "SELECT vaktbytte.id FROM vaktbytte WHERE vaktbytte.vakt_id IN (SELECT vakt.id FROM vakt WHERE vakt.dato<:datoen)";
        $st = DB::getDB()->prepare($sql);
        $st->bindParam(':datoen', $date);
        $st->execute();

        $vaktbytter = array();

        for($i = 0; $i < $st->rowCount(); $i++){
            $vaktbytter[] = Vaktbytte::medId($st->fetch()['id']);
        }

        return $vaktbytter;
    }
    
    public static function getAlleMulige(){
    
        $date = date('Y-m-d');
    
        $sql = "SELECT vaktbytte.id FROM vaktbytte WHERE vaktbytte.vakt_id IN (SELECT vakt.id FROM vakt WHERE vakt.dato>:datoen)";
        $st = DB::getDB()->prepare($sql);
        $st->bindParam(':datoen', $date);
        $st->execute();
    
        $vaktbytter = array();
    
        for($i = 0; $i < $st->rowCount(); $i++){
            $vaktbytter[] = Vaktbytte::medId($st->fetch()['id']);
        }
    
        return $vaktbytter;
        
    }

    public static function getAlle(){


        $sql = "SELECT id FROM vaktbytte";
        $st = DB::getDB()->prepare($sql);
        $st->execute();

        $vaktbytter = array();

        for($i = 0; $i < $st->rowCount(); $i++){
            $vaktbytter[] = Vaktbytte::medId($st->fetch()['id']);
        }

        return $vaktbytter;

    }

    public function leggTilForslag($vaktId){
        if($this->forslag == null){
            $forslag = $vaktId;
        } else {
            $forslag = array_push($this->forslag, $vaktId);
        }

        $st = DB::getDB()->prepare('UPDATE vaktbytte SET forslag=:forslag WHERE id=:id');
        $st->bindParam(':forslag',$forslag);
        $st->bindParam(':id', $this->id);
        $st->execute();

    }

}
?>
