<?php

namespace intern3;


class Storhybelliste
{
    private $id;
    private $navn;
    private $aktiv;
    private $semester;
    private $ledige_rom;
    private $rekkefolge;
    private $velgerNr;
    private $velger;
    private $neste;
    private $fordeling;

    private static function init(\PDOStatement $st)
    {

        $rad = $st->fetch();
        if (!$rad) {
            return null;
        }

        $instans = new self();

        $instans->id = $rad['id'];
        $instans->semester = $rad['semester'];
        $instans->navn = $rad['navn'];
        $instans->aktiv = $rad['aktiv'];
        $instans->ledige_rom = RomListe::fraStorhybelListe($instans->id);
        $instans->rekkefolge = StorhybelVelger::medStorhybel($instans->id);
        $instans->velgerNr = $rad['velger'];
        $instans->velger = $instans->velgerFraNr($rad['velger']);
        $instans->neste = $instans->velgerFraNr($instans->velgerNr + 1);
        $instans->fordeling = StorhybelFordeling::medStorhybelId($instans->id);

        return $instans;
    }

    public static function medId($id)
    {

        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();

        return self::init($st);

    }

    public static function latest(): Storhybelliste
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel ORDER BY id DESC LIMIT 1');
        return self::init($st);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function erAktiv(): bool
    {
        return $this->aktiv === '1';
    }

    public function erFerdig(): bool
    {
        return $this->aktiv === '-1';
    }

    public function erInaktiv(): bool
    {
        return $this->aktiv === '0';
    }

    public function getStatusTekst(): string
    {
        if ($this->aktiv === '1') {
            return "Aktiv";
        }

        if ($this->aktiv === '0') {
            return "Inaktiv";
        }

        if ($this->aktiv === '2') {
            return "Arkivert";
        }

        return "Ferdig";
    }

    public function getSemester(): string
    {
        return $this->semester;
    }

    public function getNavn(): string
    {
        return $this->navn;
    }

    public function getLedigeRom(): array
    {
        return $this->ledige_rom;
    }

    public function getNeste() //Return type ?Beboer.
    {
        return $this->neste;
    }

    public function getVelger(): ?StorhybelVelger
    {
        return $this->velger;
    }

    public function getVelgerNr()
    {
        return $this->velgerNr;
    }

    public function setNavn(string $navn)
    {
        $this->navn = $navn;
    }

    public function setSemester(string $semester)
    {
        $this->semester = $semester;
    }

    public function setLedigeRom(array $ledige_rom)
    {
        $this->ledige_rom = $ledige_rom;
    }

    public function setRekkefolge(array $rekkefolge)
    {
        $this->rekkefolge = $rekkefolge;
    }

    public function setNeste(Beboer $beboer)
    {
        $this->neste = $beboer;
    }

    public function getRekkefolge(): array
    {
        return $this->rekkefolge;
    }

    public function getFordeling()
    {
        return $this->fordeling;
    }

    public function aktiver()
    {
        $this->velgerNr = 1;
        $this->aktiv = 1;
        $this->lagreIntern();
    }

    public function deaktiver()
    {
        $this->aktiv = 0;
        $this->lagreIntern();
    }

    public function neste()
    {
        $this->velgerNr++;

        if ($this->velgerNr > count($this->rekkefolge)) {
            $this->velgerNr = 1;
        }

        $this->velger = $this->velgerFraNr($this->velgerNr);
        $this->neste = $this->velgerFraNr($this->velgerNr + 1);
        $this->lagreIntern();

        $tittel = "[SING-INTERN] Det er din tur på Storhybellisten!";
        $body = "<html><body>Hei, <br/><br/>Det er din tur på Storhybellisten til å velge. Du har 24t på å velge.<br/><br/>Med vennlig hilsen<br/>Internsida</body></html>";

        foreach ($this->getVelger()->getBeboere() as $beboer) {
            /* @var Beboer $beboer */
            Epost::sendEpost($beboer->getEpost(), $tittel, $body);
        }

    }

    public function forrige()
    {
        $this->velgerNr--;

        if ($this->velgerNr < 1) {
            $this->velgerNr = count($this->rekkefolge);
        }

        $this->velger = $this->velgerFraNr($this->velgerNr);
        $this->neste = $this->velgerFraNr($this->velgerNr + 1);
        $this->lagreIntern();
    }

