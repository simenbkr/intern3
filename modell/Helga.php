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

    const DAGER = array('torsdag', 'fredag', 'lordag');


    public static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->aar = $rad['aar'];

        if ($rad['start_dato'] != null) {
            $instance->start_dato = date('Y-m-d', strtotime($rad['start_dato']));
            $instance->slutt_dato = date('Y-m-d', strtotime($rad['start_dato'] . ' + 2 days'));
        } else {
            $instance->start_dato = null;
            $instance->slutt_dato = null;
        }
        if ($rad['generaler'] != null) {
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

    public static function medAar($aar)
    {

        $st = DB::getDB()->prepare('SELECT * FROM helga WHERE aar=:aar ORDER BY aar DESC LIMIT 1');
        $st->bindParam(':aar', $aar);
        $st->execute();

        return self::init($st);
    }

    public function getStartDato()
    {
        return $this->start_dato;
    }

    public function setStartDato($start)
    {
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

    public function getGeneraler()
    {
        $generalene = array();
        if (count($this->generaler) > 0) {
            foreach ($this->generaler as $id) {
                $beboeren = Beboer::medId($id);
                if ($beboeren != null) {
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
        $st = DB::getDB()->prepare('SELECT COUNT(*) as cnt FROM helgagjest WHERE (aar = :year AND dag = :dag)');
        $st->execute(['year' => $this->aar, 'dag' => '0']);
        $antall_per_dag['torsdag'] = $st->fetch()['cnt'];

        $st->execute(['year' => $this->aar, 'dag' => '1']);
        $antall_per_dag['fredag'] = $st->fetch()['cnt'];

        $st->execute(['year' => $this->aar, 'dag' => '2']);
        $antall_per_dag['lordag'] = $st->fetch()['cnt'];

        return $antall_per_dag;
    }

    public function getAntallInnePerDag()
    {
        $st = DB::getDB()->prepare('SELECT COUNT(*) as cnt FROM helgagjest WHERE (aar = :year AND dag = :dag AND inne = 1)');
        $st->execute(['year' => $this->aar, 'dag' => '0']);
        $antall_per_dag['torsdag'] = $st->fetch()['cnt'];

        $st->execute(['year' => $this->aar, 'dag' => '1']);
        $antall_per_dag['fredag'] = $st->fetch()['cnt'];

        $st->execute(['year' => $this->aar, 'dag' => '2']);
        $antall_per_dag['lordag'] = $st->fetch()['cnt'];

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

    public function setKlar()
    {
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

    public function setTema($tema)
    {
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

    public function setMaxTorsdag(int $antall)
    {
        $this->max_gjester['torsdag'] = $antall;
        $this->oppdater();
    }

    public function setMaxFredag(int $antall)
    {
        $this->max_gjester['fredag'] = $antall;
        $this->oppdater();
    }

    public function setMaxLordag(int $antall)
    {
        $this->max_gjester['lordag'] = $antall;
        $this->oppdater();
    }

    public function getMaxAlle()
    {
        return $this->max_alle;
    }

    public function setMaxAlle(int $antall)
    {
        $this->max_alle = $antall;
        $this->oppdater();
    }

    public function erSameMax()
    {
        return $this->same == 1;
    }

    public function setSameMax($value)
    {
        if ($value == 'on') {
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

        for ($i = 0; $i < $st->rowCount(); $i++) {
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

        if ($st->rowCount() > 0) {
            return self::init($st);
        }
        return null;
    }

    public static function getHelgaByAar($aar)
    {
        $st = DB::getDB()->prepare('SELECT * from helga WHERE aar=:aar');
        $st->bindParam(':aar', $aar);
        $st->execute();

        if ($st->rowCount() > 0) {
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

    public function getMaxGjest($beboer_id, $dag)
    {

        $st = DB::getDB()->prepare('SELECT * FROM helga_gjestantall WHERE (beboer_id = :beboer_id AND aar=:aar)');
        $st->execute(['beboer_id' => $beboer_id, 'aar' => $this->getAar()]);

        if ($st->rowCount() > 0) {
            return $st->fetch()[self::DAGER[$dag]];
        }

        if ($this->erSameMax()) {
            return $this->getMaxAlle();
        }

        return $this->getMaxGjester()[self::DAGER[$dag]];

    }

    public function setMaxGjest($beboer_id, $torsdag, $fredag, $lordag)
    {
        $st = DB::getDB()->prepare('SELECT count(*) as cnt FROM helga_gjestantall WHERE (beboer_id = :beboer_id AND aar = :aar)');
        $st->execute(['beboer_id' => $beboer_id, 'aar' => $this->getAar()]);
        $count = $st->fetch()['cnt'];

        if($count < 1) {
            $arr = array('torsdag' => $torsdag, 'fredag' => $fredag, 'lordag' => $lordag);
            foreach($arr as $str => $val) {
                if($this->erSameMax()) {
                    if($val < $this->getMaxAlle()) {
                        $val = $this->getMaxAlle();
                    }
                } else {
                    if($val < $this->getMaxGjester()[$str]) {
                        $val = $this->getMaxGjester()[$str];
                    }
                }

                $arr[$str] = $val;
            }

            $st = DB::getDB()->prepare('INSERT INTO helga_gjestantall (aar, beboer_id, torsdag, fredag, lordag) VALUES(:aar, :beboer_id, :torsdag, :fredag, :lordag)');
            $st->execute(array_merge(array('beboer_id' => $beboer_id, 'aar' => $this->getAar()), $arr));
        } else {
            $st = DB::getDB()->prepare('UPDATE helga_gjestantall SET torsdag = :torsdag, fredag = :fredag, lordag = :lordag WHERE (beboer_id = :beboer_id AND aar = :aar)');
            $st->execute([
                'torsdag' => $torsdag,
                'fredag' => $fredag,
                'lordag' => $lordag,
                'beboer_id' => $beboer_id,
                'aar' => $this->getAar()
            ]);
        }
    }

    public function medEgendefinertAntall() {

        $array = array();

        $st = DB::getDB()->prepare('SELECT * FROM helga_gjestantall WHERE aar = :aar');
        $st->execute(['aar' => $this->getAar()]);

        while(($rad = $st->fetch())) {

            $array[$rad['beboer_id']] = array(
                'beboer' => Beboer::medId($rad['beboer_id']),
                'torsdag' => $rad['torsdag'],
                'fredag' => $rad['fredag'],
                'lordag' => $rad['lordag']
            );

        }

        return $array;
    }

    public function harEgendefinert($beboer_id) : bool {
        $st = DB::getDB()->prepare('SELECT COUNT(*) as cnt FROM helga_gjestantall WHERE beboer_id = :beboer_id');
        $st->execute(['beboer_id' => $beboer_id]);
        return $st->fetch()['cnt'] > 0;
    }

    public function slettEgendefinert($beboer_id) {
        $st = DB::getDB()->prepare('DELETE FROM helga_gjestantall WHERE (aar = :aar AND beboer_id = :beboer_id)');
        $st->execute(['aar' => $this->getAar(), 'beboer_id' => $beboer_id]);

    }

    public function kanLeggeTil($beboer_id, $dag) {
        return HelgaGjesteListe::getGjesteCountDagBeboer($dag, $beboer_id, $this->aar) < $this->getMaxGjest($beboer_id, $dag);
    }
}