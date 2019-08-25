<?php

/* Obs: krysseliste er json fra db, mens KryssListe er en liste av Kryss. */

// Sånn her må man gjøre for å få nøsta klasser:

namespace intern3\Krysseliste;

class Kryss
{
    public $tid;
    public $fakturert;
    public $antall;

    public function __construct($tid, $antall, $fakturert)
    {
        $this->tid = $tid;
        $this->fakturert = $fakturert;
        $this->antall = $antall;
    }
}

namespace intern3;

use intern3\Krysseliste\Kryss;

class Krysseliste
{

    private $id;
    private $beboerId;
    private $drikkeId;
    public $krysseliste;

    // Latskap
    private $kryssListe = null;
    private $beboer = null;
    private $drikke = null;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM krysseliste WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medBeboerId($beboerId)
    {
        // Burde egentlig vært i KrysselisteListe som ennå ikke finnes.
        $st = DB::getDB()->prepare('SELECT id FROM krysseliste WHERE beboer_id=:beboerId;');
        $st->bindParam(':beboerId', $beboerId);
        $st->execute();
        $krysselisteListe = array();
        while ($rad = $st->fetch()) {
            $krysselisteListe[] = self::medId($rad['id']);
        }
        return $krysselisteListe;
    }

    public static function medBeboerDrikkeId($beboerId, $drikkeId)
    {
        $st = DB::getDB()->prepare('SELECT * FROM krysseliste WHERE beboer_id=:beboerId AND drikke_id=:drikkeId;');
        $st->bindParam(':beboerId', $beboerId);
        $st->bindParam(':drikkeId', $drikkeId);
        $st->execute();
        $instance = self::init($st);
        if ($instance == null) {
            $instance = new self();
            $instance->id = 0;
            $instance->beboerId = $beboerId;
            $instance->drikkeId = $drikkeId;
            $instance->krysseliste = '[]';
        }
        return $instance;
    }

    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        $instance = new self();
        if ($rad == null) {
            return null;
        }
        $instance->id = $rad['id'];
        $instance->beboerId = $rad['beboer_id'];
        $instance->drikkeId = $rad['drikke_id'];
        $instance->krysseliste = $rad['krysseliste'];
        return $instance;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBeboerId()
    {
        return $this->beboerId;
    }

    public function getBeboer()
    {
        if ($this->beboer == null) {
            $this->beboer = Beboer::medId($this->beboerId);
        }
        return $this->beboer;
    }

    public function getDrikkeId()
    {
        return $this->drikkeId;
    }

    public function getDrikke()
    {
        if ($this->drikke == null) {
            $this->drikke = Drikke::medId($this->drikkeId);
        }
        return $this->drikke;
    }

    public function getKryssListe()
    {
        if ($this->kryssListe == null) {
            $this->kryssListe = array();
            $jsonArray = json_decode($this->krysseliste);
            if (!is_array($jsonArray)) {
                $jsonArray = array();
            }
            foreach ($jsonArray as $strukt) {
                $this->kryssListe[] = new Krysseliste\Kryss(
                    $strukt->tid,
                    $strukt->antall,
                    $strukt->fakturert
                );
            }
        }
        return $this->kryssListe;
    }