    public function omgjor($velger_id)
    {

        $fordeling = StorhybelFordeling::medVelgerIdStorhybelId($velger_id, $this->id);
        $velger = StorhybelVelger::medVelgerId($velger_id);

        $rommet = $fordeling->getNyttRom();
        if (!is_null($rommet)) {
            $this->leggtilRom($rommet);
            $fordeling->setNyttRomId(null);
        }
        $this->velger = $velger;
        $this->velgerNr = $velger->getNummer();
        $this->neste = $this->velgerFraNr($velger->getNummer() + 1);
        $this->lagreIntern();
    }

    /*
     * Sjekker om input-rom er et gyldig rom å ha på lista.
     * Storhybellista kan bare ha bøttekott, storhybler og LPer.
     * SP-lister kan bare ha SPer, og
     * Korrhybellista kan bare ha korrhybler.
     */
    public function gyldigRom(Rom $rom): bool
    {

        $rel_navn = explode(' ', $this->navn)[0];

        if ($this->romValgt($rom)) {
            return false;
        }

        if (strpos($rel_navn, str_replace(' ', '', $rom->getType()->getNavn())) !== false) {
            return true;
        } elseif ($rel_navn == 'Storhybelliste' && in_array($rom->getType()->getNavn(),
                array('Bøttekott', 'Storhybel', 'Liten Parhybel'))) {
            return true;
        }
        return false;
    }

    public function romValgt(Rom $rom): bool
    {
        foreach ($this->fordeling as $fordeling) {
            /* @var StorhybelFordeling $fordeling */

            if ($fordeling->getNyttRomId() == $rom->getId()) {
                return true;
            }

        }

        return false;
    }

    private function lagre()
    {

        if ($this->id !== null) {
            /*
             * Oppdater variabler for denne klassen.
             */

            $this->lagreIntern();

            /*
             * Oppdater romliste. Først må den gamle slettes.
             */

            $this->slettRomliste();
            /*
             * Deretter setter vi inn.
             */

            $this->lagreRomliste();

            /*
             * Oppdatere rekkefølgen på samme måte.
             */

            $this->slettRekkefolge();


            $this->lagreRekkefolge();
        } else {

            /*
             * Her må vi opprette en ny database entry.
             */

            $st = DB::getDB()->prepare('INSERT INTO storhybel (navn, semester, velger) VALUES(:navn, :semester,1)');
            $st->bindParam(':navn', $this->navn);
            $st->bindParam(':semester', $this->semester);
            $st->execute();

            $this->id = DB::getDB()->query('SELECT id FROM storhybel ORDER BY id DESC LIMIT 1')->fetch()['id'];

            $this->lagreRomliste();
            $this->lagreRekkefolge();

            $st = DB::getDB()->prepare('INSERT INTO storhybel_fordeling (storhybel_id,velger_id,gammel_rom_id) VALUES(:sid,:vid,:rid)');
            $st->bindParam(':sid', $this->id);

            foreach ($this->rekkefolge as $velger) {
                /* @var StorhybelVelger $velger */

                foreach ($velger->getBeboere() as $beboer) {
                    /* @var Beboer $beboer */
                    $st->bindParam(':vid', $velger->getVelgerId());
                    $st->bindParam(':rid', $beboer->getRom()->getId());
                    $st->execute();
                }

                $velger->setStorhybel($this->id);

            }

        }

        return $this;

    }

