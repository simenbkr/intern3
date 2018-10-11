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

        foreach($romnummer as $nr){
            $rom[] = Rom::medNavn($nr);
        }


        return $rom;
    }
}

?>
