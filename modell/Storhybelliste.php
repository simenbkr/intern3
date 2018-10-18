<?php

namespace intern3;


class Storhybelliste
{
    private $id;
    private $navn;
    private $semester;
    private $ledige_rom;
    private $rekkefolge;
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
        $instans->ledige_rom = RomListe::fraStorhybelListe($instans->id);
        $instans->rekkefolge = BeboerListe::fraStorhybelliste($instans->id);

        return $instans;
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

    public function getSemester(): string
    {
        return $this->semester;
    }

    public function getLedigeRom(): array
    {
        return $this->ledige_rom;
    }

    public function getNeste(): Beboer
    {
        return $this->neste;
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
        $st = DB::getDB()->prepare('UPDATE storhybel SET navn=:navn,semester=:semester WHERE id=:id');
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':semester', $this->semester);
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

        if(!$index) {

            $this->ledige_rom[] = $rom;

            $st = DB::getDB()->prepare('INSERT INTO storhybel_rom (storhybel_id, rom_id) VALUES(:sid,:rid)');
            $st->bindParam(':sid', $this->id);
            $st->bindParam(':rid', $rom->getId());
            $st->execute();
        }
    }

    public function flyttBeboer(Beboer $beboer, int $nummer) {

    }

    public static function nyListe($ledige_rom, $rekkefolge): Storhybelliste
    {

        $instans = new Storhybelliste();
        $instans->navn = self::genererNavn();
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

}