    private function lagreIntern()
    {
        $st = DB::getDB()->prepare('UPDATE storhybel SET navn=:navn,semester=:semester,aktiv=:aktiv,velger=:velger WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':semester', $this->semester);
        $st->bindParam(':aktiv', $this->aktiv);
        $st->bindParam(':velger', $this->velgerNr);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    private function slettRomliste()
    {
        $st = DB::getDB()->prepare('DELETE FROM storhybel_rom WHERE storhybel_id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();

    }

    private function lagreRomliste()
    {
        $st = DB::getDB()->prepare('INSERT INTO storhybel_rom (storhybel_id, rom_id) VALUES(:sid, :rid)');
        $st->bindParam(':sid', $this->id);
        foreach ($this->ledige_rom as $rom) {
            /* @var \intern3\Rom $rom */
            $st->bindParam(':rid', $rom->getId());
            $st->execute();
        }
    }

    private function slettRekkefolge()
    {
        $st = DB::getDB()->prepare('DELETE FROM storhybel_rekkefolge WHERE storhybel_id=:id');
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    private function lagreRekkefolge()
    {
        $nummer = 1;
        $st = DB::getDB()->prepare('INSERT INTO storhybel_rekkefolge (storhybel_id, velger_id, nummer) VALUES (:sid, :vid, :nr)');
        $st->bindParam(':sid', $this->id);

        foreach ($this->rekkefolge as $velger) {
            /* @var StorhybelVelger $velger */

            $st->bindParam(':vid', $velger->getVelgerId());
            $st->bindParam(':nr', $nummer);
            $st->execute();

            $nummer++;

        }
    }

    public function nummerVelger(int $velger_id): int
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND velger_id=:vid)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':vid', $velger_id);
        $st->execute();

        $rad = $st->fetch();
        return $st->rowCount() > 0 ? $rad['nummer'] : -1;

    }

    public static function staticNummerVelger($velger_id, $storhybel_id): int
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND velger_id=:vid)');
        $st->bindParam(':sid', $storhybel_id);
        $st->bindParam(':vid', $velger_id);
        $st->execute();

