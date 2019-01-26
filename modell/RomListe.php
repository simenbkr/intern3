<?php

namespace intern3;

class RomListe extends Liste
{
    public static function alle()
    {
        return self::listeFraSql('Rom::medId', 'SELECT id FROM rom ORDER BY navn ASC;');
    }

    public static function alleNavn()
    {
        $st = DB::getDB()->prepare('SELECT navn FROM rom');
        $st->execute();

        $romnummer = array();
        for ($i = 0; $i < $st->rowCount(); $i++) {
            $rad = $st->fetch();
            $romnummer[] = $rad['navn'];
        }

        return $romnummer;

    }

    /*
     * Returnerer en liste over alle bøttekott, storhybler og LPer.
     */
    public static function alleStorhybelRom()
    {
        $st = DB::getDB()->prepare('SELECT id FROM rom WHERE rom.romtype_id IN 
                    (SELECT id as tid FROM romtype WHERE (navn NOT LIKE "%Stor Parhybel%" AND navn NOT LIKE "%Korr%") )
                    AND rom.navn != "060"');
        $st->execute();

        $arr = array();
        while ($rad = $st->fetch()) {
            $arr[$rad['id']] = Rom::medId($rad['id']);
        }

        return $arr;
    }

    /*
    * Returnerer alle korrhybler.
    */
    public static function alleKorrhybler() {
        $st = DB::getDB()->prepare('SELECT id FROM rom WHERE rom.romtype_id IN (SELECT id as tid FROM romtype WHERE navn LIKE "%Korr%")');
        $st->execute();

        $arr = array();
        while ($rad = $st->fetch()) {
            $arr[$rad['id']] = Rom::medId($rad['id']);
        }

        return $arr;
    }

    /*
     * Returnerer en liste over alle Store Parhybler
     */
    public static function alleStoreParhybler()
    {
        $st = DB::getDB()->prepare('SELECT id FROM rom WHERE rom.romtype_id IN (SELECT id as tid FROM romtype WHERE navn LIKE "%Stor Parhybel%")');
        $st->execute();

        $arr = array();
        while ($rad = $st->fetch()) {
            $arr[$rad['id']] = Rom::medId($rad['id']);
        }

        return $arr;
    }

    /*
     * Returnerer alle ledige rom.
     */
    public static function alleLedige()
    {

        $beboerlista = BeboerListe::aktive();
        $romnummer = RomListe::alleNavn();

        foreach ($beboerlista as $beboer) {
            $romnummeret = $beboer->getRom()->getNavn();
            $index = array_search($romnummeret, $romnummer);
            unset($romnummer[$index]);

        }

        $rom = array();

        foreach ($romnummer as $nr) {
            $rom[] = Rom::medNavn($nr);
        }


        return $rom;
    }

    /*
     * Returnerer alle ledige rom tilegnet Storhybelen (dvs alle utenom SPer)
     */
    public static function ledigeStorhybelRom()
    {

        $beboerlista = BeboerListe::aktive();
        $romlista = RomListe::alleStorhybelRom();

        foreach ($beboerlista as $beboer) {
            $romnummeret = $beboer->getRom()->getId();
            if (isset($romlista[$romnummeret])) {
                unset($romlista[$romnummeret]);
            }

        }

        return $romlista;

    }

    /*
     * Returnerer alle ledige SPer.
     */
    public static function ledigeStoreParhybler()
    {

        $beboerlista = BeboerListe::aktive();
        $romlista = RomListe::alleStoreParhybler();

        foreach ($beboerlista as $beboer) {
            $romnummeret = $beboer->getRom()->getId();
            if (isset($romlista[$romnummeret])) {
                unset($romlista[$romnummeret]);
            }

        }

        return $romlista;
    }

    /*
     * Returnerer alle ledige korrhybler.
     */
    public static function ledigeKorrhybler() {
        $beboerlista = BeboerListe::aktive();
        $romlista = RomListe::alleKorrhybler();

        foreach ($beboerlista as $beboer) {
            $romnummeret = $beboer->getRom()->getId();
            if (isset($romlista[$romnummeret])) {
                unset($romlista[$romnummeret]);
            }

        }

        return $romlista;
    }

    public static function fraStorhybelListe($id)
    {

        $st = DB::getDB()->prepare('SELECT rom_id FROM storhybel_rom WHERE storhybel_id=:id');
        $st->bindParam(':id', $id);

        $st->execute();
        $lista = array();
        foreach ($st->fetchAll(\PDO::FETCH_COLUMN) as $rom_id) {
            $lista[$rom_id] = Rom::medId($rom_id);
        }

        return $lista;
    }
}

?>
