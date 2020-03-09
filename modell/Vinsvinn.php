<?php


namespace intern3;


class Vinsvinn
{

    private int $id;
    private int $vinid;
    private ?Vin $vin;
    private int $antall;
    private string $registrert;
    private string $tidspunkt;

    private static function init(\PDOStatement $st): ?Vinsvinn
    {

        if (($rad = $st->fetch()) == null) {
            return null;
        }

        $instans = new self();

        $instans->id = intval($rad['id']);
        $instans->vinid = intval($rad['vin_id']);
        $instans->vin = null;
        $instans->antall = intval($rad['antall']);
        $instans->registrert = $rad['registrert'];
        $instans->tidspunkt = $rad['tidspunkt'];

        return $instans;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getVinid(): int
    {
        return $this->vinid;
    }

    /**
     * @return Vin
     */
    public function getVin(): ?Vin
    {
        if(is_null($this->vin)) {
            $this->vin = Vin::medId($this->vinid);
        }
        return $this->vin;
    }

    /**
     * @return int
     */
    public function getAntall(): int
    {
        return $this->antall;
    }

    /**
     * @return string
     */
    public function getRegistrert(): string
    {
        return $this->registrert;
    }

    /**
     * @return string
     */
    public function getTidspunkt(): string
    {
        return $this->tidspunkt;
    }


    public static function alle()
    {
        $res = array();

        $st = DB::getDB()->prepare('SELECT * FROM vinsvinn ORDER BY id DESC');
        $st->execute();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $res[] = self::init($st);
        }

        return $res;
    }
}