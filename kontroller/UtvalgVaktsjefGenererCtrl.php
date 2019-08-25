<?php

namespace intern3;

class UtvalgVaktsjefGenererCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if ($this->cd->getSisteArg() == 'tom') {
            $this->tomVaktTabell();
            Funk::setSuccess("Vaktlista ble tømt!");
            header('Location: ?a=utvalg/vaktsjef/generer');
            exit();
        }

        if ($this->cd->getSisteArg() == 'tomperiode') {
            $this->tomVaktTabellPeriode(
                date('Y-m-d', strtotime($post['start'])),
                date('Y-m-d', strtotime($post['slutt'])));

            Funk::setSuccess("Tømte perioden fra $post[start]-$post[slutt]");
            header('Location: ?a=utvalg/vaktsjef/generer');
            exit();
        }

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

    private function tomVaktTabellPeriode($start, $slutt)
    {
        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE vaktbytte.vakt_id IN (SELECT id FROM vakt WHERE (dato >= :start AND dato <= :slutt))');
        $st->execute(['start' => $start, 'slutt' => $slutt]);

        $st = DB::getDB()->prepare('DELETE FROM vakt WHERE dato >= :start AND dato <= :slutt');
        $st->execute(['start' => $start, 'slutt' => $slutt]);
    }

    private function tomVaktTabell()
    {
        DB::getDB()->query('TRUNCATE TABLE vakt;TRUNCATE TABLE vaktbytte;');
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
            error_log("Starter å tildele vakter");
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
            if (!preg_match("/^[\d]+$/",
                    $_POST['varighet_sikkerhetsmargin']) || $_POST['varighet_sikkerhetsmargin'] < 0) {
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
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $start = date('Y-m-d', strtotime($post['varighet_dato_start']));
        $slutt = date('Y-m-d', strtotime($post['varighet_dato_slutt']));
        $st = DB::getDB()->prepare('DELETE FROM vakt WHERE (dato >= :start AND dato <= :slutt)');
        $st->execute(['start' => $start, 'slutt' => $slutt]);

        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE vakt_id IN (SELECT id FROM vakt WHERE (dato >= :start AND dato <= :slutt))');
        $st->execute(['start' => $start, 'slutt' => $slutt]);

        foreach (VaktListe::autogenerert() as $vakt) {
            $st = DB::getDB()->prepare('UPDATE vakt SET autogenerert=0 WHERE id=:id;');
            $vaktId = $vakt->getId();
            $st->bindParam(':id', $vaktId);
            $st->execute();
        }
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
                    $auto = self::skalAutogenereres($type, $dato) ? 1 : 0;
                    $st->bindParam(':autogenerert', $auto);
                    $st->execute();
                }
            }
            $dato = strtotime('midnight + 1 day', $dato);
        } while ($dato <= $varighetDatoSlutt);
    }

    private function tildelVakter()
    {
        /*
         * Funksjon for å tildele vakter på en rettferdig måte. Vakter som autogenereres står for ca 2/3 av vaktene.
         * Funksjonen etterstreber å tildele kjipe vakter jevnt, og de andre vaktene vilkårlig.
         * Kjipe vakter er
         * - Førstevakter,
         * - Lørdagsvakter,
         * - 3. og 4. vakt på fredager og
         * - 2. og 3. vakt på søndager.
         *
         */

        $oversikt = array();
        $week_seconds = 604800;
        $day_secs = 86400;
        $vanlige_vakter = VaktListe::autogenerertIkkeKjipVakt();
        $kjipe_vakter = VaktListe::autogenerertKjipVakt();
        $forstevakter = VaktListe::autogenerertForstevakt();
        $fullvakt = BeboerListe::fullVakt();
        $halvvakt = BeboerListe::halvVakt();
        $maks_kjipe = ceil(Vakt::antallKjipeAutogenererte() / (count($fullvakt) + count($halvvakt))) + 1;
        $margin = $_POST['varighet_sikkerhetsmargin'];


        /*
         * Sett opp fordeling til bruk i hovedloop senere.
         */

        $semester = Funk::generateSemesterString(date('Y-m-d', strtotime($_POST['varighet_dato_slutt'])));
        $host = strpos($semester, "host");
        $fordeling = array();
        foreach (RolleListe::alle() as $rolle) {
            /* @var Rolle $rolle */
            if ($rolle->getVakterNow() > 0) {
                if ($host) {
                    $antall = $rolle->getVakterH();
                } else {
                    $antall = $rolle->getVakterV();
                }
                $antall = intval(floor($antall * 2 / 3));

                if ($rolle->getNavn() === 'Full vakt') {
                    $fordeling[$antall] = $fullvakt;
                } else {
                    $fordeling[$antall] = $halvvakt;
                }
            }
        }
        /*
         * Fordeling ser nå omtrent slik ut:
         * $fordeling = array(
         *                      6 => array med beboerene som skal ha full vakt.
         *                      4 => array med beboerene som skal ha halv vakt.
         * );
         */

        /*
         * Hovedloop. Vi deler først ut førstevakter, deretter de andre vaktene - i to "runder".
         */

        foreach (array($forstevakter, $kjipe_vakter, $vanlige_vakter) as $vakter) {

            while (count($vakter) > floor($margin)) {

                foreach ($fordeling as $antall => $beboere) {

                    $tmp = $beboere;

                    while (count($tmp) > 0) {

                        /*
                         * Velg én tilfeldig beboer.
                         */

                        $beboer_indeks = array_rand($tmp);
                        $beboeren = $tmp[$beboer_indeks];
                        /* @var Beboer $beboeren */

                        /*
                         * Sjekk at beboeren kan sitte flere vakter. Hvis ikke fjernes den fra denne omgangen.
                         */

                        if ($beboeren->getBruker()->antallVakterErOppsatt() >= $antall
                            || $beboeren->getBruker()->antallVakterErOppsatt() >= $beboeren->getBruker()->antallVakterSkalSitte()) {

                            if ($beboeren->getBruker()->antallVakterErOppsatt() >= $antall++ || true) {
                                array_splice($beboere, $beboer_indeks, 1);
                                array_splice($tmp, $beboer_indeks, 1);
                                continue;
                            }
                        }

                        /*
                         * Velg én tilfeldig vakt av de gjenværende vaktene.
                         */

                        $vakt_indeks = array_rand($vakter);
                        $vakta = $vakter[$vakt_indeks];
                        /* @var Vakt $vakta */

                        /*
                         * Dersom det ikke er noen flere vakter, vil array_rand returnere null. Derfor breaker vi om dette
                         * skjer, fordi da er det på tide med neste omgang.
                         * Hvis beboeren allerede har en vakt denne datoen, hopper vi til neste runde.
                         */

                        if (is_null($vakta)) {
                            break;
                        }

                        /*
                         * Sjekk om beboeren har en vakt +/- 5 dager. Hvis det er tilfelle, forsøk å trekke på nytt.
                         */

                        $i = 0;
                        while ($i++ < count($vakter)) {
                            $flag = true;

                            foreach ($oversikt[$beboeren->getBrukerId()] as $vakt) {
                                if (Vakt::timeCompare($vakt, $vakta) < 7 * $day_secs) {
                                    $flag = false;
                                }
                            }

                            if ($flag) {
                                break;
                            }

                            $vakt_indeks = array_rand($vakter);
                            $vakta = $vakter[$vakt_indeks];

                        }

                        $i = 0;
                        while ($i++ < count($vakter)) {
                            $flag = 0;
                            foreach ($oversikt[$beboeren->getBrukerId()] as $vakt) {
                                if (Vakt::timeCompare($vakt, $vakta) < 3 * $week_seconds) {
                                    $flag++;
                                }
                            }

                            if ($flag < 3) {
                                break;
                            }

                            $vakt_indeks = array_rand($vakter);
                            $vakta = $vakter[$vakt_indeks];

                        }


                        /*
                         * Beboeren velges til denne vakta. Vakta fjernes fra lista, mens beboeren blir fjerna fra denne
                         * runden.
                         */

                        $oversikt[$beboeren->getBrukerId()][] = $vakta;
                        $vakta->setBruker($beboeren->getBrukerId());

                        array_splice($tmp, $beboer_indeks, 1);
                        array_splice($vakter, $vakt_indeks, 1);

                        /*
                         * Sjekker om marginen er nådd. Vi deler på to fordi det er to runder -
                         * en runde for førstevakt og en for vanlig vakt.
                         */

                        if (count($vakter) == floor($margin)) {
                            break;
                        }
                    }
                }
            }
        }


        foreach (VaktListe::autogenerert() as $vakt) {
            $st = DB::getDB()->prepare('UPDATE vakt SET autogenerert=0 WHERE id=:id;');
            $vaktId = $vakt->getId();
            $st->bindParam(':id', $vaktId);
            $st->execute();
        }

    }


    private function tildelVakter2()
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
        //error_log("$typeStart-$datoStart -> $typeSlutt-$datoSlutt: $typeTest-$datoTest");
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
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        for ($i = 0; $i < count($post['enkeltvakt_type']); $i++) {
            if ($type == $post['enkeltvakt_type'][$i] && date('Y-m-d', $dato) == $post['enkeltvakt_dato'][$i]) {
                error_log("FALSE: $type-$dato");
                return false;
            }
        }
        for ($i = 0; $i < count($post['vaktperiode_type_start']); $i++) {
            $vaktperiodeDatoStart = strtotime($_POST['vaktperiode_dato_start'][$i]);
            $vaktperiodeDatoSlutt = strtotime($_POST['vaktperiode_dato_slutt'][$i]);
            if (self::erITidsrom(
                $post['vaktperiode_type_start'][$i], $vaktperiodeDatoStart,
                $post['vaktperiode_type_slutt'][$i], $vaktperiodeDatoSlutt,
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