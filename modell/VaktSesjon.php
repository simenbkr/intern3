<?php

namespace intern3;

class VaktSesjon
{

    private $id;
    private $beboerId;
    private $vaktnr;
    private $dato;

    private $ol;
    private $cider;
    private $carlsberg;
    private $rikdom;

    public function __construct($id, $beboerId, $vaktnr, $dato, $ol_mottatt, $ol_pafyll, $ol_avlevert, $ol_utavskap,
                                $cider_mottatt, $cider_pafyll, $cider_avlevert, $cider_utavskap,
                                $carls_mottatt, $carls_pafyll, $carls_avlevert, $carls_utavskap,
                                $rikdom_mottatt, $rikdom_pafyll, $rikdom_avlevert, $rikdom_utavskap)
    {

        $this->id = $id;
        $this->beboerId = $beboerId;
        $this->vaktnr = $vaktnr;
        $this->dato = $dato;

        $this->ol = array(
            'mottatt' => $ol_mottatt,
            'pafyll' => $ol_pafyll,
            'avlevert' => $ol_avlevert,
            'utavskap' => $ol_utavskap
        );

        $this->cider = array(
            'mottatt' => $cider_mottatt,
            'pafyll' => $cider_pafyll,
            'avlevert' => $cider_avlevert,
            'utavskap' => $cider_utavskap
        );

        $this->carlsberg = array(
            'mottatt' => $carls_mottatt,
            'pafyll' => $carls_pafyll,
            'avlevert' => $carls_avlevert,
            'utavskap' => $carls_utavskap
        );

        $this->rikdom = array(
            'mottatt' => $rikdom_mottatt,
            'pafyll' => $rikdom_pafyll,
            'avlevert' => $rikdom_avlevert,
            'utavskap' => $rikdom_utavskap
        );
    }

    private static function init(\PDOStatement $st)
    {
        $row = $st->fetch();
        if ($row == null) {
            return null;
        }
        return new self($row['kryss_id'], $row['beboer_id'], $row['vakt'], $row['dato'],
            $row['ol_mottatt'], $row['ol_pafyll'], $row['ol_avlevert'], $row['ol_utavskap'],
            $row['cid_mottatt'], $row['cid_pafyll'], $row['cid_avlevert'], $row['cid_utavskap'],
            $row['carls_mottatt'], $row['carls_pafyll'], $row['carls_avlevert'], $row['carls_utavskap'],
            $row['rikdom_mottatt'], $row['rikdom_pafyll'], $row['rikdom_avlevert'], $row['rikdom_utavskap']);
    }

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM journal WHERE kryss_id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medBeboerId($beboer_id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM journal WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $beboer_id);
        $st->execute();
        $vakter = array();
        $rader = $st->rowCount();
        for ($i = 0; $i < $rader; $i++) {
            $vakter[] = self::init($st);
        }
    }

    public static function getLatest(){
        $st = DB::getDB()->prepare('SELECT * FROM journal ORDER BY kryss_id DESC LIMIT 1');
        $st->execute();
        return self::init($st);
    }

    public static function getSecondLatest(){
        $st = DB::getDB()->prepare('SELECT * FROM journal ORDER BY kryss_id DESC LIMIT 1,1');
        $st->execute();
        return self::init($st);
    }

    private function Oppdater()
    {
        $st = DB::getDB()->prepare('UPDATE journal SET beboer_id=:beboer_id,vakt=:vakt,
ol_mottatt=:ol_mottatt,ol_pafyll=:ol_pafyll,ol_avlevert=:ol_avlevert,ol_utavskap=:ol_utavskap,
cid_mottatt=:cid_mottatt,cid_pafyll=:cid_pafyll,cid_avlevert=:cid_avlevert, cid_utavskap=:cid_utavskap,
carls_mottatt=:carls_mottatt,carls_pafyll=:carls_pafyll,carls_avlevert=:carls_avlevert, carls_utavskap=:carls_utavskap,
rikdom_mottatt=:rikdom_mottatt,rikdom_pafyll=:rikdom_pafyll,rikdom_avlevert=:rikdom_avlevert, rikdom_utavskap=:rikdom_utavskap
WHERE kryss_id=:id');
        $st->bindParam(':id', $this->id);
        $st->bindParam(':beboer_id', $this->beboerId);
        $st->bindParam(':vakt', $this->vaktnr);

        foreach ($this->ol as $infoen => $tallet) {
            $bind = ':ol_' . $infoen;
            $st->bindParam($bind, $tallet);
        }
        foreach ($this->cider as $infoen => $tallet) {
            $bind = ':cid_' . $infoen;
            $st->bindParam($bind, $tallet);
        }
        foreach ($this->carlsberg as $infoen => $tallet) {
            $bind = ':carls_' . $infoen;
            $st->bindParam($bind, $tallet);
        }
        foreach ($this->rikdom as $infoen => $tallet) {
            $bind = ':rikdom_' . $infoen;
            $st->bindParam($bind, $tallet);
        }
        $st->execute();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBeboerId()
    {
        return $this->beboerId;
    }

    public function getVaktnr()
    {
        return $this->vaktnr;
    }

    public function getDato()
    {
        return $this->dato;
    }

    public function getOl()
    {
        return $this->ol;
    }

    public function getCider()
    {
        return $this->cider;
    }

    public function getCarlsberg()
    {
        return $this->carlsberg;
    }

    public function getRikdom()
    {
        return $this->rikdom;
    }

    public function setBeboerId($beboer_id){
        $this->beboerId = $beboer_id;
        $this->Oppdater();
    }

    public function setVaktnr($vaktnr){
        $this->vaktnr = $vaktnr;
        $this->Oppdater();
    }

    public function setDato($dato){
        $this->dato = $dato;
        $this->Oppdater();
    }

    public function setOl($ol){
        $this->ol = $ol;
        $this->Oppdater();
    }

    public function setCider($cider){
        $this->cider = $cider;
        $this->Oppdater();
    }

    public function setCarlsberg($carlsberg){
        $this->carlsberg = $carlsberg;
        $this->Oppdater();
    }

    public function setRikdom($rikdom){
        $this->rikdom = $rikdom;
        $this->Oppdater();
    }

}
?>