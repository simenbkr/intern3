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
    private $antall_gjester;
    private $gjestelista;

    public function __construct($aar, $start_dato, $slutt_dato = null, $generaler = null, $tema = null, $klar = null, $max_gjester = 15, $epost_tekst = null)
    {
        if ($start_dato != null) {
            $this->start_dato = date('Y-m-d', strtotime($start_dato));
            $this->slutt_dato = date('Y-m-d', strtotime($start_dato . ' + 2 days'));
        } else {
            $this->start_dato = null;
            $this->slutt_dato = null;
        }
        $this->generaler = $generaler;
        $this->tema = $tema;
        $this->aar = $aar;
        $this->klar = $klar;
        $this->max_gjester = $max_gjester;
        $this->epost_tekst = $epost_tekst;
        $this->antall_gjester = HelgaGjesteListe::getGjesteCount($this->aar);
        $this->gjestelista = HelgaGjesteListe::getAlleGjesterAar($this->aar);
    }

    public function getStartDato()
    {
        return $this->start_dato;
    }

    public function getSluttDato()
    {
        return $this->slutt_dato;
    }

    public function getGeneraler()
    {
        return $this->generaler;
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
        $beboerne = BeboerListe::aktive();
        foreach ($beboerne as $beboer) {
            $antall_per_dag['torsdag'] += HelgaGjesteListe::getGjesteCountDagBeboer(0, $beboer->getId(), $this->getAar());
            $antall_per_dag['fredag'] += HelgaGjesteListe::getGjesteCountDagBeboer(1, $beboer->getId(), $this->getAar());
            $antall_per_dag['lordag'] += HelgaGjesteListe::getGjesteCountDagBeboer(2, $beboer->getId(), $this->getAar());
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

    public function getMaxGjester()
    {
        return $this->max_gjester;
    }

    public function getEpostTekst()
    {
        return $this->epost_tekst;
    }

    public function getGeneralerAsFornavn()
    {
        $string = "";
        foreach ($this->generaler as $general) {
            $string .= $general->getFornavn() . ',';
        }
        return rtrim($string, ',');
    }

    public function setEpostTekst($epost_tekst)
    {
        $this->epost_tekst = $epost_tekst;
        $this->oppdater();
    }

    public function setMaxGjester($antall)
    {
        $this->max_gjester = $antall;
        $this->oppdater();
    }

    public function addGeneral($beboer_id)
    {
        $this->generaler[] = Beboer::medId($beboer_id);
        $this->oppdater();
    }

    public function removeGeneral($beboer_id)
    {
        $nye_generaler = array();
        foreach ($this->generaler as $generalen) {
            if ($generalen->getId() != $beboer_id) {
                $nye_generaler[] = $generalen;
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
        $general_ider = array();

        foreach ($this->generaler as $general) {
            $general_ider[] = $general->getId();
        }

        $st = DB::getDB()->prepare('UPDATE helga SET start_dato=:start_dato, slutt_dato=:slutt_dato, generaler=:generaler,tema=:tema, max_gjest=:max_gjest, epost_tekst=:epost_tekst WHERE aar=:aar');
        $st->bindParam(':start_dato', $this->start_dato);
        $st->bindParam(':slutt_dato', $this->slutt_dato);
        $st->bindParam(':generaler', json_encode($general_ider));
        $st->bindParam(':tema', $this->tema);
        $st->bindParam(':aar', $this->aar);
        $st->bindParam(':max_gjest', $this->max_gjester);
        $st->bindParam(':epost_tekst', $this->epost_tekst);
        $st->execute();
    }

    public static function getAlleHelga()
    {

        $helgaene = array();

        $st = DB::getDB()->prepare('SELECT * FROM helga ORDER BY aar DESC');
        $st->execute();
        $rader = $st->fetchAll();

        foreach ($rader as $rad) {
            $helgaene[] = self::fraSQLRad($rad);
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

        return new self($rader['aar'], $rader['start_dato'], $rader['slutt_dato'], $generaler, $rader['tema'], $rader['klar'], $rader['max_gjest'], $rader['epost_tekst']);
    }

    public static function getHelgaByAar($aar)
    {
        $st = DB::getDB()->prepare('SELECT * from helga WHERE aar=:aar');
        $st->bindParam(':aar', $aar);
        $st->execute();

        $res = $st->fetchAll()[0];

        $generaler = array();
        $json_generaler = json_decode($res['generaler'], true);
        if ($json_generaler != null) {
            foreach ($json_generaler as $general) {
                $generaler_ider[] = $general;
                $generaler[] = Beboer::medId($general);
            }
        }
        if ($res != null) {
            return new self($res['aar'], $res['start_dato'], $res['slutt_dato'], $generaler, $res['tema'], $res['klar'], $res['max_gjest'], $res['epost_tekst']);
        } else {
            return null;
        }
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
        foreach ($this->generaler as $general) {
            if ($general->getId() == $beboer_id) {
                return true;
            }
        }
        return false;
    }

}