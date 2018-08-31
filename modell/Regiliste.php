<?php

namespace intern3;


class Regiliste
{

    private $id;
    private $navn;
    private $beboerliste;
    private $idliste;
    private $totale_timer;

    private static function init(\PDOStatement $st)
    {

        $rad = $st->fetch();
        if ($rad === null) {
            return null;
        }

        $instans = new self();
        $instans->id = $rad['id'];
        $instans->navn = $rad['navn'];

        $instans->beboerliste = array();
        $instans->idliste = array();

        $st = DB::getDB()->prepare('SELECT beboer_id AS bebid FROM regiliste_beboer WHERE (regiliste_id = :id
        AND regiliste_beboer.beboer_id IN (SELECT id FROM beboer WHERE romhistorikk LIKE :ikkeUtflyttet))');

        $ikkeUtflyttet = '%"utflyttet":NULL%';
        $st->bindParam(':id', $instans->id);
        $st->bindParam(':ikkeUtflyttet', $ikkeUtflyttet);
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $rad = $st->fetch();
            $instans->beboerliste[] = Beboer::medId($rad['bebid']);
            $instans->idliste[] = $rad['bebid'];
        }

        $instans->totale_timer = 0;
        foreach ($instans->beboerliste as $beboer) {
            /* @var \intern3\Beboer $beboer */

            $instans->totale_timer += $beboer->getRolle()->getRegitimer() - $beboer->getBruker()->getRegisekunderMedSemester() / (60 * 60);

            /*
            if($beboer->getRolle()->getRegitimer() > 18) {
                $instans->totale_timer += 48 - $beboer->getBruker()->getRegisekunderMedSemester() / (60 * 60);
            } elseif($beboer->getRolle()->getRegitimer() === 18){
                $instans->totale_timer += 18 - $beboer->getBruker()->getRegisekunderMedSemester() / (60 * 60);
            }
            */

        }

        return $instans;
    }

    public static function getAlleLister()
    {

        $st = DB::getDB()->prepare('SELECT * FROM regiliste');
        $st->execute();
        $lister = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $lister[] = self::init($st);
        }

        return $lister;
    }

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM regiliste WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }


    public function getId()
    {
        return $this->id;
    }

    public function getNavn()
    {
        return $this->navn;
    }

    public function getBeboerliste()
    {
        return $this->beboerliste;
    }

    public function getIdliste()
    {
        return $this->idliste;
    }

    public function getTotaleTimerIgjen()
    {
        return $this->totale_timer;
    }

    public static function opprett($navn, $idliste = null)
    {

        $st = DB::getDB()->prepare('INSERT INTO regiliste (navn) VALUES(:navn)');
        $st->bindParam(':navn', $navn);
        $st->execute();

        if ($idliste === null || count($idliste) < 1) {
            return;
        }

        $st = DB::getDB()->prepare('SELECT * FROM regiliste WHERE navn=:navn ORDER BY ID DESC LIMIT 1');
        $st->bindParam(':navn', $navn);
        $st->execute();

        $instans = self::init($st);

        $gyldige_ider = array();

        foreach ($idliste as $id) {
            if (($beboer = Beboer::medId($id)) !== null && $beboer->erAktiv()) {
                $gyldige_ider[] = $id;
            }
        }

        foreach ($gyldige_ider as $id) {
            $st = DB::getDB()->prepare('INSERT INTO regiliste_beboer (regiliste_id, beboer_id) VALUES(:regi_id,:beboer_id)');
            $st->bindParam(':regi_id', $instans->getId());
            $st->bindParam(':beboer_id', $id);
            $st->execute();
        }

    }

    public function endreNavn($navn)
    {

        if ($navn !== $this->navn) {
            $this->navn = $navn;
            $this->oppdater();
        }
    }


    public function endreValgte($valgte)
    {

        //TODO fiks mysteribug.

        $this->idliste = $valgte;
        $this->oppdater();


    }

    private function oppdater()
    {

        $st = DB::getDB()->prepare('UPDATE regiliste SET navn=:navn WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':id', $this->id);
        $st->execute();

        $st = DB::getDB()->prepare('DELETE FROM regiliste_beboer WHERE regiliste_id=:regiid');
        $st->bindParam(':regiid', $this->id);
        $st->execute();

        foreach ($this->idliste as $id) {
            $st2 = DB::getDB()->prepare('INSERT INTO regiliste_beboer (regiliste_id, beboer_id) VALUES(:regi_id,:beboer_id)');
            $st2->bindParam(':regi_id', $this->getId());
            $st2->bindParam(':beboer_id', $id);
            $st2->execute();
        }

    }

    public static function addBeboerToListe($liste_id, $beboer_id)
    {
        $st = DB::getDB()->prepare('INSERT INTO regiliste_beboer (regiliste_id, beboer_id) VALUES(:regi_id,:beboer_id)');
        $st->bindParam(':regi_id', $liste_id);
        $st->bindParam(':beboer_id', $beboer_id);
        $st->execute();

    }

    public static function removeBeboerFromListe($liste_id, $beboer_id)
    {
        $st = DB::getDB()->prepare('DELETE FROM regiliste_beboer WHERE (regiliste_id=:regi_id AND beboer_id=:beboer_id)');
        $st->bindParam(':regi_id', $liste_id);
        $st->bindParam(':beboer_id', $beboer_id);
        $st->execute();
    }

    public function slett()
    {

        $st = DB::getDB()->prepare('DELETE FROM regiliste_beboer WHERE regiliste_id=:regi_id');
        $st->bindParam(':regi_id', $this->id);
        $st->execute();

        $st = DB::getDB()->prepare('DELETE FROM regiliste WHERE id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();

    }


}