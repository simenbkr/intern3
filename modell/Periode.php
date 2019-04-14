<?php


namespace intern3;


class Periode
{

    private $id;
    private $start;
    private $slutt;

    private static function init(\PDOStatement $st): Periode
    {

        if (($rad = $st->fetch()) == null) {
            return null;
        }

        $instans = new self();
        $instans->id = $rad['id'];
        $instans->start = $rad['dato'];

        $st = DB::getDB()->prepare('SELECT dato FROM fakturert WHERE id = :id');
        $st->execute(['id' => $instans->id + 1]);
        $instans->slutt = $st->fetch()['dato'];

        return $instans;
    }

    public static function medId(int $id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert WHERE id = :id');
        $st->execute(['id' => $id]);

        return self::init($st);
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getSlutt()
    {
        return $this->slutt;
    }

    public static function getAlle()
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert ORDER BY id DESC');
        $st->execute();

        $array = array();
        for ($i = 0; $i < $st->rowCount(); $i++) {
            $array[] = self::init($st);
        }

        return $array;

    }

    public function toString()
    {
        return "{$this->start} - {$this->slutt}";
    }

    public function getId()
    {
        return $this->id;
    }

}