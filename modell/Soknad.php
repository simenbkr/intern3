<?php


namespace intern3;


class Soknad
{
    private $id;
    private $innsendt;
    private $navn;
    private $adresse;
    private $epost;
    private $fodselsar;
    private $skole;
    private $studie;
    private $fagbrev;
    private $kompetanse;
    private $kjennskap;
    private $kjenner;
    private $tekst;
    private $bilde;
    private $telefon;


    private static function init(\PDOStatement $st): Soknad
    {

        $rad = $st->fetch();
        if (is_null($rad)) {
            return null;
        }

        $instans = new self();
        $instans->id = $rad['id'];
        $instans->innsendt = $rad['innsendt'];
        $instans->navn = $rad['navn'];
        $instans->adresse = $rad['adresse'];
        $instans->epost = $rad['epost'];
        $instans->fodselsar = $rad['fodselsar'];
        $instans->skole = $rad['skole'];
        $instans->studie = $rad['studie'];
        $instans->fagbrev = $rad['fagbrev'];
        $instans->kompetanse = $rad['kompetanse'];
        $instans->kjennskap = $rad['kjennskap'];
        $instans->kjenner = $rad['kjenner'];
        $instans->tekst = $rad['tekst'];
        $instans->bilde = $rad['bilde'];
        $instans->telefon = $rad['telefon'];

        return $instans;
    }

    public static function medId(int $id): Soknad
    {
        $st = DB::getDB()->prepare('SELECT * FROM soknad WHERE id = :id');
        $st->execute(['id' => $id]);

        return self::init($st);
    }

    public static function alle()
    {
        $st = DB::getDB()->prepare('SELECT * FROM soknad');
        $st->execute();

        $alle = array();

        for ($i = 0; $i < $st->rowCount(); $i++) {
            $alle[] = self::init($st);
        }

        return $alle;
    }

    private function oppdater()
    {
        $st = DB::getDB()->prepare('UPDATE soknad SET navn=:navn,adresse=:adresse,epost=:epost,telefon=:telefon
                                             fodselsar=:fodselsar,skole=:skole,studie=:studie,fagbrev=:fagbrev,
                                             kompetanse=:kompetanse,kjennskap=:kjennskap,kjenner=:kjenner,
                                             tekst=:tekst,bilde=:bilde WHERE id=:id');
        $st->execute(['navn' => $this->navn, 'adresse' => $this->adresse, 'epost' => $this->epost, 'telefon' => $this->telefon, 'fodselsar' => $this->fodselsar,
            'skole' => $this->skole, 'studie' => $this->studie, 'fagbrev' => $this->fagbrev, 'kompetanse' => $this->kompetanse,
            'kjennskap' => $this->kjennskap, 'kjenner' => $this->kjenner, 'tekst' => $this->tekst, 'bilde' => $this->bilde]);
    }


    public function getId()
    {
        return $this->id;
    }

    public function getInnsendt()
    {
        return $this->innsendt;
    }

    public function setInnsendt($innsendt)
    {
        $this->innsendt = $innsendt;
        $this->oppdater();
    }

    public function getNavn()
    {
        return $this->navn;
    }


    public function setNavn($navn)
    {
        $this->navn = $navn;
        $this->oppdater();
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function getTelefon()
    {
        return $this->telefon;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
        $this->oppdater();
    }

    public function getEpost()
    {
        return $this->epost;
    }


    public function setEpost($epost)
    {
        $this->epost = $epost;
        $this->oppdater();
    }

    public function getFodselsar()
    {
        return $this->fodselsar;
    }

    public function setFodselsar($fodselsar)
    {
        $this->fodselsar = $fodselsar;
        $this->oppdater();
    }

    public function getSkole()
    {
        return $this->skole;
    }


    public function setSkole($skole)
    {
        $this->skole = $skole;
        $this->oppdater();
    }


    public function getKjenner()
    {
        return $this->kjenner;
    }

    public function getStudie()
    {
        return $this->studie;
    }


    public function setStudie($studie)
    {
        $this->studie = $studie;
        $this->oppdater();
    }


    public function getFagbrev()
    {
        return $this->fagbrev;
    }

    public function setFagbrev($fagbrev)
    {
        $this->fagbrev = $fagbrev;
        $this->oppdater();
    }

    public function getKompetanse()
    {
        return $this->kompetanse;
    }


    public function setKompetanse($kompetanse)
    {
        $this->kompetanse = $kompetanse;
        $this->oppdater();
    }


    public function getKjennskap()
    {
        return $this->kjennskap;
    }


    public function setKjennskap($kjennskap)
    {
        $this->kjennskap = $kjennskap;
        $this->oppdater();
    }


    public function getTekst()
    {
        return $this->tekst;
    }


    public function setTekst($tekst)
    {
        $this->tekst = $tekst;
        $this->oppdater();
    }


    public function getBilde()
    {
        return $this->bilde;
    }

    public function setBilde($bilde)
    {
        $this->bilde = $bilde;
        $this->oppdater();
    }

}