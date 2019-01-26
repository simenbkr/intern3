<?php

namespace intern3;


class StorhybelVelger
{
    private $velger_id;
    private $beboer_ids;
    private $beboere;
    private $storhybel_id;
    private $nummer;
    private $i;


    public static function medVelgerId($velger_id): StorhybelVelger
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_velger WHERE velger_id=:vid');
        $st->bindParam(':vid', $velger_id);
        $st->execute();

        $instans = new StorhybelVelger();
        $instans->velger_id = $velger_id;
        $instans->beboer_ids = array();
        $instans->beboere = array();
        $instans->i = 0;

        $rad = $st->fetch();
        $instans->beboer_ids[] = $rad['beboer_id'];
        $instans->storhybel_id = $rad['storhybel_id'];
        $instans->nummer = Storhybelliste::staticNummerVelger($instans->velger_id, $instans->storhybel_id);
        $instans->beboere[] = Beboer::medId($rad['beboer_id']);

        while ($rad = $st->fetch()) {
            $instans->beboer_ids[] = $rad['beboer_id'];
            $instans->storhybel_id = $rad['storhybel_id'];
            $instans->nummer = Storhybelliste::staticNummerVelger($instans->velger_id, $instans->storhybel_id);
            $instans->beboere[] = Beboer::medId($rad['beboer_id']);
        }

        return $instans;
    }

    private static function medRad($rad) {

        if($rad == null) {
            return null;
        }

        $instans = new self();
        $instans->velger_id = $rad['velger_id'];
        $instans->beboer_ids = array($rad['beboer_id']);
        $instans->beboere = array();
        $instans->storhybel_id = $rad['storhybel_id'];
        $instans->nummer = -1;
        $instans->i = 0;

        return $instans;
    }

    public static function medBeboerIdStorhybelId($beboer_id, $storhybel_id)
    {
        $st = DB::getDB()->prepare('SELECT velger_id FROM storhybel_velger WHERE (beboer_id=:bid AND storhybel_id=:sid)');
        $st->bindParam(':bid', $beboer_id);
        $st->bindParam(':sid', $storhybel_id);
        $st->execute();

        /*
         * Returnerer Array med StrohybelVelgere fordi en beboer kan stå flere ganger
         * på lista dersom det f.eks. er en storhybelliste der par er tillatt på LPer (edge-case, woo).
         */

        $arr = array();

        while ($rad = $st->fetch()) {
            $arr[] = self::medVelgerId($rad['velger_id']);
        }

        return $arr;

    }

    public static function medStorhybel($storhybel_id): array
    {

        $st = DB::getDB()->prepare('SELECT DISTINCT velger_id FROM storhybel_velger WHERE storhybel_id=:sid ORDER BY velger_id ASC');
        $st->bindParam(':sid', $storhybel_id);
        $st->execute();

        $velgere = array();
        while ($rad = $st->fetch()) {
            $velgere[] = self::medVelgerId($rad['velger_id']);
        }

        usort($velgere, array('\intern3\StorhybelVelger', 'velgerSort'));

        return $velgere;
    }

    public static function velgerSort(StorhybelVelger $a, StorhybelVelger $b)
    {

        if ($a->getNummer() > $b->getNummer()) {
            return 1;
        }

        if ($a->getNummer() === $b->getNummer()) {
            return 0;
        }

        return -1;
    }

    public function getVelgerId(): int
    {
        return $this->velger_id;
    }

    public function getBeboerIds(): array
    {
        return $this->beboer_ids;
    }

    public function getStorhybelId(): int
    {
        return $this->storhybel_id;
    }

    private function createBeboere() {
        $tmp = array();
        foreach($this->beboer_ids as $bid) {
            $tmp[] = Beboer::medId($bid);
        }
        $this->beboere = $tmp;
    }

    public function getBeboere(): array
    {
        if($this->i === 0) {
            $this->createBeboere();
            $this->i = 1;
        }
        return $this->beboere;
    }

    public function getNummer(): int
    {
        return $this->nummer;
    }

    public static function nyVelgerListe(array $beboere) {

        $base = self::getNextId();
        $start = $base;

        $db = DB::getDB();
        $db->beginTransaction();

        $query = 'INSERT INTO storhybel_velger (velger_id, beboer_id) VALUES ';
        $datalist = array();
        foreach($beboere as $beboer) {
            $datalist[] = "({$base}, {$beboer->getId()})";
            $base++;
        }

        $query .= implode(',', $datalist);
        $st = $db->prepare($query);
        $st->execute();
        $db->commit();

        $velgere = array();
        $st = DB::getDB()->prepare('SELECT * from storhybel_velger WHERE (velger_id >= :start AND velger_id <= :slutt)');
        $st->bindParam(':start', $start);
        $st->bindParam(':slutt', $base);
        $st->execute();

        while(($rad = $st->fetch())) {
            $velgere[] = self::medRad($rad);
        }


        return $velgere;
    }

    public static function nyVelger(array $beboere)
    {

        $velger_id = self::getNextId();

        $instans = new self();
        $instans->velger_id = $velger_id;
        $instans->beboere = $beboere;
        $instans->beboer_ids = array();

        foreach ($beboere as $beboer) {
            $instans->beboer_ids[] = $beboer->getId();
        }

        $st = DB::getDB()->prepare('INSERT INTO storhybel_velger (velger_id,beboer_id) VALUES(:vid,:bid)');
        $st->bindParam(':vid', $velger_id);

        foreach ($instans->beboer_ids as $id) {
            $st->bindParam(':bid', $id);
            $st->execute();
        }

        return $instans;
    }

    public function setStorhybel(int $storhybel_id)
    {
        $this->storhybel_id = $storhybel_id;
        $st = DB::getDB()->prepare('UPDATE storhybel_velger SET storhybel_id=:sid WHERE velger_id=:vid');
        $st->bindParam(':sid', $storhybel_id);
        $st->bindParam(':vid', $this->velger_id);
        $st->execute();
    }

    public static function getNextId()
    {
        $st = DB::getDB()->prepare('SELECT velger_id FROM storhybel_velger ORDER BY velger_id DESC LIMIT 1');
        $st->execute();

        return $st->fetch()['velger_id'] + 1;
    }

    public function getNavn()
    {
        $navn = array();

        if (count($this->beboere) < 1) {
            return '';
        }

        foreach ($this->beboere as $beboer) {

            if (is_null($beboer)) {
                continue;
            }

            $navn[] = $beboer->getFulltNavn();
        }

        return implode(', ', $navn);

    }

    public function getAnsiennitet()
    {
        $a = 0;
        foreach ($this->beboere as $beboer) {
            /* @var $beboer \intern3\Beboer */
            $a += $beboer->getAnsiennitet();
        }

        return $a;
    }

    public function getKlassetrinn()
    {
        $klassetrinn = array();

        foreach ($this->beboere as $beboer) {
            /* @var $beboer \intern3\Beboer */
            $klassetrinn[] = $beboer->getKlassetrinn() . '.';
        }

        return implode(', ', $klassetrinn);
    }

    public function getMaxKlassetrinn()
    {

        $klassetrinn = array();

        foreach ($this->beboere as $beboer) {
            /* @var $beboer \intern3\Beboer */
            $klassetrinn[] = $beboer->getKlassetrinn();
        }

        return max($klassetrinn);

    }

}