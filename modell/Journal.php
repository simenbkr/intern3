<?php

namespace intern3;

class Journal
{

    private $ukenr;
    private $fra;
    private $til;
    private $år;

//private $alle_kryss;
    private $ukeskryss;

    public function __construct($ukenr = Null, $år = Null)
    {
        //Default-uke er denne uken, men det er åpent for å lage journal-objekt med andre uker også.
        if (!isset($ukenr)) {
            $this->ukenr = date('w');
            $this->fra = date('Y-m-d', strtotime('-' . (self::getUkenr() - 1) . ' days'));
            $this->til = date('Y-m-d', strtotime('+' . (6 - self::getUkenr()) . ' days'));
            //$this->alle_kryss = self::getAlleKryss();
        } else {
            $this->år = (isset($år) ? $år : date('Y'));
            $this->ukenr = $ukenr;

            $fraogtil = self::getStartSluttDato($this->ukenr, $this->år);
            $this->fra = $fraogtil[0];
            $this->til = $fraogtil[1];

        }
    }

    public static function getStartSluttDato($uke, $år)
    {
        $return = array();
        $now = strtotime("1 January $år", time());
        $dag = date('w', $now);
        $now += ((7 * ($uke-1)) + 1 - $dag) * 24 * 3600;
        $return[0] = date('Y-m-d', $now);
        $now += 6 * 24 * 3600;
        $return[1] = date('Y-m-d', $now);

        return $return;
    }

    public function getUkenr()
    {
        return $this->ukenr;
    }

    public function getPeriode()
    {
        return array(
            'fra' => $this->fra,
            'til' => $this->til
        );
    }

    public function getAlleKryss()
    {
        $st = DB::getDB()->prepare('SELECT * from krysseliste');
        $st->execute();
        $krysse_array = array();

        while ($rad = $st->fetch()) {
            $krysse_array[] = $rad['krysseliste'];
        }
        return $krysse_array;
    }

    public function getUkeKryss()
    {
        $resultatsArray = [];
        $periode = self::getPeriode();
        //TODO fjern test-array!
        /*$periode = array(
            'fra' => date('2015-08-17'),
            'til' => date('2015-08-24')
        );*/

        $alle_kryss = self::getAlleKryss();

        if (count($alle_kryss) > 0) {
            foreach ($alle_kryss as $krysseliste) {
                $krysseliste = json_decode($krysseliste, true);

                foreach ($krysseliste as $enkelt_kryssing) {
                    $tid = date($enkelt_kryssing['tid']);
                    if ($tid <= $periode['til'] && $tid >= $periode['fra']) {
                        $resultatsArray[] = $enkelt_kryssing;
                    }
                }
            }
        }
        return $resultatsArray;
    }

    public function getVaktHavende($vaktnr, $dato)
    {
        //Returnerer et beboer-objekt med vakthavende.
        $st = DB::getDB()->prepare('SELECT * from vakt WHERE vakttype=:vakttype AND dato=:dato');
        $st->bindParam(':vakttype', $vaktnr);
        $st->bindParam(':dato', $dato);
        $st->execute();
        $vaktrad = $st->fetchColumn();
        $beboer = Beboer::medBrukerId($vaktrad['bruker_id']);
        return $beboer != null ? $beboer : Ansatt::medBrukerId($vaktrad['bruker_id']);
    }


    public function getKrysseInfo()
    {
        $info_arr = array();
        $perioden = self::getPeriode();
        $start = date('Y-m-d', strtotime('-1 week', strtotime($perioden['fra'])));
        $slutt = date('Y-m-d', strtotime('-1 week', strtotime($perioden['til'])));

        //Henter ut siste kryssing/vaktbytte/overføring fra forrige periode.
        $st0 = DB::getDB()->prepare('SELECT * FROM alt_journal WHERE dato>=:start AND dato <=:slutt ORDER BY dato DESC');
        $st0->bindParam(':start', $start);
        $st0->bindParam(':slutt', $slutt);
        $st0->execute();
        //$forrige = $st->fetchAll()[0];

        //Henter ut hele journalen for nåværende uke.
        $st = DB::getDB()->prepare('SELECT * FROM alt_journal WHERE dato>=:start AND dato<=:slutt ORDER BY dato ASC');
        $st->bindParam(':start', $perioden['fra']);
        $st->bindParam(':slutt', $perioden['til']);
        $st->execute();

        //$journalen = $st->fetchAll();

        $endelig_array = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {

            $aktuell_vaktsesjon = AltJournal::init($st);
            if ($aktuell_vaktsesjon != null) {
                if($aktuell_vaktsesjon->getBruker() != null) {
                    $denne_vakta = array(
                        'vakthavende' => $aktuell_vaktsesjon->getBruker()->getPerson(),
                        'vaktnr' => $aktuell_vaktsesjon->getVaktnr(),
                        'dato' => $aktuell_vaktsesjon->getDato()
                    );
                } else {

                    $vakthavende = Vakt::medDatoVakttype($aktuell_vaktsesjon->getDato(), $aktuell_vaktsesjon->getVaktnr());
                    if($vakthavende != null && $vakthavende->getBruker() != null){
                        $vakthavende = $vakthavende->getBruker()->getPerson();
                    }

                    $denne_vakta = array(
                        'vakthavende' => $vakthavende,
                        'vaktnr' => $aktuell_vaktsesjon->getVaktnr(),
                        'dato' => $aktuell_vaktsesjon->getDato()
                    );
                }

                foreach ($aktuell_vaktsesjon->getStatusAsArray() as $drikke_objekt) {
                    $denne_vakta['obj'][Drikke::medId($drikke_objekt['drikkeId'])->getNavn()] = $drikke_objekt;
                    $denne_vakta['obj'][Drikke::medId($drikke_objekt['drikkeId'])->getNavn()]['svinn'] =
                        $drikke_objekt['mottatt'] + $drikke_objekt['pafyll'] - $drikke_objekt['utavskap'] - $drikke_objekt['avlevert'];
                }
                $endelig_array[] = $denne_vakta;
            }
        }
        return $endelig_array;
    }
    
}

?>