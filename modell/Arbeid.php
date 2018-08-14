<?php

namespace intern3;

/*
 * Forskjell på arbeid og oppgave_bruker:
 *
 * oppgave_bruker lagrer hvem som er påmeldt en oppgave akkurat nå.
 * arbeid lagrer kumulativt registrerte timer.
 * Dermed:
 * Det er mulig å melde seg av en oppgave uten å miste registrert arbeid.
 * En bruker kan føre timer på en oppgave (og få godkjent) i flere omganger.
 *
 * I tillegg til oppgave.godkjent, finnes arbeid.godkjent.
 * Dermed kan arbeid kodkjennes individuelt. Løser problemet med at ingen
 * kan få godkjent før alle påmeldte har registrert (kan ta evig lenge).
 *
 * oppgave_bruker.oppgave_id ⋃ oppgave_bruker.bruker_id er unik.
 * arbeid.bruker_id ⋃ arbeid.oppgave_id er ikke unik.
 */

class Arbeid
{
    private $id;
    private $bruker_id;
    private $polymorfkategori_id;
    private $polymorfkategori_velger;
    private $tid_registrert;
    private $sekunder_brukt;
    private $tid_utfort;
    private $kommentar;
    private $godkjent;
    private $tid_godkjent;
    private $godkjent_bruker_id;
    private $tilbakemelding;
    private $arbeidbilder;

    private $db;

    //latskapsinstansiering
    private $bruker;
    private $polymorfKategori;

    public function __construct()
    {
        $this->db = DB::getDB();
    }

    private function init($st)
    {
        $st->execute();
        if ($st->rowCount() > 0) {
            $rad = $st->fetch();

            $this->id = $rad['id'];
            $this->bruker_id = $rad['bruker_id'];
            $this->polymorfkategori_id = $rad['polymorfkategori_id'];
            $this->polymorfkategori_velger = $rad['polymorfkategori_velger'];
            $this->tid_registrert = $rad['tid_registrert'];
            $this->sekunder_brukt = $rad['sekunder_brukt'];
            $this->tid_utfort = $rad['tid_utfort'];
            $this->kommentar = $rad['kommentar'];
            $this->godkjent = $rad['godkjent'];
            $this->tid_godkjent = $rad['tid_godkjent'];
            $this->godkjent_bruker_id = $rad['godkjent_bruker_id'];
            $this->tilbakemelding = $rad['tilbakemelding'];
            $this->arbeidbilder = ArbeidBilde::medArbeidId($this->id);

            $this->bruker = null;
        }
    }

    public static function medId($id)
    {
        $instans = new self();
        $st = $instans->db->prepare('SELECT * FROM arbeid WHERE id=:id');
        $st->bindParam(':id', $id);

        $instans->init($st);
        return $instans;
    }

