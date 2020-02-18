<?php


namespace intern3;


class RegiBytteForslag
{
    private int $id;

    private int $regivakt_bytte_id;
    private ?Regivaktbytte $bytte;

    private int $regivakt_id;
    private ?Regivakt $regivakt;

    private int $bruker_id;
    private ?Bruker $bruker;

    private static function init(\PDOStatement $st): ?RegiBytteForslag
    {

        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }

        $instans = new self();

        $instans->id = intval($rad['id']);

        $instans->regivakt_bytte_id = intval($rad['regivakt_bytte_id']);
        $instans->bytte = null;

        $instans->regivakt_id = intval($rad['regivakt_id']);
        $instans->regivakt = null;

        $instans->bruker_id = intval($rad['bruker_id']);
        $instans->bruker = null;

        return $instans;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrukerId()
    {
        return $this->bruker_id;
    }

    public function getBruker()
    {
        if (is_null($this->bruker)) {
            $this->bruker = Bruker::medId($this->bruker_id);
        }
        return $this->bruker;
    }

    public function getRegivaktId()
    {
        return $this->regivakt_id;
    }

    public function getRegiVakt()
    {
        if (is_null($this->regivakt)) {
            $this->regivakt = Regivakt::medId($this->regivakt_id);
        }

        return $this->regivakt;
    }

    public function getRegivaktBytte()
    {
        if (is_null($this->bytte)) {
            $this->bytte = Regivaktbytte::medId($this->regivakt_bytte_id);
        }

        return $this->bytte;
    }

    public static function fraInstanser(Regivaktbytte $bytte, Regivakt $rv, Bruker $bruker): ?RegiBytteForslag
    {
        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte_forslag WHERE regivakt_bytte_id = :rvbid AND regivakt_id = :rvid AND bruker_id = :bid');
        $st->execute([
            'rvbid' => $bytte->getId(),
            'rvid' => $rv->getId(),
            'bid' => $bruker->getId(),
        ]);

        return self::init($st);
    }

    public static function fraIder($regivakt_bytte_id, $regivakt_id, $bruker_id) {
        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte_forslag WHERE regivakt_bytte_id = :rvbid AND regivakt_id = :rvid AND bruker_id = :bid');
        $st->execute([
            'rvbid' => $regivakt_bytte_id,
            'rvid' => $regivakt_id,
            'bid' => $bruker_id,
        ]);

        return self::init($st);
    }

    public static function listeForBytte($bytte_id) {
        $lista = array();

        $st = DB::getDB()->prepare('SELECT * FROM regivakt_bytte_forslag WHERE regivakt_bytte_id = :rvbid');
        $st->execute([
            'rvbid' => $bytte_id
        ]);

        for($i = 0; $i < $st->rowCount(); $i++) {
            $lista[] = self::init($st);
        }

        return $lista;
    }

    public static function leggTilForslag($bytte_id, $rv_id, $bruker_id) {
        $st = DB::getDB()->prepare('INSERT INTO regivakt_bytte_forslag(regivakt_bytte_id, regivakt_id, bruker_id)
VALUES(:rvbid,:rvid,:bid)');
        $st->execute([
            'rvbid' => $bytte_id,
            'rvid' => $rv_id,
            'bid' => $bruker_id
        ]);

        return self::fraIder($bytte_id, $rv_id, $bruker_id);
    }

    public static function slettForslag($id) {
        $st = DB::getDB()->prepare('DELETE FROM regivakt_bytte_forslag WHERE id = :id');
        $st->execute([
            'id' => $id
        ]);
    }

}