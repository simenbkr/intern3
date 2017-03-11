<?php

namespace intern3;

class Beboer implements Person
{
    private $id;
    private $brukerId;
    private $fornavn;
    private $mellomnavn;
    private $etternavn;
    private $fodselsdato;
    private $adresse;
    private $postnummer;
    private $telefon;
    private $studieId;
    private $skoleId;
    private $klassetrinn;
    private $alkoholdepositum;
    private $rolleId;
    private $epost;
    private $romhistorikk;
    private $bilde;
    private $ansiennitet;

    // Latskap
    private $studie;
    private $skole;
    private $romId;
    private $rom;
    private $romhistorikkObjekt;
    private $rolle;
    private $vervListe;
    private $utvalgVervListe;
    private $bruker;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM beboer WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT * FROM beboer WHERE bruker_id=:brukerId;');
        $st->bindParam(':brukerId', $brukerId);
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
        $instance->brukerId = $rad['bruker_id'];
        $instance->fornavn = $rad['fornavn'];
        $instance->mellomnavn = $rad['mellomnavn'];
        $instance->etternavn = $rad['etternavn'];
        $instance->fodselsdato = $rad['fodselsdato'];
        $instance->adresse = $rad['adresse'];
        $instance->postnummer = $rad['postnummer'];
        $instance->telefon = $rad['telefon'];
        $instance->studieId = $rad['studie_id'];
        $instance->skoleId = $rad['skole_id'];
        $instance->klassetrinn = $rad['klassetrinn'];
        $instance->alkoholdepositum = $rad['alkoholdepositum'];
        $instance->rolleId = $rad['rolle_id'];
        $instance->epost = $rad['epost'];
        $instance->romhistorikk = $rad['romhistorikk'];
        $instance->bilde = $rad['bilde'];
        $instance->ansiennitet = $rad['ansiennitet'];
        $instance->studie = null;
        $instance->skole = null;
        $instance->romId = null;
        $instance->rom = null;
        $instance->romhistorikkObjekt = null;
        $instance->bruker = null;
        return $instance;
    }

    public function erBeboer()
    {
        $id = $this->getId();
        $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id LIMIT 1');
        $st->bindParam(':id', $id);
        $st->execute();

        $romhistorikk = $st->fetchColumn();

        $dec = json_decode($romhistorikk, true);

        foreach ($dec as $romInfo) {
            if ($romInfo['utflyttet'] == null) {
                return true;
            }
        }
        return false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrukerId()
    {
        return $this->brukerId;
    }

    public function getFornavn()
    {
        return $this->fornavn;
    }

    public function getMellomnavn()
    {
        return $this->mellomnavn;
    }

    public function getEtternavn()
    {
        return $this->etternavn;
    }

    public function getFulltNavn()
    {
        return trim(preg_replace('/[\s]{2,}/', ' ', $this->fornavn . ' ' . $this->mellomnavn . ' ' . $this->etternavn));
    }

    public function getFodselsdato()
    {
        return $this->fodselsdato;
    }

    public function getBilde()
    {
        return $this->bilde;
    }

    public function getFodselsar()
    {
        return substr($this->fodselsdato, 0, 4);
    }

    public function getAlderIAr()
    {
        return date('Y') - substr($this->fodselsdato, 0, 4);
    }

    public function getAlder()
    {
        return $this->getAlderIAr() - (($_SERVER['REQUEST_TIME'] - mktime(0, 0, 0, substr($this->fodselsdato, 5, 2), substr($this->fodselsdato, 8, 2))) < 0 ? 1 : 0);
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getPostnummer()
    {
        return $this->postnummer;
    }

    public function getAnsiennitet()
    {
        return $this->ansiennitet;
    }

    public function getTelefon()
    {
        if (strlen($this->telefon) > 8) {
            return substr($this->telefon, 0, strlen($this->telefon) - 8) . ' ' . substr($this->telefon, -8);
        }
        return $this->telefon;
    }

    public function getKlassetrinn()
    {
        return $this->klassetrinn;
    }

    public function harAlkoholdepositum()
    {
        return $this->alkoholdepositum > 0;
    }

    public function getStudieId()
    {
        return $this->studieId;
    }

    public function getStudie()
    {
        if ($this->studie == null) {
            $this->studie = Studie::medId($this->studieId);
        }
        return $this->studie;
    }

    public function getSkoleId()
    {
        return $this->skoleId;
    }

    public function getSkole()
    {
        if ($this->skole == null) {
            $this->skole = Skole::medId($this->skoleId);
        }
        return $this->skole;
    }

    public function getEpost()
    {
        return $this->epost;
    }

    public function getRolleId()
    {
        return $this->rolleId;
    }

    public function getRolle()
    {
        if ($this->rolle == null) {
            $this->rolle = Rolle::medId($this->rolleId);
        }
        return $this->rolle;
    }

    public function getRomId()
    {
        if ($this->romId == null) {
            $this->romId = $this->getRomhistorikk()->getAktivRomId();
        }
        return $this->romId;
    }

    public function getRom()
    {
        if ($this->rom == null) {
            $this->rom = Rom::medId($this->getRomId());
        }
        return $this->rom;
    }

    public function getRomhistorikk()
    {
        if ($this->romhistorikkObjekt == null) {
            $this->romhistorikkObjekt = Romhistorikk::fraJson($this->romhistorikk);
        }
        return $this->romhistorikkObjekt;
    }

    public function flyttUt()
    {
        $romhistorikken = $this->romhistorikk;
        $som_array = json_decode($romhistorikken, true);
        foreach ($som_array as $key => $historikk) {
            if ($historikk['utflyttet'] == null) {
                $som_array[$key]['utflyttet'] = date('Y-m-d');
            }
        }
        $som_json_igjen = json_encode($som_array);

        $st = DB::getDB()->prepare('UPDATE beboer SET romhistorikk=:historikk WHERE id=:id');
        $id = $this->getId();
        $st->bindParam(':historikk', $som_json_igjen);
        $st->bindParam(':id', $id);
        $st->execute();
    }

    public function getVakterOgVakterSittet(){
        $antall_vakter = 0;
        $antall_sittet = 0;

    }

    public function harVakt()
    {
        return $this->getRolle()->getRegitimer() < 48;
    }

    public function getVakterInnenDogn()
    {
        //Henter ut alle vakter for de neste 24 timene.
        $vakterInnenDogn = array();
        if ($this->harVakt()) {
            foreach (VaktListe::medBrukerId($this->brukerId) as $vakt) {
                if (floor((strtotime($vakt->getDato()) - time()) / (60 * 60 * 24)) <= 1 && !$vakt->erFerdig()) {
                    $vakterInnenDogn[] = $vakt;
                }
            }
        }
        return $vakterInnenDogn;
    }

    public function getVervListe()
    {
        if ($this->vervListe == null) {
            $this->vervListe = VervListe::medBeboerId($this->id);
        }
        return $this->vervListe;
    }

    public function erKjellerMester()
    {
        $st = DB::getDB()->prepare('SELECT * FROM beboer_verv WHERE beboer_id=:id AND verv_id=45');
        $st->bindParam(':id', $this->id);
        $st->execute();
        return $st->rowCount() > 0;
    }

    public function harDataVerv()
    {
        $id = $this->getId();

        $st = DB::getDB()->prepare('SELECT * from beboer_verv WHERE (beboer_id=:beboer_id AND (verv_id=43 OR verv_id=44))');
        $st->bindParam(':beboer_id', $id);
        $st->execute();

        return $st->rowCount() > 0;
    }

    public function harUtvalgVerv()
    {
        return count($this->getUtvalgVervListe()) > 0 || $this->harDataVerv();
        //return true;
    }

    public function getUtvalgVervListe()
    {
        if ($this->utvalgVervListe == null) {
            $this->utvalgVervListe = VervListe::utvalgMedBeboerId($this->id);
        }
        return $this->utvalgVervListe;
    }

    public function getMonthKryss($month = null)
    {
        return Krysseliste::getKryssByMonth($this->id, $month);
    }

    public function getEpostPref()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();

        return $st->fetchAll()[0];
    }

    public function vilHaVaktVarsler()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];

        return $epost_preferanser['snart_vakt'] == 1;
    }

    public function vilHaTildeltVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['tildelt'] == 1;
    }

    public function vilHaBytteGiVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['bytte'] == 1;
    }

    public function vilHaUtleieVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['utleie'] == 1;
    }

    public function vilHaBarvaktVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['barvakt'] == 1;
    }

    public function getBruker()
    {
        if ($this->bruker == null && $this->brukerId != 0) {
            $this->bruker = Bruker::medId($this->brukerId);
        }
        return $this->bruker;
    }

    public function erHelgaGeneral()
    {
        return (Helga::getLatestHelga()) != null ? Helga::getLatestHelga()->erHelgaGeneral($this->id) : false;
    }

    public function toString(){
        return $this->getFulltNavn();
    }
}

?>