    public static function medBrukerIdOppgIdVelg($brkId, $oppgId, $velg)
    {
        $instans = new self();
        $st = $instans->db->prepare('SELECT * FROM arbeid WHERE bruker_id=:brk AND polymorfkategori_id=:oppg AND polymorfkategori_velger=:vlg');
        $st->bindParam(':brk', $brkId);
        $st->bindParam(':oppg', $oppgId);
        $st->bindParam(':vlg', $velg);

        $instans->init($st);
        return $instans;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrukerId()
    {
        return $this->bruker_id;
    }

    public function getOppgaveId()
    {
        if ($this->polymorfkategori_velger != ArbeidPolymorfkategori::OPPG) {
            return NULL;
        }
        return $this->polymorfkategori_id;
    }

    public function getFeilId()
    {
        if ($this->polymorfkategori_velger != ArbeidPolymorfkategori::FEIL) {
            return NULL;
        }
        return $this->polymorfkategori_id;
    }

    public function getArbeidskategoriId()
    {
        if ($this->polymorfkategori_velger != ArbeidPolymorfkategori::YMSE) {
            return NULL;
        }
        return $this->polymorfkategori_id;
    }

    public function getTidRegistrert()
    {
        return $this->tid_registrert;
    }

    public function getSekunderBrukt()
    {
        return $this->sekunder_brukt;
    }

    public function getTidUtfort()
    {
        return $this->tid_utfort;
    }

    public function getKommentar()
    {
        return $this->kommentar;
    }

    public function getGodkjent()
    {
        return $this->godkjent == 1;
    }

    public function getTidGodkjent()
    {
        return $this->tid_godkjent;
    }

    public function getGodkjentBrukerId()
    {
        return $this->godkjent_bruker_id;
    }

    public function getTilbakemelding()
    {
        return $this->tilbakemelding;
    }

    public function getStatus()
    {
        if ($this->godkjent == 0) {
            return 'Ubehandla';
        } elseif ($this->godkjent == 1) {
            return 'Godkjent';
        }
        return 'Underkjent';
    }

    public function getIntStatus(){
        return $this->godkjent;
    }

    public function getGodkjentBruker(){
        return Bruker::medId($this->godkjent_bruker_id);
    }

    public static function mkArbeid(
        $bruker_id,
        $polymorfkategori_id,
        $polymorfkategori_velger,
        $sek,
        $tid,
        $kommentar
    )
    {
        if ($kommentar == '') $kommentar = NULL;

        $sql = 'INSERT INTO arbeid(bruker_id,polymorfkategori_id,polymorfkategori_velger,sekunder_brukt,tid_utfort,kommentar) VALUES(:brk,:id,:vlg,:sek,:tid,:kom)';
        $instans = new self();
        $st = $instans->db->prepare($sql);
        $st->bindParam(':brk', $bruker_id);
        $st->bindParam(':id', $polymorfkategori_id);
        $st->bindParam(':vlg', $polymorfkategori_velger);
        $st->bindParam(':sek', $sek);
        $st->bindParam(':tid', $tid);
        $st->bindParam(':kom', htmlspecialchars($kommentar));

        $st->execute();
    }

    public function getTidBrukt()
    {
        $min = round($this->sekunder_brukt / 60);
        $tim = floor($min / 60);
        $min %= 60;
        if ($min > 0) {
            $min = str_repeat('0', 2 - strlen($min)) . $min;
            return $tim . ':' . $min;
        } else {
            return $tim;
        }
    }

    /*
     * Funksjonene nedenfor instansierer variabler ved behov.
     */
    public function getBruker()
    {
        if (!$this->bruker) {
            // lazyinit
            $this->bruker = Bruker::medId($this->bruker_id);
        }
        return $this->bruker;
    }

    public function getPolymorfKategori()
    {
        if (!$this->polymorfKategori) {
            // lazyinit
            switch ($this->polymorfkategori_velger) {
                case ArbeidPolymorfkategori::YMSE:
                    $this->polymorfKategori = Arbeidskategori::medId($this->polymorfkategori_id);
                    break;
                case ArbeidPolymorfkategori::FEIL:
                    $this->polymorfKategori = Feil::medId($this->polymorfkategori_id);
                    break;
                case ArbeidPolymorfkategori::RAPP:
                    $this->polymorfKategori = Rapport::medId($this->polymorfkategori_id);
                    break;
                case ArbeidPolymorfkategori::OPPG:
                    $this->polymorfKategori = Oppgave::medId($this->polymorfkategori_id);
                    break;
            }
        }
        return $this->polymorfKategori;
    }

    public static function finsPolymorfKategoriId($polymorfKategori, $id)
    {
        $instans = new self();
        $st = $instans->db->prepare('SELECT COUNT(*) FROM arbeid WHERE polymorfkategori_velger = :type AND polymorfkategori_id = :id');
        $st->bindParam(":type", $polymorfKategori);
        $st->bindParam(":id", $id);
        $st->execute();

        return $st->fetchColumn() > 0;
    }

    public static function getTimerBruktPerSemester($dato = null)
    {
        $dato = isset($dato) ? $dato : date('Y-m-d');
        $year = date('Y', strtotime($dato));
        if (strtotime($dato) > strtotime("01-01-$year") && strtotime($dato) < strtotime("01-07-$year")) {
            //Vår-semesteret
            $start = "$year-01-01";
            $slutt = "$year-07-01";
        } else {
            $start = "$year-07-01";
            $slutt = "$year-12-31";
        }
        //$st = DB::getDB()->prepare('SELECT bruker_id, sekunder_brukt FROM arbeid WHERE (tid_utfort>:start AND tid_utfort<=:slutt AND godkjent=1)');
        $st = DB::getDB()->prepare('SELECT bruker_id, sum(sekunder_brukt) AS tot FROM arbeid 
WHERE (tid_utfort>:start AND tid_utfort<=:slutt AND godkjent=1)
GROUP BY bruker_id');

        $st->bindParam(':start', $start);
        $st->bindParam(':slutt', $slutt);
        $st->execute();
        $radene = $st->fetchAll();
        $totalt_sek_utfort = 0;
        $beboerListe = BeboerListe::aktive();

        /*foreach ($radene as $rad) {
            //172800s = 48 timer.
            if( ($tid_brukt = $rad['sekunder_brukt']) > 172800){
                $tid_brukt = 172800;
            }
            $totalt_sek_utfort += $tid_brukt;
        } */

        foreach($radene as $rad){
            /* @var \intern3\Beboer $beboer */
            $max = Bruker::medId($rad['bruker_id'])->getPerson()->getRolle()->getRegitimer();

            if($rad['tot'] > ($max_s = $max * 60 * 60) ){
                $totalt_sek_utfort += $max_s;
            } else {
                $totalt_sek_utfort += $rad['tot'];
            }
        }

        $timer = Funk::tidTilTimer($totalt_sek_utfort);
        $maks_timer = 0;

        foreach (BeboerListe::aktiveMedRegi() as $beboer) {
            $rollen = $beboer->getRolle();
            if ($rollen != null) {
                $maks_timer += $rollen->getRegiTimer();
            }
        }

        $resterende_timer = Funk::tidTilTimer($maks_timer * 60 * 60 - $totalt_sek_utfort);

        return array($timer, $maks_timer, $resterende_timer);
    }

    public function inCurrentSem(){

        $now = Funk::semStrToUnix(Funk::generateSemesterString(date('Y-m-d')));
        $done = Funk::semStrToUnix(Funk::generateSemesterString(date('Y-m-d',strtotime($this->tid_utfort))));

        return $now === $done;
    }
    
    public function getArbeidBilder(){
        return $this->arbeidbilder;
    }
}

?>
