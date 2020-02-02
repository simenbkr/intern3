<?php

namespace intern3;

class Regivakt
{
    private int $id;
    private string $dato;
    private string $start_tid;
    private string $slutt_tid;
    private ?array $bruker_ider;
    private string $beskrivelse;
    private int $status;
    private string $nokkelord;
    private int $antall;
    private bool $bytte;

    const STATUS_TEKST = array(0 => 'Planlagt', 1 => 'Ferdig', 2 => 'Godkjent', 3 => 'Underkjent');

    private $brukere;

    private static function init(\PDOStatement $st): ?Regivakt
    {

        if (is_null(($rad = $st->fetch()))) {
            return null;
        }

        $instance = new self();
        $instance->id = intval($rad['id']);
        $instance->dato = $rad['dato'];
        $instance->start_tid = $rad['start_tid'];
        $instance->slutt_tid = $rad['slutt_tid'];
        $instance->bruker_ider = json_decode($rad['bruker_ider']);
        $instance->nokkelord = $rad['nokkelord'];
        $instance->beskrivelse = $rad['beskrivelse'];
        $instance->status = intval($rad['status']);
        $instance->antall = intval($rad['antall']);
        $instance->bytte = (intval($rad['bytte']) == 1);
        $instance->brukere = array();

        return $instance;
    }

