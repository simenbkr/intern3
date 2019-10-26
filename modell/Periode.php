<?php


namespace intern3;


class Periode
{

    private $id;
    private $start;
    private $slutt;

    private static function init(\PDOStatement $st): ?Periode
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

    public static function medId(int $id): ?Periode
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert WHERE id = :id');
        $st->execute(['id' => $id]);

        return self::init($st);
    }

    public static function getForste(): ?Periode
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert ORDER BY id ASC LIMIT 1');
        $st->execute();

        return self::init($st);
    }

    public static function getSiste(): Periode
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert ORDER BY id DESC LIMIT 1');
        $st->execute();

        return self::init($st);
    }

    public static function getForrige(): Periode {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert ORDER BY id DESC LIMIT 2');
        $st->execute();
        self::init($st);

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

    public static function getAlle(): array
    {
        $st = DB::getDB()->prepare('SELECT * FROM fakturert ORDER BY id DESC');
        $st->execute();

        $array = array();
        for ($i = 0; $i < $st->rowCount(); $i++) {
            $array[] = self::init($st);
        }

        return $array;
    }

    public function toString(): string
    {
        return "{$this->start} - {$this->slutt}";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function beboerPerioder(Beboer $beboer): array
    {
        $result = array();
        $semesterliste = $beboer->getSemesterlist();

        $start = date('Y-m-d',
            Funk::semStrToUnix(end($semesterliste))
        );

        $slutt = Funk::getSemesterEnd($semesterliste[0]);

        $st = DB::getDB()->prepare('SELECT * FROM fakturert WHERE (dato >= :start AND dato <= :slutt) ORDER BY id DESC');
        $st->execute(['start' => $start, 'slutt' => $slutt]);

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $kandidater[] = self::init($st);
        }

        foreach ($kandidater as $kand) {
            /* @var \intern3\Periode $kand */

            if ($beboer->beboerVed($kand->getStart()) || $beboer->beboerVed($kand->getSlutt())) {
                $result[] = $kand;
            }

        }

        return $result;
    }

}