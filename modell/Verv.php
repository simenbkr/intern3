<?php

namespace intern3;

class Verv
{

    private $id;
    private $navn;
    private $utvalg;
    private $epost;
    private $regitimer;
    private $beskrivelse;

    // Latskap
    private $apmend = null;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM verv WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medNavn($navn)
    {
        $st = DB::getDB()->prepare('SELECT * FROM verv WHERE navn=:navn;');
        $st->bindParam(':navn', $navn);
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
        $instance->navn = $rad['navn'];
        $instance->utvalg = $rad['utvalg'] == 1 ? 1 : 0;
        $instance->epost = $rad['epost'];
        $instance->regitimer = $rad['regitimer'];
        $instance->beskrivelse = $rad['beskrivelse'];
        return $instance;
    }

    public static function deleteBeboerFromVerv($beboerId, $vervId)
    {
        $st = DB::getDB()->prepare('DELETE FROM beboer_verv WHERE (beboer_id=:beboer_id AND verv_id=:verv_id)');
        $st->bindParam(':beboer_id', $beboerId);
        $st->bindParam(':verv_id', $vervId);
        $st->execute();
    }

    public static function updateVerv($beboerId, $vervId)
    {
        //$st = DB::getDB()->prepare('UPDATE beboer_verv SET beboer_id=:beboer_id WHERE verv_id=:verv_id');
        $st = DB::getDB()->prepare('INSERT INTO beboer_verv SET beboer_id=:beboer_id, verv_id=:verv_id');
        $st->bindParam(':beboer_id', $beboerId);
        $st->bindParam(':verv_id', $vervId);
        $st->execute();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNavn()
    {
        return $this->navn;
    }

    public function erUtvalg()
    {
        //return true;
        //return isset($this->utvalg) ? $this->utvalg : 0;
        return $this->utvalg == 1;
    }

    public function getEpost()
    {
        return $this->epost;
    }

    public function getApmend()
    {
        if ($this->apmend == null) {
            $this->apmend = BeboerListe::medVervId($this->id);
        }
        return $this->apmend;
    }

    public function getRegitimer(){
        return $this->regitimer;
    }

    public function getBeskrivelse(){
        return $this->beskrivelse;
    }

    private function oppdater(){
        $st = DB::getDB()->prepare('UPDATE verv SET navn=:navn,beskrivelse=:beskrivelse WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':beskrivelse', $this->beskrivelse);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function setNavn($navn, $oppdater = true){
        $this->navn = $navn;

        if($oppdater)
            $this->oppdater();
    }

    public function setBeskrivelse($beskrivelse, $oppdater = true){
        $this->beskrivelse = $beskrivelse;

        if($oppdater)
            $this->oppdater();
    }

    public static function opprett($navn, $beskrivelse, $regitimer, $epost, $utvalg){
        $st = DB::getDB()->prepare('INSERT INTO verv (navn,beskrivelse,regitimer,epost,utvalg) 
                                             VALUES(:navn,:besk,:regi,:epost,:utv)');
        $st->bindParam(':navn', $navn);
        $st->bindParam(':besk', $beskrivelse);
        $st->bindParam(':regi', $regitimer);
        $st->bindParam(':epost', $epost);
        $st->bindParam(':utv', $utvalg);
        $st->execute();
    }

    public function slett(){
        $st = DB::getDB()->prepare('DELETE FROM verv WHERE id=:id');
        $st->bindParam(':id', $this->id);

        $st2 = DB::getDB()->prepare('DELETE FROM beboer_verv WHERE verv_id=:id');
        $st2->bindParam(':id', $this->id);

        $st2->execute();
        $st->execute();
    }

}

?>
