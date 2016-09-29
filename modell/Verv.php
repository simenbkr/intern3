<?php

namespace intern3;

class Verv
{

    private $id;
    private $navn;
    private $utvalg;
    private $epost;

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
        $instance->utvalg = isset($rad['utvalg']) ? $rad['utvalg'] : 0;
        $instance->epost = $rad['epost'];
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
        $st = DB::getDB()->prepare('INSERT INTO beboer_verv (beboer_id,verv_id) VALUES(:beboer_id,:verv_id)');
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
        return isset($this->utvalg) ? $this->utvalg : 0;
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

}

?>