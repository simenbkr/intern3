<?php

namespace intern3;

class Helga
{
    private $start_dato;
    private $slutt_dato;
    private $generaler;
    private $tema;
    private $aar;
    private $klar;
    private $max_gjester;
    private $max_alle;
    private $same;
    private $antall_gjester;
    private $gjestelista;
    private $epost_tekst;


    public static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->aar = $rad['aar'];

        if($rad['start_dato'] != null){
            $instance->start_dato = date('Y-m-d', strtotime($rad['start_dato']));
            $instance->slutt_dato = date('Y-m-d', strtotime($rad['start_dato'] . ' + 2 days'));
        } else {
            $instance->start_dato = null;
            $instance->slutt_dato = null;
        }
        if($rad['generaler'] != null) {
            $instance->generaler = json_decode($rad['generaler'], true);
        } else {
            $instance->generaler = array();
        }
        $instance->tema = $rad['tema'];
        $instance->klar = $rad['klar'];
        $instance->max_alle = $rad['max_gjest'];
        $instance->same = $rad['same'];
        $instance->max_gjester = array(
            'torsdag' => $rad['num_torsdag'],
            'fredag' => $rad['num_fredag'],
            'lordag' => $rad['num_lordag']
        );
        $instance->epost_tekst = $rad['epost_text'];
        $instance->antall_gjester = HelgaGjesteListe::getGjesteCount($instance->aar);
        $instance->gjestelista = HelgaGjesteListe::getGjesteCount($instance->aar);
        return $instance;
    }

    public static function medAar($aar) {

        $st = DB::getDB()->prepare('SELECT * FROM helga WHERE aar=:aar ORDER BY aar DESC LIMIT 1');
        $st->bindParam(':aar', $aar);
        $st->execute();

        return self::init($st);
    }

    public function getStartDato()
    {
        return $this->start_dato;
    }

    public function setStartDato($start) {
        $this->start_dato = date('Y-m-d', strtotime($start));
        $this->slutt_dato = date('Y-m-d', strtotime($start . ' + 2 days'));
        $this->oppdater();
    }

    public function getSluttDato()
    {
        return $this->slutt_dato;
    }

    public function getGeneralIder()
    {
        return $this->generaler;
    }

    public function getGeneraler(){
        $generalene = array();
        if(count($this->generaler) > 0){
            foreach ($this->generaler as $id) {
                $beboeren = Beboer::medId($id);
                if($beboeren != null){
                    $generalene[] = $beboeren;
                }
            }
        } else {
            return null;
        }
        return $generalene;
    }

    public function getGjesteliste()
    {
        return $this->gjestelista;
    }

    public function getAntallGjester()
    {
        return $this->antall_gjester;
    }

    public function getAntallPerDag()
    {
        $antall_per_dag = array(
            'torsdag' => 0,
            'fredag' => 0,
            'lordag' => 0
        );

        //public static function getGjesteCountDagBeboer($dag, $beboerid,$aar){
        foreach (BeboerListe::aktive() as $beboer) {
            $antall_per_dag['torsdag'] += HelgaGjesteListe::getGjesteCountDagBeboer(0, $beboer->getId(), $this->getAar());
            $antall_per_dag['fredag'] += HelgaGjesteListe::getGjesteCountDagBeboer(1, $beboer->getId(), $this->getAar());
            $antall_per_dag['lordag'] += HelgaGjesteListe::getGjesteCountDagBeboer(2, $beboer->getId(), $this->getAar());
        }
        return $antall_per_dag;
    }

    public function getAntallInnePerDag(){
        $antall_per_dag = array(
            'torsdag' => 0,
            'fredag' => 0,
            'lordag' => 0
        );

        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE aar=:aar');
        $st->bindParam(':aar', $this->aar);
        $st->execute();

        for($i = 0; $i < $st->rowCount(); $i++){
            $rad = $st->fetch();
            if($rad['inne'] == 0){
                continue;
            }
            switch($rad['dag']){
                case 0:
                    $antall_per_dag['torsdag']++;
                    break;
                case 1:
                    $antall_per_dag['fredag']++;
                    break;
                case 2:
                    $antall_per_dag['lordag']++;
            }
        }
        return $antall_per_dag;
    }

    public function getAar()
    {
        return $this->aar;
    }

    public function getTema()
    {
        return $this->tema;
    }

    public function getKlar()
    {
        return $this->klar != 0;
    }

    public function setKlar() {
        $this->klar = 1;
        $this->oppdater();
    }

    public function getMaxGjester()
    {
        return $this->max_gjester;
    }

    public function getEpostTekst()
    {
        return $this->epost_tekst;
    }

    public function setTema($tema) {
        $this->tema = $tema;
        $this->oppdater();
    }

    public function getGeneralerAsFornavn()
    {
        $string = "";
        foreach ($this->generaler as $general) {
            $string .= $general->getFornavn() . ', ';
        }
        return rtrim($string, ', ');
    }

    public function setEpostTekst($epost_tekst)
    {
        $this->epost_tekst = $epost_tekst;
        $this->oppdater();
    }

    public function setMaxGjester(array $antall)
    {
        $this->max_gjester = $antall;
        $this->oppdater();
    }

    public function setMaxTorsdag(int $antall) {
        $this->max_gjester['torsdag'] = $antall;
        $this->oppdater();
    }

    public function setMaxFredag(int $antall) {
        $this->max_gjester['fredag'] = $antall;
        $this->oppdater();
    }

    public function setMaxLordag(int $antall) {
        $this->max_gjester['lordag'] = $antall;
        $this->oppdater();
    }

    public function getMaxAlle(){
        return $this->max_alle;
    }

    public function setMaxAlle(int $antall) {
        $this->max_alle = $antall;
        $this->oppdater();
    }

    public function erSameMax() {
        return $this->same == 1;
    }

    public function setSameMax($value) {
        if($value == 'on') {
            $this->same = 1;
        } else {
            $this->same = 0;
        }
        $this->oppdater();
    }

    public function addGeneral($beboer_id)
    {
        $this->generaler[] = $beboer_id;
        $this->oppdater();
    }

    public function removeGeneral($beboer_id)
    {
        $nye_generaler = array();
        foreach ($this->generaler as $id) {
            if ($id != $beboer_id) {
                $nye_generaler[] = $id;
            }
        }
        $this->generaler = $nye_generaler;
        $this->oppdater();
    }

    public function changeTema($nytt_tema)
    {
        $this->tema = $nytt_tema;
        $this->oppdater();
    }

    public function changeDato($ny_start_dato)
    {
        $this->start_dato = date('Y-m-d', strtotime($ny_start_dato));
        $this->slutt_dato = date('Y-m-d', strtotime($ny_start_dato . ' + 2 days'));
        $this->oppdater();
    }

    private function oppdater()
    {
        $general_ider = json_encode($this->generaler);
        $st = DB::getDB()->prepare('UPDATE helga SET 
                                              start_dato=:start_dato,
                                              slutt_dato=:slutt_dato, 
                                              generaler=:generaler, 
                                              tema=:tema, 
                                              epost_text=:epost_tekst,
                                              num_torsdag=:torsdag, 
                                              num_fredag=:fredag,
                                              num_lordag=:lordag,
                                              klar=:klar,
                                              same=:same,
                                              max_gjest=:max_alle
                                              WHERE aar=:aar');
        $st->bindParam(':start_dato', $this->start_dato);
        $st->bindParam(':slutt_dato', $this->slutt_dato);
        $st->bindParam(':generaler', $general_ider);
        $st->bindParam(':tema', $this->tema);
        $st->bindParam(':aar', $this->aar);
        $st->bindParam(':epost_tekst', $this->epost_tekst);

        $st->bindParam(':torsdag', $this->max_gjester['torsdag']);
        $st->bindParam(':fredag', $this->max_gjester['fredag']);
        $st->bindParam(':lordag', $this->max_gjester['lordag']);
        $st->bindParam(':klar', $this->klar);
        $st->bindParam(':max_alle', $this->max_alle);
        $st->bindParam(':same', $this->same);

        $st->execute();
    }

    public static function getAlleHelga()
    {

        $helgaene = array();

        $st = DB::getDB()->prepare('SELECT * FROM helga ORDER BY aar DESC');
        $st->execute();

        for($i = 0; $i < $st->rowCount(); $i++) {
            $helgaene[] = self::init($st);
        }
        return $helgaene;
    }

    public static function fraSQLRad($rad)
    {
        return new self($rad['aar'], $rad['start_dato'], $rad['slutt_dato'], $rad['generaler'], $rad['tema'], $rad['klar'], $rad['max_gjest']);
    }

    public static function getLatestHelga()
    {
        $st = DB::getDB()->prepare('SELECT * FROM helga ORDER BY aar DESC LIMIT 1');
        $st->execute();

        if($st->rowCount() > 0){
            return self::init($st);
        }
        return null;
/*
        if ($st->rowCount() > 0) {
            $rader = $st->fetchAll()[0];
        } else {
            return null;
        }
        $generaler = array();
        $json_generaler = json_decode($rader['generaler'], true);
        if ($json_generaler != null) {
            foreach ($json_generaler as $general) {
                $generaler[] = Beboer::medId($general);
            }
        }

        return new self($rader['aar'], $rader['start_dato'], $rader['slutt_dato'], $generaler, $rader['tema'], $rader['klar'], $rader['max_gjest'], $rader['epost_tekst']);*/
    }

    public static function getHelgaByAar($aar)
    {
        $st = DB::getDB()->prepare('SELECT * from helga WHERE aar=:aar');
        $st->bindParam(':aar', $aar);
        $st->execute();

        if($st->rowCount() > 0){
            return self::init($st);
        }
        return null;
    }

    public static function createHelga($start_dato)
    {
        $aar = date('Y', strtotime($start_dato));
        $slutt_dato = date('Y-m-d', strtotime($start_dato . ' + 2 days'));
        $st = DB::getDB()->prepare('INSERT INTO helga (aar, start_dato, slutt_dato) VALUES(:aar, :start_dato, :slutt_dato)');
        $st->bindParam(':aar', $aar);
        $st->bindParam(':start_dato', $start_dato);
        $st->bindParam(':slutt_dato', $slutt_dato);
        $st->execute();
        return self::getHelgaByAar($aar);
    }

    public static function createBareBoneHelga($aar)
    {
        $st = DB::getDB()->prepare('INSERT INTO helga (aar) VALUES(:aar)');
        $st->bindParam(':aar', $aar);
        $st->execute();
    }

    public function erHelgaGeneral($beboer_id)
    {
        return in_array($beboer_id, $this->generaler);
    }

}