<?php

namespace intern3;

class UtvalgVaktsjefGenererCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        list($feilVarighet, $feilEnkelt, $feilPeriode) = array(array(), array(), array());
        do {
            if (!isset($_POST['generer'])) {
                break;
            }
            list($feilVarighet, $feilEnkelt, $feilPeriode) = $this->genererVaktliste();
            if (count($feilVarighet) > 0 || count($feilEnkelt) > 0 || count($feilPeriode) > 0) {
                break;
            }
            Header('Location: ?a=utvalg/vaktsjef/vaktoversikt');
            $this->sendEpostTilVakter();
            exit();
        } while (false);
        $dok = new Visning($this->cd);
        $dok->set('feilVarighet', $feilVarighet);
        $dok->set('feilEnkelt', $feilEnkelt);
        $dok->set('feilPeriode', $feilPeriode);
        $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_generer.php');
    }

    private function sendEpostTilVakter()
    {
        return;

        $mottakere = "";
        foreach (BeboerListe::harVakt() as $beboer) {
            $mottakere .= $beboer->getEpost() . ',';
        }
        $tittel = "[SING-INTERN] Vaktsjef har nå generert nye vakter!";
        $tekst = "<html>(Dette er en automatisert beskjed)<br/><br/>Vaktsjef har nå generert nye vakter. Se når du skal sitte vakt på <a href='https://intern.singsaker.no'>Internsidene</a> for mer informasjon" .
            "<br/><br/></html>";
        Epost::sendEpost($mottakere, $tittel, $tekst);
    }

    private function genererVaktliste()
    {
        $feilVarighet = $this->godkjennVaktlisteVarighet();
        $feilEnkelt = $this->godkjennVaktlisteEnkelt();
        $feilPeriode = $this->godkjennVaktlistePeriode();
        if (count($feilVarighet) == 0 && count($feilEnkelt) == 0 && count($feilPeriode) == 0) {
            DB::getDB()->beginTransaction();
            /* Obs: Ikke noe feilhåndtering på disse stegene ennå. */
            $this->nullstillTabell();
            $this->opprettVakter();
            $this->tildelVakter();
            //$this->opprettOgTildelAnsattVakter();
            /* Ved feil, ->rollback() istedet for ->commit(). */
            DB::getDB()->commit();
        }
        return array($feilVarighet, $feilEnkelt, $feilPeriode);
    }

    private function opprettOgTildelAnsattVakter()
    {
        $varighetDatoStart = strtotime($_POST['varighet_dato_start']);
        $varighetDatoSlutt = strtotime($_POST['varighet_dato_slutt']);
        $dato = $varighetDatoStart;
        $id = Ansatt::getSisteAnsatt()->getBrukerId();
        do {
            for ($type = 1; $type <= 4; $type++) {
                if (($type == 2 || !self::erIHelg($dato)) && self::erITidsrom(
                        $_POST['varighet_type_start'], $varighetDatoStart,
                        $_POST['varighet_type_slutt'], $varighetDatoSlutt,
                        $type, $dato
                    )) {
                    $st = DB::getDB()->prepare('INSERT INTO vakt(bruker_id,vakttype,dato) VALUES(:bruker_id,:vakttype,:dato);');
                    $st->bindParam(':vakttype', $type);
                    $isoDato = date('Y-m-d', $dato);
                    $st->bindParam(':dato', $isoDato);
                    $st->bindParam(':bruker_id', $id);
                    $st->execute();
                }
            }
            $dato = strtotime('midnight + 1 day', $dato);
        } while ($dato <= $varighetDatoSlutt);
    }

    private function godkjennVaktlisteVarighet()
    {
        $feilVarighet = array();
        do {
            if (!isset($_POST['varighet_dato_start']) || !$_POST['varighet_dato_start']) {
                $feilVarighet[] = 'Startdato mangler.';
                break;
            }
            if (!Funk::erDatoGyldigFormat($_POST['varighet_dato_start'])) {
                $feilVarighet[] = 'Startdato må være i formatet åååå-mm-dd.';
                break;
            }
            if (!Funk::finsDato($_POST['varighet_dato_start'])) {
                $feilVarighet[] = 'Startdato er ugyldig, datoen fins ikke.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['varighet_dato_slutt']) || !$_POST['varighet_dato_slutt']) {
                $feilVarighet[] = 'Sluttdato mangler.';
                break;
            }
            if (!Funk::erDatoGyldigFormat($_POST['varighet_dato_start'])) {
                $feilVarighet[] = 'Sluttdato må være i formatet åååå-mm-dd.';
                break;
            }
            if (!Funk::finsDato($_POST['varighet_dato_start'])) {
                $feilVarighet[] = 'Sluttdato er ugyldig, datoen fins ikke.';
                break;
            }
        } while (false);
        if (
            !isset($_POST['varighet_type_start']) || !$_POST['varighet_type_start']
            || !isset($_POST['varighet_type_slutt']) || !$_POST['varighet_type_slutt']
            || !in_array($_POST['varighet_type_start'], array("1", "2", "3", "4"))
            || !in_array($_POST['varighet_type_slutt'], array("1", "2", "3", "4"))
        ) {
            $feilVarighet[] = 'Velg mellom 1., 2., 3. eller 4. vakt.';
        }
        do {
            /*
            if (!isset($_POST['varighet_sikkerhetsmargin']) || !$_POST['varighet_sikkerhetsmargin']) {
              //$feilVarighet[] = 'Sikkerhetsmargin mangler.';
              break;
            }*/
            if (!preg_match("/^[\d]+$/", $_POST['varighet_sikkerhetsmargin']) || $_POST['varighet_sikkerhetsmargin'] < 0) {
                $feilVarighet[] = 'Sikkerhetsmargin må være et positivt heltall eller 0.';
                break;
            }
        } while (false);
        return $feilVarighet;
    }

    private function godkjennVaktlisteEnkelt()
    {
        $feilEnkelt = array();
        $len = isset($_POST['enkeltvakt_type']) && isset($_POST['enkeltvakt_dato'])
        && is_array($_POST['enkeltvakt_type']) && is_array($_POST['enkeltvakt_dato'])
            ? max(count($_POST['enkeltvakt_type']), count($_POST['enkeltvakt_dato']))
            : 0;
        for ($i = 0; $i < $len; $i++) {
            if (!isset($_POST['enkeltvakt_type'][$i]) || !isset($_POST['enkeltvakt_dato'][$i]) || !$_POST['enkeltvakt_dato'][$i]) {
                unset($_POST['enkeltvakt_type'][$i], $_POST['enkeltvakt_dato'][$i]);
                continue;
            }
            do {
                if (!Funk::erDatoGyldigFormat($_POST['enkeltvakt_dato'][$i])) {
                    $feilEnkelt['Dato må være i formatet åååå-mm-dd.'] = 1;
                    break;
                }
                if (!Funk::finsDato($_POST['enkeltvakt_dato'][$i])) {
                    $feilEnkelt['En dato er ugyldig, datoen fins ikke.'] = 1;
                    break;
                }
            } while (false);
            if (
                !isset($_POST['enkeltvakt_type'][$i]) || !$_POST['enkeltvakt_type'][$i]
                || !in_array($_POST['enkeltvakt_type'][$i], array("1", "2", "3", "4"))
            ) {
                $feilEnkelt['Velg mellom 1., 2., 3. eller 4. vakt.'] = 1;
            }
        }
        $_POST['enkeltvakt_type'] = array_values($_POST['enkeltvakt_type']);
        $_POST['enkeltvakt_dato'] = array_values($_POST['enkeltvakt_dato']);
        return array_keys($feilEnkelt);
    }

    private function godkjennVaktlistePeriode()
    {
        $feilPeriode = array();
        $len = isset($_POST['vaktperiode_type_start']) && isset($_POST['vaktperiode_dato_start'])
        && isset($_POST['vaktperiode_type_slutt']) && isset($_POST['vaktperiode_dato_slutt'])
        && is_array($_POST['vaktperiode_type_start']) && is_array($_POST['vaktperiode_dato_start'])
        && is_array($_POST['vaktperiode_type_slutt']) && is_array($_POST['vaktperiode_dato_slutt'])
            ? max(
                count($_POST['vaktperiode_type_start']), count($_POST['vaktperiode_dato_start']),
                count($_POST['vaktperiode_type_slutt']), count($_POST['vaktperiode_dato_slutt'])
            )
            : 0;
        for ($i = 0; $i < $len; $i++) {
            if (!isset($_POST['vaktperiode_type_start'][$i]) || !isset($_POST['vaktperiode_dato_start'][$i]) || !$_POST['vaktperiode_dato_start'][$i] || !isset($_POST['vaktperiode_type_slutt'][$i]) || !isset($_POST['vaktperiode_dato_slutt'][$i]) || !$_POST['vaktperiode_dato_slutt'][$i]) {
                unset($_POST['vaktperiode_type_start'][$i], $_POST['vaktperiode_dato_start'][$i],
                    $_POST['vaktperiode_type_slutt'][$i], $_POST['vaktperiode_dato_slutt'][$i]);
                continue;
            }
            do {
                if (!Funk::erDatoGyldigFormat($_POST['vaktperiode_dato_start'][$i]) || !Funk::erDatoGyldigFormat($_POST['vaktperiode_dato_slutt'][$i])) {
                    $feilPeriode['Dato må være i formatet åååå-mm-dd.'] = 1;
                    break;
                }
                if (!Funk::finsDato($_POST['vaktperiode_dato_start'][$i]) || !Funk::finsDato($_POST['vaktperiode_dato_slutt'][$i])) {
                    $feilPeriode['En dato er ugyldig, datoen fins ikke.'] = 1;
                    break;
                }
            } while (false);
            if (
                !isset($_POST['vaktperiode_type_start'][$i]) || !$_POST['vaktperiode_type_start'][$i]
                || !in_array($_POST['vaktperiode_type_start'][$i], array("1", "2", "3", "4"))
                || !isset($_POST['vaktperiode_type_slutt'][$i]) || !$_POST['vaktperiode_type_slutt'][$i]
                || !in_array($_POST['vaktperiode_type_slutt'][$i], array("1", "2", "3", "4"))
            ) {
                $feilPeriode['Velg mellom 1., 2., 3. eller 4. vakt.'] = 1;
            }
        }
        $_POST['vaktperiode_type_start'] = array_values($_POST['vaktperiode_type_start']);
        $_POST['vaktperiode_dato_start'] = array_values($_POST['vaktperiode_dato_start']);
        $_POST['vaktperiode_type_slutt'] = array_values($_POST['vaktperiode_type_slutt']);
        $_POST['vaktperiode_dato_slutt'] = array_values($_POST['vaktperiode_dato_slutt']);
        return array_keys($feilPeriode);
    }

    private function nullstillTabell()
    {
        DB::getDB()->query('TRUNCATE TABLE vakt;TRUNCATE TABLE vaktbytte;');
    }

    private function opprettVakter()
    {
        $varighetDatoStart = strtotime($_POST['varighet_dato_start']);
        $varighetDatoSlutt = strtotime($_POST['varighet_dato_slutt']);
        $dato = $varighetDatoStart;
        do {
            for ($type = 1; $type <= 4; $type++) {
                if (($type <> 2 || self::erIHelg($dato)) && self::erITidsrom(
                        $_POST['varighet_type_start'], $varighetDatoStart,
                        $_POST['varighet_type_slutt'], $varighetDatoSlutt,
                        $type, $dato
                    )) {
                    $st = DB::getDB()->prepare('INSERT INTO vakt(vakttype,dato,autogenerert) VALUES(:vakttype,:dato,:autogenerert);');
                    $st->bindParam(':vakttype', $type);
                    $isoDato = date('Y-m-d', $dato);
                    $st->bindParam(':dato', $isoDato);
                    $auto = self::skalAutogenereres($type, $dato);
                    $st->bindParam(':autogenerert', $auto);
                    $st->execute();
                }
            }
            $dato = strtotime('midnight + 1 day', $dato);
        } while ($dato <= $varighetDatoSlutt);
    }


    private function tildelVakter2()
    {
        $vanlige_vakter = VaktListe::autogenerertVanligVakt();
        $forstevakter = VaktListe::autogenerertForstevakt();
        $beboere = BeboerListe::harVakt();
        $maks_forstevakter = 0;

        while(count($forstevakter) > 0) {

            $tmp = $beboere;

            while(count($tmp) > 0 ){

                $beboer_indeks = array_rand($tmp);
                $beboeren = $tmp[$beboer_indeks]; /* @var Beboer $beboeren */

                $vakt_indeks = array_rand($forstevakter);
                $vakta = $forstevakter[$vakt_indeks]; /* @var Vakt $vakta */

                if(is_null($vakta)){
                    break;
                }

                $vakta->setBruker($beboeren->getBrukerId());

                array_splice($tmp, $beboer_indeks, 1);
                array_splice($forstevakter, $vakt_indeks, 1);


            }

        }

        while(count($vanlige_vakter) > 0) {

            $tmp = $beboere;

            while(count($tmp) > 0 ){

                $beboer_indeks = array_rand($tmp);
                $beboeren = $tmp[$beboer_indeks]; /* @var Beboer $beboeren */

                $vakt_indeks = array_rand($vanlige_vakter);
                $vakta = $vanlige_vakter[$vakt_indeks]; /* @var Vakt $vakta */

                if(is_null($vakta)){
                    break;
                }

                $vakta->setBruker($beboeren->getBrukerId());

                array_splice($tmp, $beboer_indeks, 1);
                array_splice($vanlige_vakter, $vakt_indeks, 1);


            }

        }






        /*
        while(count($vakter) > 0){
            $temp_beboere = $beboere;

            while(count($temp_beboere) > 0){

                $beboer_indeks = array_rand($temp_beboere);
                $beboeren = $temp_beboere[$beboer_indeks]; /* @var Beboer $beboeren */

        /*
                $forstevakter = $beboeren->getBruker()->antallForstevakter();

                $vakt_indeks = array_rand($vakter);
                $vakta =  $vakter[$vakt_indeks]; /* @var Vakt $vakta */

         /*       if(is_null($vakta)){
                    break;
                }

                if($vakta->getVakttype() == 1 && $forstevakter >= $maks_forstevakter){
                    continue;
                }


                $vakta->setBruker($beboeren->getBrukerId());

                array_splice($temp_beboere, $beboer_indeks, 1);
                array_splice($vakter, $vakt_indeks, 1);
            }

            $maks_forstevakter++;


        } */

        foreach(VaktListe::autogenerert() as $vakt){
            $st = DB::getDB()->prepare('UPDATE vakt SET autogenerert=0 WHERE id=:id;');
            $vaktId = $vakt->getId();
            $st->bindParam(':id', $vaktId);
            $st->execute();
        }

    }


    private function tildelVakter()
    {
        $brukere = array(0 => array(), 1 => array()); // Hvor mange flere vakter hver bruker har igjen under trekkinga
        foreach (BeboerListe::harVakt() as $beboer) {
            $brukere[1][$beboer->getBrukerId()] = Vakt::antallSkalSitteMedBrukerId($beboer->getBrukerId());
            $brukere[0][$beboer->getBrukerId()] = round($brukere[1][$beboer->getBrukerId()] / 3);
            $brukere[1][$beboer->getBrukerId()] -= $brukere[0][$beboer->getBrukerId()];
        }
        $margin = $_POST['varighet_sikkerhetsmargin'];
        $vakter = array(0 => array(), 1 => VaktListe::autogenerert(), 2 => array());
        foreach ($vakter[1] as $indeks => $vakt) {
            if ($vakt->getVakttype() == 1) {
                unset($vakter[1][$indeks]);
                $vakter[0][] = $vakt;
            }
        }
        $vakter[1] = array_values($vakter[1]);
        //print_r($brukere);
        //var_dump(count($brukere), array_sum($brukere), count($vakter));
        foreach (range(0, 1) as $omgang) {
            while (array_sum($brukere[$omgang]) > 0 && $margin < count($vakter[$omgang])) {
                do {
                    $brukerTrekk = array_rand($brukere[$omgang]);
                    //var_dump($netter[$brukerTrekk] . ' ' . max($netter));
                    //break;
                } while ($brukere[$omgang][$brukerTrekk] < max($brukere[$omgang]));
                //} while($brukere[$brukerTrekk] < max($brukere) || $netter[$brukerTrekk] < max($netter) - 1);
                //} while(
                //		pow(max(0, max($brukere) - $brukere[$brukerTrekk]), 2)
                //		+ pow(max(0, max($netter) - $netter[$brukerTrekk]), 2)
                //		> 0
                //);
                do {
                    $vaktTrekk = mt_rand(0, count($vakter[$omgang]) - 1);
                    //var_dump($omgang . ' ' . count($vakter[$omgang]) . ' ' . count($brukere[$omgang]) . ' ' . $vakter[$omgang][$vaktTrekk]->getVakttype());
                    //break;
                } while ($omgang == 0 && $vakter[$omgang][$vaktTrekk]->getVakttype() <> '1');
                $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:brukerId WHERE id=:id;');
                $vaktId = $vakter[$omgang][$vaktTrekk]->getId();
                $st->bindParam(':id', $vaktId);
                $st->bindParam(':brukerId', $brukerTrekk);
                $st->execute();
                $brukere[$omgang][$brukerTrekk]--;
                if ($brukere[$omgang][$brukerTrekk] == 0) {
                    unset($brukere[$omgang][$brukerTrekk]);
                }
                unset($vakter[$omgang][$vaktTrekk]);
                $vakter[$omgang] = array_values($vakter[$omgang]);
            }
            foreach ($brukere[$omgang] as $brukerId => $igjen) {
                $brukere[$omgang][$brukerId] += $igjen;
            }
            $vakter[$omgang + 1] = array_merge($vakter[$omgang + 1], $vakter[$omgang]);
        }
        //print_r($brukere);
        //var_dump(count($brukere), array_sum($brukere), count($vakter));
        foreach ($vakter[1] as $vakt) {
            $st = DB::getDB()->prepare('UPDATE vakt SET autogenerert=0 WHERE id=:id;');
            $vaktId = $vakt->getId();
            $st->bindParam(':id', $vaktId);
            $st->execute();
        }
        //exit();
    }

    private static function erITidsrom($typeStart, $datoStart, $typeSlutt, $datoSlutt, $typeTest, $datoTest)
    {
        /* Sjekk om en tenkt vakt (gitt av type og dato) er i et tidsrom. */
        if ($datoStart > $datoTest || $datoTest > $datoSlutt) {
            /* Dato er ikke i periode. */
            return false;
        }
        if (($datoStart == $datoTest && $typeStart > $typeTest) || ($datoTest == $datoSlutt && $typeTest > $typeSlutt)) {
            /* Vakttype er utenfor periode til tross for at dato er innenfor. */
            return false;
        }
        return true;
    }

    private static function skalAutogenereres($type, $dato)
    {
        for ($i = 0; $i < count($_POST['enkeltvakt_type']); $i++) {
            if ($type == $_POST['enkeltvakt_type'][$i] && date('Y-m-d', $dato) == $_POST['enkeltvakt_dato'][$i]) {
                return false;
            }
        }
        for ($i = 0; $i < count($_POST['vaktperiode_type_start']); $i++) {
            $vaktperiodeDatoStart = strtotime($_POST['vaktperiode_dato_start'][$i]);
            $vaktperiodeDatoSlutt = strtotime($_POST['vaktperiode_dato_slutt'][$i]);
            if (self::erITidsrom(
                $_POST['vaktperiode_type_start'][$i], $vaktperiodeDatoStart,
                $_POST['vaktperiode_type_slutt'][$i], $vaktperiodeDatoSlutt,
                $type, $dato
            )) {
                return false;
            }
        }
        return true;
    }

    private static function erIHelg($dato)
    {
        return date('N', $dato) > 5;
    }
}

?>
