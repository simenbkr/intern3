<?php

namespace intern3;

class Oppgave
{
    private $id;
    private $tid_oppretta;
    private $anslag_timer;
    private $ansvarsomrade_id; // trengs bare hvis oppgaven ikke tilhører noen rapport
    private $anslag_personer;
    private $prioritet_id;
    private $navn;
    private $beskrivelse;
    private $godkjent;
    private $tid_godkjent;
    private $godkjent_bruker_id;
    private $status;
    private $tid_utfore;
    private $tid_ferdig;

    private $db;

    //latskapsinstansiering
    private $pameldte;
    private $arbeidListe; //arbeidListe er flertallsformen av arbeid
    private $arbeidListeBrukerId;

    public function __construct()
    {
        $this->db = DB::getDB();
    }

    private function init($pdo_statement)
    {
        $st = $pdo_statement;
        $st->execute();
        if ($st->rowCount() > 0) {
            $rad = $st->fetch();

            $this->id = $rad['id'];
            $this->tid_oppretta = $rad['tid_oppretta'];
            $this->anslag_timer = $rad['anslag_timer'];
            $this->ansvarsomrade_id = $rad['ansvarsomrade_id'];
            $this->anslag_personer = $rad['anslag_personer'];
            $this->prioritet_id = $rad['prioritet_id'];
            $this->navn = $rad['navn'];
            $this->beskrivelse = $rad['beskrivelse'];
            $this->godkjent = $rad['godkjent'];
            $this->tid_godkjent = $rad['tid_godkjent'];
            $this->godkjent_bruker_id = $rad['godkjent_bruker_id'];
            $this->pameldte = $rad['paameldte'];
            $this->status = $rad['status'];
            $this->tid_utfore = $rad['tid_utfore'];
            $this->tid_ferdig = $rad['tid_ferdig'];

            $this->brukere = null;
        }
    }

    private static function init_ny(\PDOStatement $st)
    {
        $st->execute();
        $rad = $st->fetch();

        if ($rad === null) {
            return null;
        }
        $instance = new self();

        $instance->id = $rad['id'];
        $instance->tid_oppretta = $rad['tid_oppretta'];
        $instance->anslag_timer = $rad['anslag_timer'];
        $instance->ansvarsomrade_id = $rad['ansvarsomrade_id'];
        $instance->anslag_personer = $rad['anslag_personer'];
        $instance->prioritet_id = $rad['prioritet_id'];
        $instance->navn = $rad['navn'];
        $instance->beskrivelse = $rad['beskrivelse'];
        $instance->godkjent = $rad['godkjent'];
        $instance->tid_godkjent = $rad['tid_godkjent'];
        $instance->godkjent_bruker_id = $rad['godkjent_bruker_id'];
        $instance->pameldte = $rad['paameldte'];
        $instance->status = $rad['status'];
        $instance->tid_utfore = $rad['tid_utfore'];
        $instance->tid_ferdig = $rad['tid_ferdig'];

        $instance->brukere = null;

        return $instance;
    }

    private function oppdater()
    {

        $st = DB::getDB()->prepare('UPDATE oppgave SET navn=:navn, tid_utfore=:tidutfore, tid_ferdig=:tidferdig, anslag_timer=:timer,
                                            anslag_personer=:personer, beskrivelse=:besk WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':tidutfore', $this->tid_utfore);
        $st->bindParam(':tidferdig', $this->tid_ferdig);
        $st->bindParam(':timer', $this->anslag_timer);
        $st->bindParam(':personer', $this->anslag_personer);
        $st->bindParam(':besk', $this->beskrivelse);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }


    public static function medId($id)
    {
        $instans = new self();
        $st = $instans->db->prepare('SELECT * FROM oppgave WHERE id = :id');
        $st->bindParam(":id", $id);

        $instans->init($st);
        return $instans;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function erFryst()
    {
        return $this->status == 1;
    }

    public function setFryst()
    {
        $this->status = 1;
        $this->setStatus(1);
    }

    public function unFrys()
    {
        $this->status = 0;
        $this->setStatus(0);
    }

    public function getTidOppretta()
    {
        return $this->tid_oppretta;
    }

    public function getAnslagTimer()
    {
        return $this->anslag_timer;
    }

    public function getAnsvarsomradeId()
    {
        return $this->ansvarsomrade_id;
    }

    public function getAnslagPersoner()
    {
        return $this->anslag_personer;
    }

    public function getNavn()
    {
        return $this->navn;
    }

    public function getBeskrivelse()
    {
        return $this->beskrivelse;
    }

    public function getGodkjent()
    {
        return $this->godkjent;
    }

    public function getTidGodkjent()
    {
        return $this->tid_godkjent;
    }

    public function getGodkjentBrukerId()
    {
        return $this->godkjent_bruker_id;
    }

    public function getGodkjentBruker()
    {
        if ($this->godkjent_bruker_id != null) {
            return Bruker::medId($this->godkjent_bruker_id);
        }
        return null;
    }

    public function getPrioritet()
    {
        return Prioritet::medId($this->getPrioritetId());
    }

    public function getTidFerdig()
    {
        return $this->tid_ferdig;
    }

    public function getTidUtfore()
    {
        return $this->tid_utfore;
    }

    public function getTidTekst()
    {

        if (is_null($this->tid_utfore)) {
            return 'Ikke satt.';
        }

        if (is_null($this->tid_ferdig)) {
            return $this->tid_utfore;
        }

        if (date('Y-m-d', strtotime($this->tid_utfore)) === date('Y-m-d', strtotime($this->tid_ferdig))) {
            return date('Y-m-d', strtotime($this->tid_utfore)) . ': ' .
                date('H:i', strtotime($this->tid_utfore)) . ' - ' . date('H:i', strtotime($this->tid_ferdig));
        }

        return $this->tid_utfore . " - " . $this->tid_ferdig;

    }

    public function setStatus($status)
    {
        $this->status = $status;
        DB::getDB()->query('UPDATE oppgave SET status=' . $status . ' WHERE id=' . $this->id);
    }


    public function setNavn($navn)
    {
        $this->navn = $navn;
        $this->oppdater();
    }

    public function setBeskrivelse($beskrivelse)
    {
        $this->beskrivelse = $beskrivelse;
        $this->oppdater();
    }

    public function setTidutfore($tid_utfore)
    {
        if (empty($tid_utfore) || is_null($tid_utfore)) {
            return;
        }

        $this->tid_utfore = $tid_utfore;
        $this->oppdater();
    }

    public function setTidFerdig($tid_ferdig)
    {
        if (empty($tid_utfore) || is_null($tid_utfore)) {
            return;
        }

        $this->tid_ferdig = $tid_ferdig;
        $this->oppdater();
    }

    public function setAnslagTimer($anslag_timer)
    {
        $this->anslag_timer = $anslag_timer;
        $this->oppdater();
    }

    public function setAnslagPersoner($anslag_personer)
    {
        $this->anslag_personer = $anslag_personer;
        $this->oppdater();
    }

    public static function setGodkjent($oppgaver_id, $godkjent)
    {
        $instans = new self(); //BUG: Får ikke tak i $this->db, så hva skal vi med $this->db ?
        foreach ($oppgaver_id as $oppgave_id) {
            $st = $instans->db->prepare('UPDATE oppgave SET godkjent=:godkjent,tid_godkjent=NOW(),godkjent_bruker_id=:bruker_id WHERE godkjent=' . (1 ^ $godkjent) . ' AND id = :rapport_id;');
            //Faen ta PDO: Kan ikke skrive "WHERE godkjent=:motsatt".

            $st->bindParam(':godkjent', $godkjent);
            $st->bindParam(':rapport_id', $rapport_id);
            $st->bindParam(":bruker_id", $_SESSION['bruker_id']);
            $st->execute();
        }
    }

    public function setBrukerPameldt($bruker_id, $pameldt)
    {
        $instans = new self();
        $sql = ($pameldt) ?
            'INSERT INTO oppgave_bruker(bruker_id,oppgave_id) VALUES(:brk,:oppg)'
            :
            'DELETE FROM oppgave_bruker WHERE bruker_id=:brk AND oppgave_id=:oppg';
        $st = $instans->db->prepare($sql);
        $st->bindParam(':brk', $bruker_id);
        $st->bindParam(':oppg', $this->id);
        $st->execute();
    }

    public static function endreGodkjent($oppgaveId, $status)
    {
        if ($status == 0) {
            $st = DB::getDB()->prepare('UPDATE oppgave SET godkjent=0,tid_godkjent=NULL,godkjent_bruker_id=NULL WHERE id=:id');
            $st->bindParam(':id', $oppgaveId);
            $st->execute();
        } elseif ($status == 1) {
            $st = DB::getDB()->prepare('UPDATE oppgave SET godkjent=1,tid_godkjent=NOW(),godkjent_bruker_id=:brukerId WHERE id=:id');
            $st->bindParam(':id', $oppgaveId);
            $aktivbrukerId = Session::get('brid');
            $st->bindParam(':brukerId', $aktivbrukerId);
            $st->execute();
        }
    }

    public static function AddOppgave($navn, $pri, $anslagtid, $anslagpers, $beskrivelse, $tid_utfore = null, $tid_ferdig = null)
    {

        if (!is_null($tid_utfore)) {
            //Formatér til DATETIME-format som DBen krever.
            $tid_utfore = date('Y-m-d H:i:s', strtotime($tid_utfore));

            if (!is_null($tid_ferdig)) {
                $tid_ferdig = date('Y-m-d H:i:s', strtotime($tid_ferdig));
            }

            $st = DB::getDB()->prepare('INSERT INTO oppgave 
(tid_oppretta,anslag_timer,anslag_personer,prioritet_id,navn,beskrivelse,godkjent,tid_godkjent,godkjent_bruker_id, status, tid_utfore, tid_ferdig)
        VALUES(NOW(),:anslagtimer,:anslagpersoner,:pri,:navn,:beskrivelse,0,NULL,NULL,0, :tid_utfore,:tid_ferdig)');

            $st->bindParam(':tid_utfore', $tid_utfore);
            $st->bindParam(':tid_ferdig', $tid_ferdig);

        } else {

            $st = DB::getDB()->prepare('INSERT INTO oppgave 
(tid_oppretta,anslag_timer,anslag_personer,prioritet_id,navn,beskrivelse,godkjent,tid_godkjent,godkjent_bruker_id, status, tid_utfore)
        VALUES(NOW(),:anslagtimer,:anslagpersoner,:pri,:navn,:beskrivelse,0,NULL,NULL,0, NULL)');

        }

        $st->bindParam(':anslagtimer', $anslagtid);
        $st->bindParam(':anslagpersoner', $anslagpers);
        $st->bindParam(':pri', $pri);
        $st->bindParam(':navn', $navn);
        $st->bindParam(':beskrivelse', $beskrivelse);
        $st->execute();
    }


    /*
     * Funksjonene nedenfor instansierer variabler ved behov.
     */
    public function getPameldteId()
    {
        return json_decode($this->pameldte, true);
    }

    public function getPameldteBeboere()
    {
        $beboerne = array();
        if ($this->getPameldteId() != null) {
            foreach ($this->getPameldteId() as $id) {
                $beboerne[] = Beboer::medId($id);
            }
        }
        return $beboerne;
    }

    public function getArbeidListe()
    {
        if (!$this->arbeidListe) {
            // lazyinit
            $this->arbeidListe = ArbeidListe::medOppgaveId($this->id);
        }
        return $this->arbeidListe;
    }

    public function getArbeidListeBrukerId($bruker_id)
    {
        if (!$this->arbeidListeBrukerId) {
            // lazyinit
            $this->arbeidListeBrukerId = ArbeidListe::medBrukerIdOppgIdVelg($bruker_id, $this->id, ArbeidPolymorfkategori::OPPG);
        }
        return $this->arbeidListeBrukerId;
    }

    public function erBrukerPameldt($bruker_id)
    {
        $this->getPameldte();
        foreach ($this->pameldte as $bruker) {
            if ($bruker->getId() == $bruker_id) {
                return true;
            }
        }
        return false;
    }

    public function getPrioritetId()
    {
        /*if (!$this->prioritet_id) {
            // lazyinit
            $pri = array();
            foreach (RapportListe::medOppgaveId($this->id) as $Rapport) {
                $pri[$Rapport->getPrioritetId()] = $Rapport->getPrioritet() . getNummer();
            }
            arsort($pri, SORT_NUMERIC);
            $pri = array_keys($pri);
            $this->prioritet_id = $pri[0];
        }
        return $this->prioritet_id;*/
        return $this->prioritet_id == null || $this->prioritet_id == 0 ? 1 : $this->prioritet_id;
    }

    public function fjernPerson($beboer_id)
    {
        $nye_paameldte = array();

        foreach ($this->getPameldteId() as $id) {
            if ($id != $beboer_id) {
                $nye_paameldte[] = $id;
            }
        }
        $nye_paameldte = count($nye_paameldte) != 0 ? json_encode($nye_paameldte) : '';
        $st = DB::getDB()->prepare('UPDATE oppgave SET paameldte=:paameldte WHERE id=:id');
        $st->bindParam(':paameldte', $nye_paameldte);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function meldPa($beboer_id)
    {
        $pameldte = $this->getPameldteId();

        if ($pameldte === null || count($pameldte) < 1) {
            $pameldte = array();
        }

        if (in_array($beboer_id, $pameldte)) {
            return;
        }

        $pameldte[] = $beboer_id;
        $nypameldte = json_encode($pameldte, true);

        $st = DB::getDB()->prepare('UPDATE oppgave SET paameldte=:paameldte WHERE id=:id');
        $st->bindParam(':paameldte', $nypameldte);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public function settLag($beboer_id_liste)
    {
        $pameldte = json_encode($beboer_id_liste);
        $st = DB::getDB()->prepare('UPDATE oppgave SET paameldte=:paameldte WHERE id=:id');
        $st->bindParam(':paameldte', $pameldte);
        $st->bindParam(':id', $this->id);
        $st->execute();

    }

    public static function getSiste()
    {
        $st = DB::getDB()->prepare('SELECT * FROM oppgave ORDER BY id DESC LIMIT 1');
        $st->execute();
        return self::init_ny($st);
    }

    public static function getOppgaverByUtforelseDato($dato)
    {
        $st = DB::getDB()->prepare('SELECT * FROM oppgave WHERE tid_utfore=:dato');
        $st->bindParam(':dato', $dato);
        $st->execute();

        if ($st->rowCount() == 0) {
            return null;
        }

        $oppgaver = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {

            $oppgaver[] = self::init_ny($st);
        }

        return $oppgaver;

    }

    public static function getOppgaverISemester($dato = null)
    {
        $oppgaver = array();

        if ($dato === null) {
            $dato = date('Y-m-d');
        }

        $semester = Funk::generateSemesterString($dato);
        $start = Funk::getSemesterStart($semester);
        $slutt = Funk::getSemesterEnd($semester);

        $st = DB::getDB()->prepare('SELECT * FROM oppgave WHERE (tid_oppretta>:start AND tid_oppretta<:slutt)');
        $st->bindParam(':start', $start);
        $st->bindParam(':slutt', $slutt);
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $oppgaver[] = self::init_ny($st);
        }

        return $oppgaver;

    }

    public static function getOppgaverISemesterBeboerId($beboer_id, $dato = null)
    {
        $oppgaver = array();

        if ($dato === null) {
            $dato = date('Y-m-d');
        }

        $semester = Funk::generateSemesterString($dato);
        $start = Funk::getSemesterStart($semester);
        $slutt = Funk::getSemesterEnd($semester);
        $paameldte = '%' . $beboer_id . '%';

        $st = DB::getDB()->prepare('SELECT * FROM oppgave WHERE (tid_oppretta>:start AND tid_oppretta<:slutt AND paameldte LIKE :paameldte)');
        $st->bindParam(':start', $start);
        $st->bindParam(':slutt', $slutt);
        $st->bindParam(':paameldte', $paameldte);
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $oppgaver[] = self::init_ny($st);
        }

        return $oppgaver;

    }

}

?>
