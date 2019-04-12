<?php

namespace intern3;

class Bruker
{

    private $id;
    private $passord;
    private $salt;
    private $resett_tid;
    private $glemt_token;

    // Latskap
    private $person;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM bruker WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medEpost($epost)
    {
        if (!$epost) {
            return null;
        }
        $st = DB::getDB()->prepare('(SELECT br.* FROM beboer AS be,bruker AS br WHERE be.bruker_id=br.id AND be.epost=:epost) UNION (SELECT br.* FROM bruker AS br,ansatt AS an WHERE an.bruker_id=br.id AND an.epost=:epost);');
        $st->bindParam(':epost', $epost);
        $st->execute();
        return self::init($st);
    }

    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->passord = $rad['passord'];
        $instance->salt = $rad['salt'];
        $instance->resett_tid = $rad['dato'];
        $instance->glemt_token = $rad['glemt_token'];
        $instance->person = null;
        return $instance;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function passordErGyldig($passord)
    {
        //MÅ bruke === og ikke == da == er en shitty operator i denne situasjonen. TODO test at funker.
        return $passord === $this->passord;
    }

    public function getPerson()
    {
        if ($this->person <> null) {
            return $this->person;
        }
        $this->person = Beboer::medBrukerId($this->id);
        if ($this->person == null) {
            $this->person = Ansatt::medBrukerId($this->id);
        }
        return $this->person;
    }

    public function antallStraffevakter()
    {
        return Vakt::antallStraffeVakter($this->id);
    }

    public function antallVakterSkalSitte()
    {

        $semester = Funk::generateSemesterString(date('Y-m-d'));

        if (($vaktantall = VaktAntall::medIdSemester($this->id, $semester)) != null) {
            return $vaktantall->getAntall();
        }

        $beboer = $this->getPerson();
        if (!$beboer->erBeboer()) {
            return 0;
        }
        $rolle = $beboer->getRolle();
        $antall = date('m') > 6 ? $rolle->getVakterH() : $rolle->getVakterV();
        $antall += $this->antallStraffevakter();
        return $antall;
    }

    public function antallVakterHarSittet()
    {
        return Vakt::antallHarSittetMedBrukerId($this->id);
    }

    public function antallVakterErOppsatt()
    {
        return Vakt::antallErOppsattMedBrukerId($this->id);
    }

    public function antallForstevakter()
    {
        return Vakt::antallForsteMedBrukerId($this->id);
    }

    public function antallVakterHarIgjen()
    {
        return Vakt::antallHarIgjenMedBrukerId($this->id, $this->antallVakterSkalSitte());
    }

    public function antallVakterIkkeOppsatt()
    {
        return Vakt::antallIkkeOppsattMedBrukerId($this->id, $this->antallVakterSkalSitte());
    }

    public function antallVakterIkkeBekreftet()
    {
        return Vakt::antallIkkeBekreftetMedBrukerId($this->id);
    }


