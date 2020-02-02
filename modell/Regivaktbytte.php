<?php


namespace intern3;


class Regivaktbytte
{

    private int $id;
    private int $bruker_id;
    private int $regivakt_id;
    private ?Regivakt $regivakt;
    private bool $gisbort;
    private bool $har_passord;
    private ?string $passord;
    private array $forslag_ider;
    private ?array $forslag_vakter;
    private ?string $merknad;
    private ?string $slipp;
    private ?Bruker $bruker;

    private static function init(\PDOStatement $st): ?Regivaktbytte
    {

        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }

        $instans = new self();
        $instans->id = intval($rad['id']);
        $instans->bruker_id = intval($rad['bruker_id']);
        $instans->regivakt_id = intval($rad['regivakt_id']);
        $instans->regivakt = null;
        $instans->gisbort = (intval($rad['gisbort']) === 1);
        $instans->passord = $rad['passord'];
        $instans->har_passord = (strlen($instans->passord) > 0);
        $instans->forslag_ider = strlen($rad['forslag']) > 0 ? json_decode($rad['forslag'], true) : array();
        $instans->forslag_vakter = null;
        $instans->merknad = $rad['merknad'];
        $instans->slipp = $rad['slipp'];
        $instans->bruker = null;

        return $instans;
    }

    public function lagre()
    {
        $st = DB::getDB()->prepare('UPDATE regivakt_bytte SET bruker_id = :bid, 
regivakt_id = :rvid, gisbort = :gisbort, passord = :passord, forslag = :forslag, merknad = :merknad, slipp = :slipp WHERE id = :id');
        $st->execute([
            'id' => $this->id,
            'bid' => $this->bruker_id,
            'rvid' => $this->regivakt_id,
            'gisbort' => $this->gisbort,
            'passord' => $this->passord,
            'forslag' => json_encode($this->forslag_ider, true),
            'merknad' => $this->merknad,
            'slipp' => $this->slipp
        ]);
    }

    public function slett()
    {
        $st = DB::getDB()->prepare('DELETE FROM regivakt_bytte WHERE id = :id');
        $st->execute(['id' => $this->id]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrukerId(): int
    {
        return $this->bruker_id;
    }

    public function getBruker(): ?Bruker
    {
        if (is_null($this->bruker)) {
            $this->bruker = Bruker::medId($this->bruker_id);
        }
        return $this->bruker;
    }

    public function setBrukerId(int $bruker_id): void
    {
        $this->bruker_id = $bruker_id;
    }

    public function getRegivaktId(): int
    {
        return $this->regivakt_id;
    }

    public function setRegivaktId(int $regivakt_id): void
    {
        $this->regivakt_id = $regivakt_id;
    }

    public function getRegivakt(): ?Regivakt
    {
        if (is_null($this->regivakt)) {
            $this->regivakt = Regivakt::medId($this->regivakt_id);
        }
        return $this->regivakt;
    }

    public function setRegivakt(?Regivakt $regivakt): void
    {
        $this->regivakt = $regivakt;
    }

    public function gisbort(): bool
    {
        return $this->gisbort;
    }

    public function setGisbort(bool $gisbort): void
    {
        $this->gisbort = $gisbort;
    }

    public function harPassord(): bool
    {
        return $this->har_passord;
    }

    public function getPassord(): string
    {
        return $this->passord;
    }

    public function setPassord(string $passord): void
    {
        $this->passord = $passord;
    }

    public function riktigPassord(string $passord): bool
    {
        return $this->passord === $passord;
    }

    public function getForslagIderAssoc(): array
    {
        return $this->forslag_ider;
    }

    public function getForslagIder() : array {
        $arr = array();
        foreach($this->forslag_ider as $id => $bruker) {
            $arr[] = $id;
        }

        return $arr;
    }

    public function getForslagVakter(): array
    {
        if (is_null($this->forslag_vakter)) {
            $tmp = array();
            foreach ($this->forslag_ider as $rvid => $forslager) {
                $tmp[] = Regivakt::medId($rvid);
            }
            $this->forslag_vakter = $tmp;
        }

        return $this->forslag_vakter;
    }

    public function getForslagVakterAssoc(): array
    {
        if (is_null($this->forslag_vakter)) {
            $tmp = array();
            foreach ($this->forslag_ider as $rvid => $forslager) {
                $tmp[$forslager] = Regivakt::medId($rvid);
            }
            $this->forslag_vakter = $tmp;
        }

        return $this->forslag_vakter;
    }


    public function leggTilForslag($bruker_id, $id)
    {
        $this->forslag_ider[$id] = $bruker_id;
        $this->lagre();
    }

    public function slettForslag($bruker_id, $id) {
        $ny_array = array();
        foreach($this->forslag_ider as $fid => $bid) {
            if($fid == $id && $bruker_id == $bid) {
                continue;
            }
            $ny_array[] = $fid;
        }
        $this->forslag_ider = $ny_array;
        $this->lagre();
    }

    public function getMerknad(): string
    {
        return $this->merknad;
    }

    public function setMerknad(string $merknad): void
    {
        $this->merknad = $merknad;
    }

    public function getSlipp(): string
    {
        return $this->slipp;
    }

    public function setSlipp(string $slipp): void
    {
        $this->slipp = $slipp;
    }

    public static function ny($bruker_id, $regivakt_id, $gisbort, $passord, $merknad)
    {

        $st = DB::getDB()->prepare('INSERT INTO regivakt_bytte(bruker_id, regivakt_id, gisbort, passord, merknad)
VALUES(:bid, :rvid, :gisbort, :passord, :merknad)');
        $st->execute([
            'bid' => $bruker_id,
            'rvid' => $regivakt_id,
            'gisbort' => $gisbort,
            'passord' => $passord,
            'merknad' => $merknad
        ]);

        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte ORDER BY id DESC LIMIT 1');
        $st->execute();
        return self::init($st);
    }

    public static function liste()
    {

        $lista = array();
        //$start = Funk::getSemesterStart(Funk::generateSemesterString(date('Y-m-d')));
        $slutt = Funk::getSemesterEnd(Funk::generateSemesterString(date('Y-m-d')));

        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte WHERE regivakt_bytte.regivakt_id IN (SELECT id FROM regivakt WHERE dato >= :start AND dato <= :slutt)');
        $st->execute([
            'start' => date('Y-m-d'),
            'slutt' => $slutt
        ]);

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $lista[] = self::init($st);
        }

        return $lista;
    }

    public static function medRegivaktId($regivakt_id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte WHERE regivakt_id = :rvid');
        $st->execute(['rvid' => $regivakt_id]);
        return self::init($st);
    }

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte WHERE id = :id');
        $st->execute(['id' => $id]);
        return self::init($st);
    }

    public static function medRegivaktIdBrukerId($regivakt_id, $bruker_id) {
        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte WHERE regivakt_id = :rvid AND bruker_id = :bid');
        $st->execute([
            'rvid' => $regivakt_id,
            'bid' => $bruker_id
        ]);

        return self::init($st);
    }


}