    public static function getAllIkkeFakturert()
    {
        $beboerListe = BeboerListe::aktive();
        $drikke = Drikke::alle();
        $krysseListeListe = array();
        foreach ($beboerListe as $beboer) {
            $Helekrysseliste = self::medBeboerId($beboer->getId());
            /*$kryss = array('Pant' => 0,
                'Øl' => 0,
                'Cider' => 0,
                'Carlsberg' => 0,
                'Rikdom' => 0
            );*/
            $kryss = array();
            foreach ($drikke as $drikken) {
                $kryss[$drikken->getNavn()] = 0;
            }
            foreach ($Helekrysseliste as $delKryseListe) {
                $KryssDrikka = json_decode($delKryseListe->krysseliste, true);

                foreach ($KryssDrikka as $enkelt_kryss) {
                    if ($enkelt_kryss['fakturert'] == 0) {
                        $kryss[Drikke::medId($delKryseListe->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                    }
                }
            }
            $krysseListeListe[$beboer->getId()] = $kryss;
        }
        return $krysseListeListe;
    }

    public static function getAlleKryssetEtterDato($dato)
    {
        $beboerListe = BeboerListe::aktive();
        $krysseListeListe = array();
        $drikke = Drikke::alle();
        foreach ($beboerListe as $beboer) {
            $Helekrysseliste = self::medBeboerId($beboer->getId());
            $kryss = array();
            foreach ($drikke as $drikken) {
                $kryss[$drikken->getNavn()] = 0;
            }
            foreach ($Helekrysseliste as $delKryseListe) {
                $KryssDrikka = json_decode($delKryseListe->krysseliste, true);

                foreach ($KryssDrikka as $enkelt_kryss) {
                    if ($enkelt_kryss['tid'] > $dato) {
                        $kryss[Drikke::medId($delKryseListe->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                    }
                }

            }
            $krysseListeListe[$beboer->getId()] = $kryss;
        }
        return $krysseListeListe;
    }


    public static function getAllIkkeFakturertBeboer($beboerId)
    {
        $Helekrysseliste = self::medBeboerId($beboerId);
        $drikke = Drikke::alle();
        $kryss = array();
        foreach ($drikke as $drikken) {
            $kryss[$drikken->getNavn()] = 0;
        }
        foreach ($Helekrysseliste as $delKryseListe) {
            $KryssDrikka = json_decode($delKryseListe->krysseliste, true);

            foreach ($KryssDrikka as $enkelt_kryss) {
                if ($enkelt_kryss['fakturert'] == 0) {
                    $kryss[Drikke::medId($delKryseListe->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                }
            }

        }
        return $kryss;
    }


    public static function getAllFakturert()
    {
        $beboerListe = BeboerListe::aktive();
        $krysseListeListe = array();
        $drikke = Drikke::alle();
        foreach ($beboerListe as $beboer) {
            $Helekrysseliste = self::medBeboerId($beboer->getId());
            $kryss = array();
            foreach ($drikke as $drikken) {
                $kryss[$drikken->getNavn()] = 0;
            }
            foreach ($Helekrysseliste as $delKryseListe) {
                $KryssDrikka = json_decode($delKryseListe->krysseliste, true);

                foreach ($KryssDrikka as $enkelt_kryss) {
                    if ($enkelt_kryss['fakturert'] != 0) {
                        $kryss[Drikke::medId($delKryseListe->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                    }
                }

            }
            $krysseListeListe[$beboer->getId()] = $kryss;
        }
        return $krysseListeListe;
    }

    public static function getKryssByMonth($beboer_id, $dato = null)
    {
        $drikke = Drikke::alle();
        $id = $beboer_id;
        $st = DB::getDB()->prepare('SELECT * from krysseliste WHERE beboer_id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        $krysserader = $st->fetchAll();

        $datoen = (isset($dato)) ? $dato : date('Y-m');

        $first = date('Y-m-01', strtotime($datoen));
        $last = date('Y-m-t', strtotime($datoen));

        $kryss = array();
        foreach ($drikke as $drikken) {
            $kryss[$drikken->getNavn()] = 0;
        }

        foreach ($krysserader as $krysseliste) {
            $krysseliste2 = json_decode($krysseliste['krysseliste'], true);

            foreach ($krysseliste2 as $enkelt_kryss) {
                $tiden = date($enkelt_kryss['tid']);
                if ($tiden >= $first && $tiden <= $last) {
                    $kryss[Drikke::medId($krysseliste2->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                }
            }
        }
        return $kryss;
    }

    public static function getKryssbyDay($beboer_id, $dato = null)
    {
        $drikke = Drikke::alle();
        $datoen = isset($dato) ? date('Y-m-d', strtotime($dato)) : date('Y-m-d', strtotime('now'));
        $id = $beboer_id;
        $st = DB::getDB()->prepare('SELECT * from krysseliste WHERE beboer_id=:id');

        $st->bindParam(':id', $id);
        $st->execute();

        $krysserader = $st->fetchAll();

        $kryss = array();
        foreach ($drikke as $drikken) {
            $kryss[$drikken->getNavn()] = 0;
        }

        foreach ($krysserader as $krysseliste) {
            $krysseliste2 = json_decode($krysseliste['krysseliste'], true);
            foreach ($krysseliste2 as $enkelt_kryss) {
                $tiden = date('Y-m-d', strtotime($enkelt_kryss['tid']));
                if ($tiden == $datoen) {
                    $kryss[Drikke::medId($krysseliste2->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                }
            }
        }
        return $kryss;
    }

    public static function getAlleKryssByBeboerByDay($beboer_id, $dato)
    {
        //TODO fix this shit.
        $drikke_rader = self::medBeboerId($beboer_id);
    }

    public static function getAlleKryssByDay($dato = null)
    {
        //TODO fix this shit.
        $datoen = isset($dato) ? date('Y-m-d', strtotime($dato)) : date('Y-m-d', strtotime('now'));
        $alleKryss = array();

    }

    public static function ufakturertTilCSVoppTilDato($tildato)
    {
        $beboerListe = BeboerListe::aktive();
        $drikke = Drikke::alle();
        $out = array();
        $header = array("Fullt navn");

        foreach($drikke as $drikken) {
            $header[] = $drikken->getNavn();
        }

        $out[0] = $header;

        foreach ($beboerListe as $beboer) {
            /* @var Beboer $beboer */
            if (!$beboer->harAlkoholdepositum()) {
                continue;
            }

            $beboers_krysseliste = self::medBeboerId($beboer->getId());
            $kryss = array();
            foreach($drikke as $drikken) {
                $kryss[$drikken->getNavn()] = 0;
            }

            foreach($beboers_krysseliste as $delkrysseliste) {
                $dekodet_delkrysseliste = json_decode($delkrysseliste->krysseliste, true);

                foreach($dekodet_delkrysseliste as $enkelt_kryss) {
                    if(strtotime($enkelt_kryss->tid) <= strtotime($tildato) && $enkelt_kryss->fakturert == 0) {
                        $kryss[Drikke::medId($delkrysseliste->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                    }
                }

            }
            $beb_arr = array($beboer->getFulltNavn());
            foreach($kryss as $res) {
                $beb_arr[] = $res;
            }
            $out[] = $beb_arr;
        }

        return $out;
    }


    public static function periodeTilCSV()
    {
        $beboerListe = BeboerListe::aktive();
        $drikke = Drikke::alle();
        $out = array();
        $header = array("Fullt navn");

        foreach($drikke as $drikken) {
            $header[] = $drikken->getNavn();
        }

        $out[0] = $header;

        foreach ($beboerListe as $beboer) {
            /* @var Beboer $beboer */
            if (!$beboer->harAlkoholdepositum()) {
                continue;
            }

            $beboers_krysseliste = self::medBeboerId($beboer->getId());
            $kryss = array();
            foreach($drikke as $drikken) {
                $kryss[$drikken->getNavn()] = 0;
            }

            foreach($beboers_krysseliste as $delkrysseliste) {
                $dekodet_delkrysseliste = json_decode($delkrysseliste->krysseliste, true);

                foreach($dekodet_delkrysseliste as $enkelt_kryss) {
                    if($enkelt_kryss['fakturert'] == 0) {
                        $kryss[Drikke::medId($delkrysseliste->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                    }
                }

            }
            $beb_arr = array($beboer->getFulltNavn());
            foreach($kryss as $res) {
                $beb_arr[] = $res;
            }
            $out[] = $beb_arr;
        }

        return $out;
    }


    public static function setPeriodeFakturert()
    {

        $beboerListe = BeboerListe::aktive();

        foreach ($beboerListe as $beboer) {
            if (!$beboer->harAlkoholdepositum()) {
                continue;
            }
            $beboers_krysseliste = self::medBeboerId($beboer->getId());

            foreach ($beboers_krysseliste as $drikke_kryss) {
                $drikke_kryss->getKryssListe();
                foreach ($drikke_kryss->kryssListe as $enkelt_kryss) {
                    if ($enkelt_kryss->fakturert == 0) {
                        $enkelt_kryss->fakturert = 1;
                    }

                }
                $drikke_kryss->oppdater();
            }

        }
        self::oppdaterFakturert();
    }

    public static function fakturerOppTil($dato)
    {

        $beboerListe = BeboerListe::aktive();

        foreach ($beboerListe as $beboer) {
            if (!$beboer->harAlkoholdepositum()) {
                continue;
            }
            $beboers_krysseliste = self::medBeboerId($beboer->getId());

            foreach ($beboers_krysseliste as $drikke_kryss) {
                /* @var Kryss $drikke_kryss */
                $drikke_kryss->getKryssListe();
                foreach ($drikke_kryss->kryssListe as $enkelt_kryss) {
                    if (strtotime($enkelt_kryss->tid) <= strtotime($dato) && $enkelt_kryss->fakturert == 0) {
                        $enkelt_kryss->fakturert = 1;
                    }

                }
                $drikke_kryss->oppdater();
            }

        }


        self::oppdaterFakturertDato($dato);
    }

    public static function getSistFakturert()
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert ORDER BY dato DESC LIMIT 1');
        $st->execute();
        $resultat = $st->fetchAll()[0];
        return $resultat['dato'];
    }

    private static function oppdaterFakturert()
    {
        $st = DB::getDB()->prepare('INSERT INTO fakturert (dato) VALUES (NOW())');
        $st->execute();
    }

    private static function oppdaterFakturertDato($dato)
    {
        $datoen = date('Y-m-d H:i:s', strtotime($dato));
        $st = DB::getDB()->prepare('INSERT INTO fakturert (dato) VALUES (:dato)');
        $st->bindParam(':dato', $datoen);
        $st->execute();
    }


    public function setFakturert($start, $slutt)
    {
        $this->getKryssListe();
        $start = date('Y-m-d', strtotime($start));
        $slutt = date('Y-m-d', strtotime($slutt));
        foreach ($this->kryssListe as $kryss) {
            if ($kryss->tid >= $start && $kryss->tid <= $slutt) {
                $kryss->fakturert = 1;
            }
        }
        $this->oppdater();
    }

    public function addKryss($antall, $unixTid = false, $fakturert = false)
    {
        if ($unixTid === false) {
            $unixTid = $_SERVER['REQUEST_TIME'];
        }
        $this->settInnKryss(new Krysseliste\Kryss(
            date('Y-m-d H:i:s', $unixTid),
            $antall,
            $fakturert ? 1 : 0
        ));
    }

    private function settInnKryss($kryss)
    {
        $this->getKryssListe();
        $this->kryssListe[] = $kryss;
    }

    public function getJson()
    {
        return json_encode($this->kryssListe);
    }

    public function oppdater()
    {
        $this->krysseliste = $this->getJson();
        if ($this->id == 0) {
            return $this->opprett();
        }
        $st = DB::getDB()->prepare('UPDATE krysseliste SET krysseliste=:krysseliste
WHERE beboer_id=:beboerId AND drikke_id=:drikkeId;');
        $st->bindParam(':beboerId', $this->beboerId);
        $st->bindParam(':drikkeId', $this->drikkeId);
        $st->bindParam(':krysseliste', $this->krysseliste);
        $st->execute();
    }

    private function opprett()
    {
        $st = DB::getDB()->prepare('INSERT INTO krysseliste(
	beboer_id,drikke_id,krysseliste
) VALUES(
	:beboerId,:drikkeId,:krysseliste
);');
        $st->bindParam(':beboerId', $this->beboerId);
        $st->bindParam(':drikkeId', $this->drikkeId);
        $st->bindParam(':krysseliste', $this->krysseliste);
        $st->execute();
        $this->id = DB::getDB()->lastInsertId();
    }


    public static function getKryssByPeriode($start, $slutt)
    {
        //Må iterere over samtlige objekter pga elendig datastruktur. Takk'a.
        //Terminerer på sånn 1sek lokalt, tipper det er tigangen på tjenern. Hvorfor er det json i dette igjen?
        //Fuck databasenormalisering eller hva? :( :(
        $beboerListe = BeboerListe::alle();
        $drikke = Drikke::alle();

        $beboerKrysselisteListe = array();
        foreach ($beboerListe as $beboer) {
            /* @var Beboer $beboer */

            $beboers_kryssesum = array();

            foreach ($drikke as $drikken) {
                $beboers_kryssesum[$drikken->getNavn()] = 0;
            }
            foreach (self::medBeboerId($beboer->getId()) as $krysseListe) {
                /* @var Krysseliste $krysseListe */
                $kryssDrikka = json_decode($krysseListe->krysseliste, true);

                foreach ($kryssDrikka as $kryss) {
                    //Jeg vet, tre loops?! Ahahaha fuck this shit man

                    if (strtotime($kryss['tid']) > strtotime($start) && strtotime($kryss['tid']) < strtotime($slutt)) {
                        //Satser på at ingen krysser når faktureringen pågår. AKA yolo.
                        $beboers_kryssesum[$krysseListe->getDrikke()->getNavn()] += $kryss['antall'];

                    }
                }
            }
            $beboerKrysselisteListe[$beboer->getId()] = $beboers_kryssesum;
        }

        $siste_lista = array();
        foreach ($beboerKrysselisteListe as $key => $lista) {
            if (array_sum($lista) > 0) {
                $siste_lista[$key] = $lista;
            }
        }
        return $siste_lista;
    }

    public static function getAlleKryssPeriode($startdato, $sluttdato)
    {
        $beboerListe = BeboerListe::aktive();
        $krysseListeListe = array();
        $drikke = Drikke::alle();
        foreach ($beboerListe as $beboer) {
            $Helekrysseliste = self::medBeboerId($beboer->getId());
            $kryss = array();
            foreach ($drikke as $drikken) {
                $kryss[$drikken->getNavn()] = 0;
            }
            foreach ($Helekrysseliste as $delKryseListe) {
                $KryssDrikka = json_decode($delKryseListe->krysseliste, true);

                foreach ($KryssDrikka as $enkelt_kryss) {
                    if ($enkelt_kryss['tid'] > $startdato && $enkelt_kryss['tid'] < $sluttdato) {
                        $kryss[Drikke::medId($delKryseListe->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                    }
                }

            }
            $krysseListeListe[$beboer->getId()] = $kryss;
        }
        return $krysseListeListe;
    }

    public static function getAlleKryssPeriodeBeboer($startdato, $sluttdato, $beboer_id)
    {

        $drikke = Drikke::alle();
        $Helekrysseliste = self::medBeboerId($beboer_id);
        $kryss = array();

        foreach ($drikke as $drikken) {
            $kryss[$drikken->getNavn()] = 0;
        }

        foreach ($Helekrysseliste as $delKryseListe) {
            $KryssDrikka = json_decode($delKryseListe->krysseliste, true);

            foreach ($KryssDrikka as $enkelt_kryss) {
                if ($enkelt_kryss['tid'] > $startdato && $enkelt_kryss['tid'] < $sluttdato) {
                    $kryss[Drikke::medId($delKryseListe->drikkeId)->getNavn()] += $enkelt_kryss['antall'];
                }
            }

        }

        return $kryss;
    }

    public static function periodeSummary($periode_array) {

        $result = array();

        foreach($periode_array as $drikke_array) {
            foreach($drikke_array as $drikke_navn => $antall) {
                if(array_key_exists($drikke_navn, $result)) {
                    $result[$drikke_navn] += $antall;
                } else {
                    $result[$drikke_navn] = $antall;
                }
            }
        }

        return $result;
    }


    public static function getTotalKryssByDate($start, $slutt) {

        $unix_start = strtotime($start);
        $unix_end = strtotime($slutt);

        $day_to_result = array(); //array(2019-02-03 => array(Pant => 42, Øl => 102))
        $template = array();
        $drikker = array();

        foreach(Drikke::alle() as $d) {
            $drikker[$d->getId()] = $d->getNavn();
            $template[$d->getNavn()] = 0;
        }

        $krysselista = array();
        foreach(BeboerListe::aktiveMedAlko() as $beboer) {
            $krysselista = array_merge($krysselista, self::medBeboerId($beboer->getId()));
        }


        foreach($krysselista as $drikke_kryss) {
            /* @var \intern3\Krysseliste $drikke_kryss */

            foreach($drikke_kryss->getKryssListe() as $enkelt_kryss) {
                $unix = strtotime($enkelt_kryss->tid);
                $date = date('Y-m-d', $unix);

                if($unix >= $unix_end || $unix <= $unix_start) {
                    continue;
                }

                if(array_key_exists($date, $day_to_result)) {
                    $day_to_result[$date][$drikke_kryss->getDrikke()->getNavn()] += $enkelt_kryss->antall;
                } else {
                    $day_to_result[$date] = $template;
                    $day_to_result[$date][$drikke_kryss->getDrikke()->getNavn()] += $enkelt_kryss->antall;
                }
            }
        }

        return $day_to_result;

    }

    public static function getKryssSistPeriode()
    {
        $st = DB::getDB()->prepare('SELECT * from fakturert ORDER BY id DESC LIMIT 2');
        $start = $st->fetch()['dato'];
        $slutt = $st->fetch()['dato'];
        return self::getAlleKryssPeriode($start, $slutt);
    }

}