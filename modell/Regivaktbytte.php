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
    private string $passord;
    private array $forslag_ider;
    private ?array $forslag_vakter;
    private string $merknad;
    private string $slipp;

    private static function init(\PDOStatement $st): ?Regivaktbytte
    {

        if (is_null(($rad = $st->fetch()))) {
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
        $instans->forslag_ider = strlen($rad['forslag']) > 0 ? json_decode($rad['forslag']) : array();
        $instans->forslag_vakter = null;
        $instans->merknad = $rad['merknad'];
        $instans->slipp = $rad['slipp'];

        return $instans;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getBrukerId(): int
    {
        return $this->bruker_id;
    }

    /**
     * @param int $bruker_id
     */
    public function setBrukerId(int $bruker_id): void
    {
        $this->bruker_id = $bruker_id;
    }

    /**
     * @return int
     */
    public function getRegivaktId(): int
    {
        return $this->regivakt_id;
    }

    /**
     * @param int $regivakt_id
     */
    public function setRegivaktId(int $regivakt_id): void
    {
        $this->regivakt_id = $regivakt_id;
    }

    /**
     * @return Regivakt|null
     */
    public function getRegivakt(): ?Regivakt
    {
        if(is_null($this->regivakt)) {
            $this->regivakt = Regivakt::medId($this->regivakt_id);
        }
        return $this->regivakt;
    }

    /**
     * @param Regivakt|null $regivakt
     */
    public function setRegivakt(?Regivakt $regivakt): void
    {
        $this->regivakt = $regivakt;
    }

    /**
     * @return bool
     */
    public function isGisbort(): bool
    {
        return $this->gisbort;
    }

    /**
     * @param bool $gisbort
     */
    public function setGisbort(bool $gisbort): void
    {
        $this->gisbort = $gisbort;
    }

    /**
     * @return bool
     */
    public function harPassord(): bool
    {
        return $this->har_passord;
    }

    /**
     * @return string
     */
    public function getPassord(): string
    {
        return $this->passord;
    }

    /**
     * @param string $passord
     */
    public function setPassord(string $passord): void
    {
        $this->passord = $passord;
    }

    /**
     * @return array
     */
    public function getForslagIder(): array
    {
        return $this->forslag_ider;
    }

    /**
     * @return array
     */
    public function getForslagVakter(): array
    {
        if(is_null($this->forslag_vakter)) {
            $tmp = array();
            foreach($this->forslag_ider as $rvid) {
                $tmp[] = Regivakt::medId($rvid);
            }
        }

        return $this->forslag_vakter;
    }

    /**
     * @return string
     */
    public function getMerknad(): string
    {
        return $this->merknad;
    }

    /**
     * @param string $merknad
     */
    public function setMerknad(string $merknad): void
    {
        $this->merknad = $merknad;
    }

    /**
     * @return string
     */
    public function getSlipp(): string
    {
        return $this->slipp;
    }

    /**
     * @param string $slipp
     */
    public function setSlipp(string $slipp): void
    {
        $this->slipp = $slipp;
    }


}