    /*
     * Returnerer førstevakter + 3.-4. vakt fredag og 2.,3.,4. vakt lørdag og 2.,3. vakt søndag
     */
    public function antallKjipeVakter(): int
    {

        $st = DB::getDB()->prepare('SELECT count(id) AS sum FROM vakt WHERE 
                                        (bruker_id=:brukerid  
                                        AND(
                                          (DAYOFWEEK(dato) = 6 AND vakttype IN (3, 4) ) 
                                          OR (DAYOFWEEK(dato) = 7 AND vakttype IN (2,3,4) ) 
                                          OR (DAYOFWEEK(dato) = 1 AND vakttype IN (2,3))
                                          OR vakttype = 1)
                                      )');

        $st->execute(['brukerid' => $this->id]);
        return $st->fetch()["sum"];
    }



    public function harForMangeForstevakter($lista = null) : bool
    {

        if ($lista == null) {
            $lista = VaktListe::medBrukerIdEtter($this->id, date('Y-m-d'));
        }

        $i = 0;
        foreach ($lista as $vakt) {
            /* @var Vakt $vakt */
            if ($vakt->getVakttype() == 1) {
                $i++;
            }
        }

        return $i > 2;
    }

    public function harForMangeKjipeVakter($lista = null) : bool
    {

        if ($lista == null) {
            $lista = VaktListe::medBrukerIdEtter($this->id, date('Y-m-d'));
        }

        $i = 0;
        foreach ($lista as $vakt) {
            /* @var Vakt $vakt */

            if ($vakt->erKjip()) {
                $i++;
            }

        }

        return $i > intval(floor($this->getPerson()->getRolle()->getVakterNow() / 2));
    }

    public function harVakterTett($lista = null, $cnt = 0)
    {

        if ($lista == null) {
            $lista = VaktListe::medBrukerIdEtter($this->id, date('Y-m-d'));
            $cnt = count($lista);
        }

        if ($cnt >= 2) {
            for ($i = 1; $i < $cnt; $i++) {
                $vakt_1 = $lista[$i - 1];
                /* @var Vakt $vakt_1 */
                $vakt_2 = $lista[$i];
                /* @var Vakt $vakt_2 */

                $max_time = 604800; // 7 days
                if (Vakt::timeCompare($vakt_1, $vakt_2) < $max_time) {
                    return true;
                }

            }
        }

        return false;
    }

    public function vaktAdvarsel(): bool
    {

        $lista = VaktListe::medBrukerIdEtter($this->id, date('Y-m-d'));
        $cnt = count($lista);

        return $this->harForMangeForstevakter($lista) || $this->harForMangeKjipeVakter($lista) || $this->harVakterTett($lista, $cnt);
    }

    public function advarselArsak() : string {

        $lista = VaktListe::medBrukerIdEtter($this->id, date('Y-m-d'));
        $cnt = count($lista);

        $out = array();

        if($this->harForMangeForstevakter($lista)) {
            $out[] = "Det ser ut til at {$this->getPerson()->getFulltNavn()} skal sitte mange førstevakter.";
        }

        if($this->harForMangeKjipeVakter($lista)) {
            $out[] = "Det ser ut til at {$this->getPerson()->getFulltNavn()} skal sitte mange kjipe vakter.";
        }

        if($this->harVakterTett($lista, $cnt)) {
            $out[] = "Det ser ut til at {$this->getPerson()->getFulltNavn()} har vakter med kort tidsrom mellom enkelte av vaktene.";
        }

        return count($out) > 0 ? implode('<br/>', $out) : '';
    }


    public function getRegisekunderMedSemester($unix = false)
    {
        if ($unix === false) {
            global $_SERVER;
            $unix = $_SERVER['REQUEST_TIME'];
        }
        $sum = 0;
        foreach (ArbeidListe::medBrukerIdSemester($this->id, $unix) as $arbeid) {
            if (!$arbeid->getGodkjent()) {
                continue;
            }
            $sum += $arbeid->getSekunderBrukt();
        }
        return $sum;
    }

    public function getRegitimerigjen()
    {

        return max($this->getPerson()->getRolle()->getRegitimer() - ($this->getRegisekunderMedSemester() / 3600), 0);
    }

    public function getOppgaveTimer()
    {
        $sum = 0;
        foreach (Oppgave::getOppgaverISemesterBeboerId($this->getPerson()->getId()) as $oppgave) {
            /* @var \intern3\Oppgave $oppgave */
            if (!$oppgave->getGodkjent()) {
                $sum += $oppgave->getAnslagTimer();
            }
        }

        return $sum;

    }

    public function getRegiTilBehandling()
    {

        $unix = $_SERVER['REQUEST_TIME'];
        $sum = 0;
        foreach (ArbeidListe::medBrukerIdSemester($this->id, $unix) as $arbeid) {
            /* @var Arbeid $arbeid */
            if ($arbeid->getIntStatus() == 0) {
                $sum += $arbeid->getSekunderBrukt();
            }
        }
        return $sum / (60 * 60);

    }

    public function getDisponibelRegitid()
    {

        return $this->getRegitimerigjen() - $this->getOppgaveTimer();
    }

    public function endreSalt($salt)
    {
        $st = DB::getDB()->prepare('UPDATE bruker SET salt=:salt WHERE id=:id');
        $st->bindParam(':salt', $salt);
        $st->bindParam(':id', $this->id);
        $st->execute();
        $this->salt = $salt;
    }

    public function endrePassord($passord)
    {
        $st = DB::getDB()->prepare('UPDATE bruker SET passord=:password WHERE id=:id');
        $st->bindParam(':password', $passord);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public static function byGlemtToken($token)
    {
        $st = DB::getDB()->prepare('SELECT * FROM bruker WHERE glemt_token=:token');
        $st->bindParam(':token', $token);
        $st->execute();
        return self::init($st);
    }

    public function getResettTid()
    {
        return $this->resett_tid;
    }

    public function getGlemtToken()
    {
        return $this->glemt_token;
    }

}

?>