    public static function medId($id): ?Regivakt
    {
        $st = DB::getDB()->prepare('SELECT * FROM regivakt WHERE id = :id');
        $st->execute(['id' => $id]);
        return self::init($st);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDato()
    {
        return $this->dato;
    }

    public function setDato($dato)
    {
        $this->dato = $dato;
    }

    public function getStartTid()
    {
        return $this->start_tid;
    }

    public function setStartTid($start_tid)
    {
        $this->start_tid = $start_tid;
    }

    public function getSluttTid()
    {
        return $this->slutt_tid;
    }

    public function setSluttTid($slutt_tid)
    {
        $this->slutt_tid = $slutt_tid;
    }

    public function getNokkelord(): string
    {
        return $this->nokkelord;
    }

    public function setNokkelord($nokkelord)
    {
        $this->nokkelord = $nokkelord;
    }

    public function getBrukerIder()
    {
        return $this->bruker_ider;
    }

    public function getBrukere()
    {
        $brukere = array();
        foreach ($this->bruker_ider as $id) {
            $brukere[] = Bruker::medId($id);
        }

        return $brukere;
    }

    public function getAntallPameldte(): int
    {
        return count($this->bruker_ider);
    }

    public function getBeskrivelse(): string
    {
        return $this->beskrivelse;
    }

    public function setBeskrivelse($beskrivelse)
    {
        $this->beskrivelse = $beskrivelse;
    }

    public function getStatusInt(): int
    {
        return $this->status;
    }

    public function getStatus(): string
    {
        return self::STATUS_TEKST[$this->status];
    }

    public function setStatusInt($status_int)
    {
        if (is_null(intval($status_int))) {
            return;
        }

        $this->status = intval($status_int);
    }

    public function getAntall(): int
    {
        return $this->antall;
    }

    public function harPlass(): bool
    {
        return $this->antall > count($this->bruker_ider);
    }

    public function setBytte(bool $bytte)
    {
        $this->bytte = $bytte;
    }

    public function byttes(): bool
    {
        return $this->bytte;
    }

    public function lagre()
    {
        $st = DB::getDB()->prepare('UPDATE regivakt SET dato = :dato,
start_tid = :start,
slutt_tid = :slutt,
beskrivelse = :beskrivelse,
nokkelord = :nokkelord,
antall = :antall,
status = :status,
bytte = :bytte
WHERE id = :id');
        $st->execute([
            'dato' => $this->dato,
            'start' => $this->start_tid,
            'slutt' => $this->slutt_tid,
            'beskrivelse' => $this->beskrivelse,
            'nokkelord' => $this->nokkelord,
            'antall' => $this->antall,
            'status' => $this->status,
            'bytte' => ($this->bytte ? 1 : 0),
            'id' => $this->id
        ]);
    }


    public function addBrukerId($bruker_id)
    {
        $ider = $this->bruker_ider;
        $ider[] = (string) $bruker_id;
        $this->bruker_ider = $ider;
        $ider = json_encode($ider, true);
        $st = DB::getDB()->prepare('UPDATE regivakt SET bruker_ider = :bider WHERE id = :id');
        $st->execute([
            'bider' => $ider,
            'id' => $this->id
        ]);
    }

    public function removeBrukerId($bruker_id)
    {
        $nye_ider = array();

        foreach ($this->bruker_ider as $id) {
            if ($id != $bruker_id) {
                $nye_ider[] = $id;
            }
        }

        $this->bruker_ider = $nye_ider;
        $nye_ider = json_encode($nye_ider, true);
        if (count($this->bruker_ider) === 0) {
            $nye_ider = '';
        }

        $st = DB::getDB()->prepare('UPDATE regivakt SET bruker_ider = :bider WHERE id = :id');
        $st->execute(['bider' => $nye_ider, 'id' => $this->id]);

    }

    public static function ny($dato, $start_tid, $slutt_tid, $beskrivelse, $nokkelord, $antall = 1)
    {
        $st = DB::getDB()->prepare('
INSERT INTO regivakt(dato, start_tid, slutt_tid, beskrivelse,nokkelord, antall) 
VALUES(:dato, :start, :slutt, :beskrivelse,:nokkelord, :antall)');

        $st->execute([
            'dato' => $dato,
            'start' => $start_tid,
            'slutt' => $slutt_tid,
            'beskrivelse' => $beskrivelse,
            'nokkelord' => $nokkelord,
            'antall' => $antall
        ]);

        $st = DB::getDB()->prepare('SELECT * from regivakt ORDER BY id DESC LIMIT 1');
        $st->execute();
        return self::init($st);
    }

    public static function listeMedDato($dato): array
    {
        $res = array();

        $st = DB::getDB()->prepare('SELECT * FROM regivakt WHERE dato = :dato');
        $st->execute(['dato' => $dato]);

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $res[] = self::init($st);
        }

        return $res;
    }

    public function slett()
    {

        $st = DB::getDB()->prepare('DELETE FROM regivakt WHERE id = :id');
        $st->execute(['id' => $this->id]);

        $st = DB::getDB()->prepare('DELETE FROM regivakt_bytte WHERE regivakt_id = :id');
        $st->execute(['id' => $this->id]);

    }

    public function godkjenn()
    {

        $tid_brukt = strtotime($this->slutt_tid) - strtotime($this->start_tid);
        $kommentar = "Regivakt: {$this->getNokkelord()}. Beskrivelse: {$this->beskrivelse}.";
        $dato = $this->dato;
        $tid_registrert = date('Y-m-d H:i:s');
        $tid_godkjent = $tid_registrert;
        $godkjent_bruker_id = Session::getAktivBruker()->getId();

        foreach ($this->getBrukere() as $bruker) {
            /* @var Bruker $bruker */

            $st = DB::getDB()->prepare('INSERT INTO 
arbeid(bruker_id, polymorfkategori_id, polymorfkategori_velger, tid_registrert, sekunder_brukt, tid_utfort, kommentar, godkjent, tid_godkjent, godkjent_bruker_id)
VALUES(:bid, 1, 0, :tid_reg, :sek_brukt, :tid_utfort, :kommentar, 1, :tid_godkjent, :godkjenner)');

            $st->execute([
                'bid' => $bruker->getId(),
                'tid_reg' => $tid_registrert,
                'sek_brukt' => $tid_brukt,
                'tid_utfort' => $dato,
                'kommentar' => $kommentar,
                'tid_godkjent' => $tid_godkjent,
                'godkjenner' => $godkjent_bruker_id
            ]);
        }

        $this->setStatusInt(2);
        $this->lagre();
    }

    public function underkjenn()
    {
        $this->status = 3;
        $this->lagre();

        $tid_brukt = strtotime($this->slutt_tid) - strtotime($this->start_tid);
        $kommentar = "Regivakt: {$this->getNokkelord()}. Beskrivelse: {$this->beskrivelse}.";

        $st = DB::getDB()->prepare('DELETE FROM arbeid WHERE 
(sekunder_brukt = :sek_brukt AND kommentar = :kommentar AND bruker_id = :bruker_id)');

        foreach ($this->getBrukere() as $bruker) {
            $st->execute([
                'bruker_id' => $bruker->getId(),
                'sek_brukt' => $tid_brukt,
                'kommentar' => $kommentar,
            ]);
        }

    }

    public static function regivakterDetteSemesteretBruker($bruker_id)
    {

        $start = Funk::getSemesterStart(Funk::generateSemesterString(date('Y-m-d')));
        $slutt = Funk::getSemesterEnd(Funk::generateSemesterString(date('Y-m-d')));

        $st = DB::getDB()->prepare('SELECT * FROM regivakt WHERE (dato >= :start AND dato <= :slutt AND bruker_ider LIKE :bid)');
        $st->execute([
            'start' => $start,
            'slutt' => $slutt,
            'bid' => "%\"{$bruker_id}\"%"
        ]);

        $vakter = array();
        for ($i = 0; $i < $st->rowCount(); $i++) {
            $vakter[] = self::init($st);
        }

        return $vakter;
    }

    public function toString()
    {
        $df = new \IntlDateFormatter('nb_NO',
            \IntlDateFormatter::TRADITIONAL, \IntlDateFormatter::NONE,
            'Europe/Oslo');
        return $df->format(strtotime($this->getDato()));
    }

    public function shortToString()
    {
        $df = new \IntlDateFormatter('nb_NO',
            \IntlDateFormatter::FULL, \IntlDateFormatter::NONE,
            'Europe/Oslo');

        return ucfirst($df->format(strtotime($this->dato)));
    }

    public function medToString()
    {
        $df = new \IntlDateFormatter('nb_NO',
            \IntlDateFormatter::FULL, \IntlDateFormatter::NONE,
            'Europe/Oslo');

        return ucfirst($df->format(strtotime($this->dato)));
    }

}