        $rad = $st->fetch();
        return $st->rowCount() > 0 ? $rad['nummer'] : -1;

    }

    private function velgerFraNr($nr)
    {

        $st = DB::getDB()->prepare('SELECT velger_id FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND nummer=:nr)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':nr', $nr);
        $st->execute();

        $id = $st->fetch()['velger_id'];

        return StorhybelVelger::medVelgerId($id);
    }

    public function fjernRom(Rom $rom)
    {

        $index = array_search($rom, $this->ledige_rom);

        if ($index > -1) {

            array_splice($this->ledige_rom, $index, 1);

            $st = DB::getDB()->prepare('DELETE FROM storhybel_rom WHERE (storhybel_id=:id AND rom_id=:rid)');
            $st->bindParam(':id', $this->id);
            $st->bindParam(':rid', $rom->getId());
            $st->execute();
        }
    }

    public function leggtilRom(Rom $rom)
    {

        if (!isset($this->ledige_rom[$rom->getId()])) {

            $this->ledige_rom[] = $rom;

            $st = DB::getDB()->prepare('INSERT INTO storhybel_rom (storhybel_id, rom_id) VALUES(:sid,:rid)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':rid', $rom->getId());
            $st->execute();
        }
    }

    public function fjernVelger(int $velger_id)
    {

        if (($nr = $this->nummerVelger($velger_id)) > 0) {
            // Slett personen fra rekkefølgen.
            $st = DB::getDB()->prepare('DELETE FROM storhybel_rekkefolge WHERE velger_id=:vid');
            $st->bindParam(':vid', $velger_id);
            $st->execute();

            $st = DB::getDB()->prepare('DELETE FROM storhybel_velger WHERE velger_id=:vid');
            $st->bindParam(':vid', $velger_id);
            $st->execute();

            $st = DB::getDB()->prepare('DELETE FROM storhybel_fordeling WHERE velger_id=:vid');
            $st->bindParam(':vid', $velger_id);
            $st->execute();

            // Flytt alle beboere under opp én plass.
            $st = DB::getDB()->prepare('UPDATE storhybel_rekkefolge SET nummer=nummer-1 WHERE (storhybel_id=:sid AND nummer > :nr)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':nr', $nr);
            $st->execute();
        }
    }

    public function leggTilVelger(int $velger_id, int $nr = null)
    {
        $st = DB::getDB()->prepare('INSERT INTO storhybel_rekkefolge (storhybel_id,velger_id,nummer) VALUES(:sid,:vid,:nr)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':vid', $velger_id);
        if (is_null($nr)) {
            $nr = count($this->rekkefolge) + 1;
        }
        $st->bindParam(':nr', $nr);
        $st->execute();
    }

    public function flyttVelger(StorhybelVelger $velger, int $nyNr)
    {
        $fraNr = $this->nummerVelger($velger->getVelgerId());

        $st = DB::getDB()->prepare('DELETE FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND velger_id=:vid)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':vid', $velger->getVelgerId());
        $st->execute();


        //Feks fra 3 til 10
        if ($fraNr - $nyNr < 0) {
            $st = DB::getDB()->prepare('UPDATE storhybel_rekkefolge SET nummer=nummer-1 WHERE (storhybel_id=:sid AND nummer > :fra AND nummer <= :ny)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':ny', $nyNr);
            $st->bindParam(':fra', $fraNr);
            $st->execute();

        } //Feks fra 10 til 3
        else {
            $st = DB::getDB()->prepare('UPDATE storhybel_rekkefolge SET nummer=nummer+1 WHERE (storhybel_id=:sid AND nummer >= :ny AND nummer < :fra)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':ny', $nyNr);
            $st->bindParam(':fra', $fraNr);
            $st->execute();
        }

        $st = DB::getDB()->prepare('INSERT INTO storhybel_rekkefolge (storhybel_id, velger_id, nummer) VALUES(:sid,:vid,:nummer)');
        $st->bindParam(':nummer', $nyNr);
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':vid', $velger->getVelgerId());
        $st->execute();


    }

    public static function nyListe($ledige_rom, $rekkefolge, $navn = null): Storhybelliste
    {

        $instans = new Storhybelliste();

        if (!is_null($navn)) {
            $instans->navn = self::genererNavn($navn);
        } else {
            $instans->navn = self::genererNavn();
        }
        $instans->aktiv = 0;
        $instans->semester = $semester = Funk::generateSemesterString(date('Y-m-d'));
        $instans->ledige_rom = $ledige_rom;
        //$instans->rekkefolge = self::beboerTilVelgerListe($rekkefolge);
        $instans->rekkefolge = StorhybelVelger::nyVelgerListe($rekkefolge);

        $instans->lagre();

        return $instans;
    }

    public static function nyTomListe($navn = null)
    {

        $instans = new Storhybelliste();

        if (!is_null($navn)) {
            $instans->navn = self::genererNavn($navn);
        } else {
            $instans->navn = self::genererNavn();
        }
        $instans->aktiv = 0;
        $instans->semester = $semester = Funk::generateSemesterString(date('Y-m-d'));
        $instans->ledige_rom = null;
        $instans->rekkefolge = null;

        $instans->lagre();

        return $instans;

    }

    public static function beboerTilVelgerListe(array $rekkefolge): array
    {

        $velger_rekkefolge = array();

        foreach ($rekkefolge as $beboer) {
            $velger_rekkefolge[] = StorhybelVelger::nyVelger(array($beboer));
        }

        return $velger_rekkefolge;

    }

    public static function genererNavn($type = null): string
    {
        if (!is_null($type)) {
            $typen = $type;
        } else {
            $typen = 'Storhybelliste';
        }

        $semester_readable = Funk::genReadableSemStr(date('Y-m-d'));
        $semester = Funk::generateSemesterString(date('Y-m-d'));

        $temp = "%{$typen}%";
        $st = DB::getDB()->prepare('SELECT count(*) as cnt FROM storhybel WHERE (semester=:semester AND navn LIKE :typen)');
        $st->execute(['semester' => $semester, 'typen' => $temp]);

        $nummer = $st->fetch()['cnt'] + 1;

        return "{$typen} {$semester_readable} - Nr. {$nummer}";

    }

    public static function genererKorrNavn(): string
    {
        $semester_readable = Funk::genReadableSemStr(date('Y-m-d'));
        $semester = Funk::generateSemesterString(date('Y-m-d'));

        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE (semester=:semester AND navn LIKE "%Korrhybelliste%" ');
        $st->bindParam(':semester', $semester);
        $st->execute();

        $nummer = $st->rowCount() + 1;

        return "Korrhybelliste {$semester_readable} - Nr. {$nummer}";
    }

    public static function genererSPNavn(): string
    {
        $semester_readable = Funk::genReadableSemStr(date('Y-m-d'));
        $semester = Funk::generateSemesterString(date('Y-m-d'));

        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE (semester=:semester AND navn LIKE "%Stor Parhybelliste%" ');
        $st->bindParam(':semester', $semester);
        $st->execute();

        $nummer = $st->rowCount() + 1;

        return "Stor Parhybelliste {$semester_readable} - Nr. {$nummer}";
    }

    public static function alle()
    {
        $arr = array();

        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE (aktiv != 2)');
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $arr[] = Storhybelliste::init($st);
        }

        return $arr;

    }

    public static function arkiverte()
    {
        $arr = array();

        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE (aktiv = 2)');
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $arr[] = Storhybelliste::init($st);
        }

        return $arr;
    }

    public function arkiver()
    {
        $this->aktiv = 2;
        $this->lagreIntern();
    }

    public function erArkivert()
    {
        return $this->aktiv === '2';
    }

    /*
     * Returnerer true om det finnes en aktiv storhybelliste.
     * False ellers.
     */
    public static function finnesAktive(): bool
    {
        $st = DB::getDB()->prepare('SELECT count(*) as cnt FROM storhybel WHERE aktiv=1 ORDER BY id DESC LIMIT 1');
        $st->execute();

        $antall = $st->fetch()['cnt'];
        return $antall > 0;
    }

    public static function aktive(): array
    {
        // Det skal bare være én aktiv. Henter ut denne.
        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE aktiv=1');
        $st->execute();

        $results = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $results[] = self::init($st);
        }

        return $results;
    }

    public function slett()
    {

        $st = DB::getDB()->prepare('DELETE FROM storhybel_velger WHERE storhybel_id=:sid');
        $st->bindParam(':sid', $this->id);
        $st->execute();

        $st = DB::getDB()->prepare('DELETE FROM storhybel_rekkefolge WHERE storhybel_id=:sid');
        $st->bindParam(':sid', $this->id);
        $st->execute();

        $st = DB::getDB()->prepare('DELETE FROM storhybel_fordeling WHERE storhybel_id=:sid');
        $st->bindParam(':sid', $this->id);
        $st->execute();

        $st = DB::getDB()->prepare('DELETE FROM storhybel_rom WHERE storhybel_id=:sid');
        $st->bindParam(':sid', $this->id);
        $st->execute();

        $st = DB::getDB()->prepare('DELETE FROM storhybel WHERE id=:sid');
        $st->bindParam(':sid', $this->id);
        $st->execute();

    }

    public function velgRom(StorhybelVelger $velger, Rom $rom)
    {

        /*
         * Mellomlagre aktuelle rom da disse kan bli endret av funksjoner på $this. #laziness
         */
        $gamle_rom = array();
        foreach ($velger->getBeboere() as $beboer) {
            $gamle_rom[] = $beboer->getRom();
        }

        $st = DB::getDB()->prepare('UPDATE storhybel_fordeling SET ny_rom_id=:nri WHERE (storhybel_id=:sid AND velger_id=:vid)');
        $st->bindParam(':nri', $rom->getId());
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':vid', $velger->getVelgerId());
        $st->execute();

        /*
         * Oppdater fordelingsinstans for å sikre korrekt databehandling videre.
         */
        $this->fordeling = StorhybelFordeling::medStorhybelId($this->id);

        /*
         * Fjern det rommet som ble valgt fra lista.
         */
        $this->fjernRom($rom);

        /*
         * De gamle rommene tilhørende velgeren blir lagt ut på lista.
         */
        foreach ($gamle_rom as $gammelt_rom) {
            /* @var Rom $gammelt_rom */
            if (!($gammelt_rom->getId() == $rom->getId())
                && $this->gyldigRom($gammelt_rom)) {
                $this->leggtilRom($gammelt_rom);
            }
        }

        /*
         * Beboerene i velger-objektet blir fjerna fra andre aktive lister. De har allerede valgt en hybel, og
         * trenger følgelig ikke flere for det kommende/inneværende semesteret.
         */
        foreach ($velger->getBeboere() as $beboer) {
            /* @var Beboer $beboer */
            $lister = self::listerMedBeboer($beboer->getId());

            if (count($lister) > 1) {
                foreach ($lister as $lista) {
                    /* @var Storhybelliste $lista */

                    if ($lista->getId() == $this->id) {
                        continue;
                    }

                    $velgeren = StorhybelVelger::medBeboerIdStorhybelId($beboer->getId(), $lista->getId());
                    /* @var StorhybelVelger $velgeren */
                    $lista->fjernVelger($velgeren->getVelgerId());
                }
            }

        }

        /*
         * Og så blir det automagisk nestemanns tur.
         */
        $this->neste();
    }

    /*
     * Dersom en beboer er på flere velger-objekter, er det mulig å passe på den første.
     */
    public function kanPasse(Beboer $beboer, StorhybelVelger $aktuell_velger): bool
    {

        if(!is_null(StorhybelFordeling::medVelgerIdStorhybelId($aktuell_velger->getVelgerId(), $this->id)->getNyttRomId())) {
            return true;
        }

        $st = DB::getDB()->prepare('SELECT count(*) as cnt FROM storhybel_velger AS sv WHERE (sv.storhybel_id = :sid AND sv.beboer_id= :bid)');
        $st->execute(['sid' => $this->id, 'bid' => $beboer->getId()]);
        $count = $st->fetch()['cnt'];

        if ($count < 2) {
            return false;
        }

        $velgere = StorhybelVelger::medBeboerIdStorhybelId($beboer->getId(), $this->id);
        $minsteNr = $aktuell_velger->getNummer();
        foreach ($velgere as $velger) {

            if ($velger->getNummer() < $minsteNr) {
                $minsteNr = $velger->getNummer();
            }

        }

        return $minsteNr === $aktuell_velger->getNummer();

    }

    /*
     * Returnerer en liste med AKTIVE Storhybellister der beboeren med $beboer_id inngår.
     */
    public static function listerMedBeboer(int $beboer_id): array
    {

        $st = DB::getDB()->prepare(
            'SELECT * FROM storhybel AS sh WHERE 
                                     (sh.aktiv = 1 
                                        AND sh.id IN 
                                            (SELECT storhybel_id FROM storhybel_velger WHERE beboer_id=:bid))');
        $st->execute(['bid' => $beboer_id]);

        $arr = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $arr[] = self::init($st);
        }

        return $arr;
    }

    public function reSort()
    {

        usort($this->rekkefolge, function (StorhybelVelger $a, StorhybelVelger $b) {
            if ($a->getAnsiennitet() > $b->getAnsiennitet()) {
                return -1;
            }

            if ($a->getAnsiennitet() == $b->getAnsiennitet() && $a->getMaxKlassetrinn() > $b->getMaxKlassetrinn()) {
                return -1;
            }

            if ($a->getAnsiennitet() == $b->getAnsiennitet() && $a->getMaxKlassetrinn() == $b->getMaxKlassetrinn()) {
                $r = [1, -1];
                return $r[array_rand($r)];
            }

            return 1;
        });

        $this->slettRekkefolge();
        $this->lagreRekkefolge();
    }

    /*
     * Denne funksjonen kan bare kalles én gang per storhybelliste.
     * Når det commites, vil alle beboere 'flyttes' til de valgte rommene.
     * Deretter settes Storhybellisten til 'Ferdig', og kan ikke lenger
     * interageres med.
     */
    public function commit()
    {
        foreach ($this->fordeling as $fordeling) {
            /* @var $fordeling \intern3\StorhybelFordeling */
            //$beboer = Beboer::medId($fordeling->getBeboerId());
            $velger = StorhybelVelger::medVelgerId($fordeling->getVelgerId());

            foreach ($velger->getBeboere() as $beboer) {
                /* @var $beboer \intern3\Beboer */
                if ($fordeling->getNyttRomId() !== null && $fordeling->getNyttRom() !== null) {
                    $beboer->byttRom($fordeling->getNyttRom());
                }
            }
        }

        $st = DB::getDB()->prepare('UPDATE storhybel SET aktiv=-1 WHERE id=:sid');
        $st->bindParam(':sid', $this->id);
        $st->execute();
    }

    public static function aktivStorhybelliste(): ?Storhybelliste
    {
        foreach (self::aktive() as $liste) {
            /* @var \intern3\Storhybelliste $liste */

            if (strpos($liste->getNavn(), 'Storhybelliste') !== false) {
                return $liste;
            }
        }

        return null;
    }

}