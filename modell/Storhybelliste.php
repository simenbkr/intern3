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

    private static function init(\PDOStatement $st): Storhybelliste
    {

        $rad = $st->fetch();
        if ($rad === null) {
            return null;
        }

        $instans = new self();

        $instans->id = $rad['id'];
        $instans->semester = $rad['semester'];
        $instans->navn = $rad['navn'];
        $instans->aktiv = $rad['aktiv'] != 0;
        $instans->ledige_rom = RomListe::fraStorhybelListe($instans->id);
        $instans->rekkefolge = BeboerListe::fraStorhybelliste($instans->id);
        $instans->velgerNr = $rad['velger'];
        $instans->velger = $instans->beboerFraNr($rad['velger']);
        $instans->neste = $instans->beboerFraNr($instans->velgerNr + 1);

        return $instans;
    }

    public static function medId($id): Storhybelliste
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
        return $this->aktiv;
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

    public function getVelger() //Return type ?Beboer.
    {
        return $this->velger;
    }

    public function getVelgerNr(): int
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

    public function setNeste(Beboer $beboer)
    {
        $this->neste = $beboer;
    }

    public function getRekkefolge(): array
    {
        return $this->rekkefolge;
    }

    public function aktiver()
    {
        $this->velger++;
        $this->aktiv = 1;
        $this->lagreIntern();
    }

    public function neste()
    {
        $this->velger++;
        $this->lagreIntern();
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

            $st = DB::getDB()->prepare('INSERT INTO storhybel (navn, semester) VALUES(:navn, :semester)');
            $st->bindParam(':navn', $this->navn);
            $st->bindParam(':semester', $this->semester);
            $st->execute();

            $this->id = DB::getDB()->query('SELECT id FROM storhybel ORDER BY id DESC LIMIT 1')->fetch()['id'];

            $this->lagreRomliste();
            $this->lagreRekkefolge();

        }

        return $this;

    }

    private function lagreIntern()
    {
        $st = DB::getDB()->prepare('UPDATE storhybel SET navn=:navn,semester=:semester,aktiv=:aktiv,velger=:velger WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':semester', $this->semester);
        $st->bindParam(':aktiv', $this->aktiv);
        $st->bindParam(':velger', $this->velger);
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
        $st = DB::getDB()->prepare('INSERT INTO storhybel_rekkefolge (storhybel_id, beboer_id, nummer) VALUES (:sid, :bid, :nr)');
        $st->bindParam(':sid', $this->id);

        foreach ($this->rekkefolge as $beboer) {
            /* @var Beboer $beboer */

            $st->bindParam(':bid', $beboer->getId());
            $st->bindParam(':nr', $nummer);
            $st->execute();

            $nummer++;

        }
    }

    public function nummerBeboer(int $beboer_id): int
    {
        $st = DB::getDB()->prepare('SELECT * FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND beboer_id=:bid)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':bid', $beboer_id);
        $st->execute();

        $rad = $st->fetch();
        return $rad['nummer'];

    }

    private function beboerFraNr($nr) //Legg til return type ?Beboer når php7.1
    {

        $st = DB::getDB()->prepare('SELECT beboer_id FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND nummer=:nr)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':nr', $nr);
        $st->execute();

        $id = $st->fetch()['beboer_id'];

        return Beboer::medId($id);
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
        $index = array_search($rom, $this->ledige_rom);

        if (!$index) {

            $this->ledige_rom[] = $rom;

            $st = DB::getDB()->prepare('INSERT INTO storhybel_rom (storhybel_id, rom_id) VALUES(:sid,:rid)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':rid', $rom->getId());
            $st->execute();
        }
    }

    public function flyttBeboer(Beboer $beboer, int $nyNr)
    {
        $fraNr = $this->nummerBeboer($beboer->getId());

        $st = DB::getDB()->prepare('DELETE FROM storhybel_rekkefolge WHERE (storhybel_id=:sid AND beboer_id=:bid)');
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':bid', $beboer->getId());
        $st->execute();



        //Feks fra 3 til 10
        if ($fraNr - $nyNr < 0) {
            $st = DB::getDB()->prepare('UPDATE storhybel_rekkefolge SET nummer=nummer-1 WHERE (storhybel_id=:sid AND nummer > :fra AND nummer <= :ny)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':ny', $nyNr);
            $st->bindParam(':fra', $fraNr);
            $st->execute();

        }
        //Feks fra 10 til 3
        else {
            $st = DB::getDB()->prepare('UPDATE storhybel_rekkefolge SET nummer=nummer+1 WHERE (storhybel_id=:sid AND nummer >= :ny AND nummer < :fra)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':ny', $nyNr);
            $st->bindParam(':fra', $fraNr);
            $st->execute();
        }

        $st = DB::getDB()->prepare('INSERT INTO storhybel_rekkefolge (storhybel_id, beboer_id, nummer) VALUES(:sid,:bid,:nummer)');
        $st->bindParam(':nummer', $nyNr);
        $st->bindParam(':sid', $this->id);
        $st->bindParam(':bid', $beboer->getId());
        $st->execute();


    }

    public static function nyListe($ledige_rom, $rekkefolge): Storhybelliste
    {

        $instans = new Storhybelliste();
        $instans->navn = self::genererNavn();
        $instans->aktiv = 0;
        $instans->semester = $semester = Funk::generateSemesterString(date('Y-m-d'));
        $instans->ledige_rom = $ledige_rom;
        $instans->rekkefolge = $rekkefolge;

        return $instans->lagre();
    }

    public static function genererNavn(): string
    {

        $semester_readable = Funk::genReadableSemStr(date('Y-m-d'));
        $semester = Funk::generateSemesterString(date('Y-m-d'));

        $st = DB::getDB()->prepare('SELECT * FROM storhybel WHERE semester=:semester');
        $st->bindParam(':semester', $semester);
        $st->execute();

        $nummer = $st->rowCount() + 1;

        return "{$semester_readable} - Nr. {$nummer}";

    }

    public static function alle()
    {
        $arr = array();

        $st = DB::getDB()->prepare('SELECT * FROM storhybel');
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $arr[] = Storhybelliste::init($st);
        }

        return $arr;

    }

}