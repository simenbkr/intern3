<?php

namespace intern3;

class HelgaGjest
{

    private $id;
    private $navn;
    private $epost;
    private $vertId;
    private $vert;
    private $sendt_epost;
    private $inne;
    private $dag;
    private $aar;
    private $api_nokkel;

    public static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        $instance->epost = $rad['epost'];
        $instance->vertId = $rad['vert'];
        $instance->sendt_epost = $rad['sendt_epost'];
        $instance->inne = $rad['inne'];
        $instance->dag = $rad['dag'];
        $instance->aar = $rad['aar'];
        $instance->api_nokkel = $rad['api_nokkel'];
        return $instance;
    }

    public static function byRow($rad)
    {
        //Legacyshit allerede mann.
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->navn = $rad['navn'];
        $instance->epost = $rad['epost'];
        $instance->vertId = $rad['vert'];
        $instance->sendt_epost = $rad['sendt_epost'];
        $instance->inne = $rad['inne'];
        $instance->dag = $rad['dag'];
        $instance->aar = $rad['aar'];
        $instance->api_nokkel = $rad['api_nokkel'];
        return $instance;
    }

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE id = :id');
        $st->execute(['id' => $id]);

        return self::init($st);
    }


    public function getId()
    {
        return $this->id;
    }

    public function getEpost()
    {
        return $this->epost;
    }

    public function getNokkel()
    {
        return $this->api_nokkel;
    }

    public function getNavn()
    {
        return $this->navn;
    }

    public function getVertId()
    {
        return $this->vertId;
    }

    public function getVert()
    {
        if(is_null($this->vert)) {
            $this->vert = Beboer::medId($this->vertId);
        }
        return $this->vert;
    }

    public function getSendt()
    {
        return $this->sendt_epost;
    }

    public function getInne()
    {
        return $this->inne;
    }

    public function getDag()
    {
        return $this->dag;
    }

    public function getAar()
    {
        return $this->aar;
    }

    public function setDag($dag)
    {
        $this->dag = $dag;
        $this->oppdater();
    }

    public function setEpost($epost)
    {
        $this->epost = $epost;
        $this->oppdater();
    }

    public function setNavn($navn)
    {
        $this->navn = $navn;
        $this->oppdater();
    }

    public function setVert($beboer_id)
    {
        $this->vert = $beboer_id;
        $this->oppdater();
    }

    public function setSendt($sendt)
    {
        $this->sendt_epost = $sendt;
        $this->oppdater();
    }

    public function setInne($inne)
    {
        $this->inne = $inne;
        $this->oppdater();
    }


    private function oppdater()
    {
        $st = DB::getDB()->prepare('UPDATE helgagjest SET epost=:epost, navn=:navn, vert=:vert, inne=:inne,sendt_epost=:sendt_epost WHERE id=:id');
        $st->bindParam(':epost', $this->epost);
        $st->bindParam(':navn', $this->navn);
        $st->bindParam(':vert', $this->vertId);
        $st->bindParam(':inne', $this->inne);
        $st->bindParam(':sendt_epost', $this->sendt_epost);
        $st->bindParam(':id', $this->id);
        $st->execute();
    }

    public static function byId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function addGjest($navn, $epost, $vertId, $dag, $aar, $inne = 0, $sendt_epost = 0)
    {
        $st = DB::getDB()->prepare('INSERT INTO helgagjest (navn, aar, epost, vert, dag ,inne, sendt_epost, api_nokkel)
                            VALUES(:navn, :aar, :epost, :vert, :dag, :inne, :sendt_epost, :nokkel)');
        $nokkel = hash('sha512', Funk::generatePassword(30));
        $null = 0;
        $st->bindParam(':navn', $navn);
        $st->bindParam(':aar', $aar);
        $st->bindParam(':epost', $epost);
        $st->bindParam(':vert', $vertId);
        $st->bindParam(':inne', $null);
        $st->bindParam(':sendt_epost', $null);
        $st->bindParam(':dag', $dag);
        $st->bindParam(':nokkel', $nokkel);
        $st->execute();

        \PHPQRCode\QRcode::png("http://intern.singsaker.no/?a=helga/reg/" . $nokkel,
            PATH . '/www/qrkoder/' . $nokkel . ".png",
            'L',
            4,
            2);

    }

    public static function removeGjest($gjestid)
    {
        $st = DB::getDB()->prepare('DELETE FROM helgagjest WHERE id=:id');
        $st->bindParam(':id', $gjestid);
        $st->execute();
    }

    public static function belongsToBeboer($gjesteid, $beboerid)
    {
        $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE (id=:id AND vert=:vert)');
        $st->bindParam(':id', $gjesteid);
        $st->bindParam(':vert', $beboerid);
        $st->execute();
        return $st->rowCount() > 0;
    }


    public function sendEpost()
    {
        $helga = Helga::medAar($this->aar);
        $beboer = $this->getVert();
        $nettsiden = "http://intern.singsaker.no/qrkoder/" . $this->getNokkel() . ".png";
        $dagen =
        $datoen = date('Y-m-d',
            strtotime($helga->getStartDato() . " +" . $this->getDag() . " days"));
        $tittel = "[SING-HELGA] Du har blitt invitert til HELGA-" . $helga->getAar();
        $beskjed = "<html><body>Hei, " . $this->getNavn() . "! <br/><br/>Du har blitt invitert til "
            . $helga->getTema() . "-" . "HELGA" . " av " . $beboer->getFulltNavn() .
            "<br/><br/>Denne invitasjonen gjelder for $dagen $datoen<br/><br/>
                                            Vi håper du ønsker å ta turen! Din billett for dagen finnes <a href='" . $nettsiden . "'>her</a><br/><br/>
                                            Med vennlig hilsen<br/>HELGA-" . $helga->getAar() . "<br/><br/>
                                            <br/><br/><p>Dette er en automatisert melding. Feil? Vennligst ta kontakt
                                             med data@singsaker.no.</p></body></html>";


        Epost::sendEpost($this->epost, $tittel, $beskjed);
        $this->setSendt(1);
    }

}