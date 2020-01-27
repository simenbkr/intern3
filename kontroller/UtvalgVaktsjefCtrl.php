<?php

namespace intern3;

use intern3\Krysseliste\Kryss;

class UtvalgVaktsjefCtrl extends AbstraktCtrl
{

    private function endreVaktAntall()
    {

        $visning = new Visning($this->cd);
        $sisteArg = $this->cd->getSisteArg();
        $options = Funk::genNextSemsterStrings();

        if (($beboer = Beboer::medId($sisteArg)) == null) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $antall = $post['antall'];
            $semester = $post['semester'];

            if (!in_array($semester, $options)) {
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Det ser ut til at du har valgt et ugyldig semester!";
                return;
            }

            if (($vaktantall = VaktAntall::medIdSemester($beboer->getBrukerId(), $semester)) != null) {
                $vaktantall->endreAntall($antall);
            } else {
                VaktAntall::add($beboer->getBrukerId(), $semester, $antall);
            }

            $_SESSION['success'] = 1;
            $_SESSION['msg'] = "Du endret antall vakter for " . $beboer->getFulltNavn() . " til " . $antall;
            header('Location: ?a=utvalg/vaktsjef/vaktoversikt');
            exit();
        }

        $vakter = VaktListe::medBrukerId($beboer->getBrukerId());
        $visning->set('vakter', $vakter);
        $visning->set('options', $options);
        $visning->set('beboer', $beboer);
        $visning->vis('utvalg/vaktsjef/utvalg_vaktsjef_vaktoversikt_endre.php');
        exit();
    }

    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
            case 'setvar':
                $_SESSION['semester'] = "var";
                break;
            case 'sethost';
                $_SESSION['semester'] = "host";
                break;
            case 'generer':
                $valgtCtrl = new UtvalgVaktsjefGenererCtrl($this->cd->skiftArg());
                $valgtCtrl->bestemHandling();
                break;
            case 'opprett':
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $dato = strtotime($post['start']);
                $slutt = strtotime($post['slutt']);
                $vakttype = $post['options'];

                while ($dato < $slutt) {
                    $isodato = date('Y-m-d', $dato);
                    $st = DB::getDB()->prepare('INSERT INTO vakt(vakttype,dato) VALUES(:vakttype, :dato)');
                    $st->execute(['vakttype' => $vakttype, 'dato' => $isodato]);
                    $dato = strtotime('midnight + 1 day', $dato);
                }
                Funk::setSuccess("La til blanke {$vakttype}. vakter {$post['start']}-{$post['slutt']}");
                header('Location: ?a=utvalg/vaktsjef/vaktstyring');
                exit();
            case 'publiser':
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $vaktliste = VaktListe::listeEtterDatoType($post['start'], $post['slutt'], $post['options']);
                $slipp = date('Y-m-d H:i:s', strtotime($post['slipp']));

                foreach ($vaktliste as $vakt) {
                    $vakt->toggleByttemarked($slipp);
                }
                Funk::setSuccess('Innsending fullført!');
                header('Location: ?a=utvalg/vaktsjef/vaktstyring');
                break;
            case 'vaktoversikt':

                if ($aktueltArg != $this->cd->getSisteArg()) {
                    $this->endreVaktAntall();
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    //'endreVakt=1&hosthalv=' + host_halv + "&vaarhalv=" + vaar_halv + "&hosthel=" + host_hel + "&vaarhel=" + vaar_hel,
                    // [endreVakt] => 1 [hosthalv] => 5 [vaarhalv] => 6 [hosthel] => 1 [vaarhel] => 9
                    if (isset($post['endreVakt']) && $post['endreVakt'] == 1 && isset($post['hosthalv']) && is_numeric($post['hosthalv'])
                        && isset($post['vaarhalv']) && is_numeric($post['vaarhalv']) && isset($post['hosthel']) && is_numeric($post['hosthel'])
                        && isset($post['vaarhel']) && is_numeric($post['vaarhel'])
                    ) {
                        $host_halv = $post['hosthalv'];
                        $vaar_halv = $post['vaarhalv'];
                        $host_hel = $post['hosthel'];
                        $vaar_hel = $post['vaarhel'];
                        $st = DB::getDB()->prepare('UPDATE rolle SET vakter_h=:host_halv, vakter_v=:vaar_halv WHERE id=1');
                        $st->bindParam(':host_halv', $host_halv);
                        $st->bindParam(':vaar_halv', $vaar_halv);
                        $st->execute();
                        $st2 = DB::getDB()->prepare('UPDATE rolle SET vakter_h=:hosthel, vakter_v=:vaarhel WHERE id=2');
                        $st2->bindParam(':hosthel', $host_hel);
                        $st2->bindParam(':vaarhel', $vaar_hel);
                        $st2->execute();
                    }
                }

                $beboerListe = BeboerListe::harVakt();
                $antallVakter = Vakt::antallVakter();
                $antallUfordelte = Vakt::antallUfordelte();
                $antallUbekreftet = Vakt::antallUbekreftet();
                $roller = RolleListe::alle();
                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe);
                $dok->set('antallVakter', $antallVakter);
                $dok->set('antallUfordelte', $antallUfordelte);
                $dok->set('antallUbekreftet', $antallUbekreftet);
                $dok->set('roller', $roller);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_vaktoversikt.php');
                break;
            case 'vaktstyring':
                $beboerListe = BeboerListe::harVakt();
                $torild = Ansatt::getSisteAnsatt();
                $dok = new Visning($this->cd);
                $dok->set('torild', $torild);
                $dok->set('denneUka', @date('W'));
                $dok->set('detteAret', @date('Y'));
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_vaktstyring.php');
                break;
            case 'vaktstyring_modal':
                $beboerListe = BeboerListe::harVakt();
                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_vaktstyring_modal.php');
                break;
            case 'vaktstyring_settvakt':
                if (isset($_POST['beboerId']) && isset($_POST['vaktId_1'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboerId = $post['beboerId'];
                    $vaktId_1 = $post['vaktId_1'];
                    $beboer = Beboer::medId($beboerId);
                    if ($beboer == null) {
                        exit();
                    } else {
                        $brukerId = $beboer->getBrukerId();
                        Vakt::settVakt($brukerId, $vaktId_1);
                    }
                }
                break;
            case 'vaktstyring_lagvakt':
                if (isset($_POST['modalId'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $modalId = $post['modalId'];
                    $vakttype = substr($modalId, -1);
                    $dato = substr($modalId, 6, -2);
                    Vakt::lagVakt($vakttype, $dato);
                }
                break;
            case 'vaktstyring_byttvakt':
                if (isset($_POST['vaktId_1']) && isset($_POST['vaktId_2'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    $vaktId_2 = $post['vaktId_2'];
                    Vakt::byttVakt($vaktId_1, $vaktId_2);
                    exit;
                } elseif (isset($_POST['beboerId'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboerId = $post['beboerId'];
                    $beboer = Beboer::medId($beboerId);
                    if ($beboer == null) {
                        exit();
                    } else {
                        $dok = new Visning($this->cd);
                        $dok->set('visFerdig', 1);
                        $dok->set('beboer', $beboer);
                        $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_vaktstyring_byttvakt.php');
                    }
                }
                break;
            case 'vaktstyring_dobbelvakt':
                if (isset($_POST['vaktId_1'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    //Vakt::setDobbelvakt($vaktId_1);
                    Vakt::medId($vaktId_1)->endreDobbelvakt();
                }
                break;
            case 'vaktstyring_straffevakt':
                if (isset($_POST['vaktId_1'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    Vakt::setStraffevakt($vaktId_1);
                }
                break;
            case 'vaktstyring_slettvakt':
                if (isset($_POST['vaktId_1'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    Vakt::slettVakt($vaktId_1);
                    exit();
                }
                break;
            case 'vaktstyring_torildvakt':
                if (isset($_POST['vaktId_1'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    //Vakt::settVakt(443, $vaktId_1); // 443 Torild
                    //Vakt::settVakt(Ansatt::getSisteAnsatt()->getBrukerId(), $vaktId_1);
                    $st = DB::getDB()->prepare('DELETE FROM vakt WHERE id=:id');
                    $st->bindParam(':id', $vaktId_1);
                    $st->execute();
                    exit();
                }
                break;
            case 'vaktstyring_byttemarked':
                if (isset($_POST['vaktId'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vakten = Vakt::medId($post['vaktId']);
                    if ($vakten != null) {
                        $vakten->toggleByttemarked();
                    }
                }
                break;
            case 'ukerapport':
                $Uke = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
                $Aar = $this->cd->getArg($this->cd->getAktuellArgPos() + 2);
                if (is_numeric($Uke)) {
                    $krysseinstans = new Journal($Uke, $Aar);
                } else {
                    $krysseinstans = new Journal();
                }
                $dok = new Visning($this->cd);
                $dok->set('krysseting', $krysseinstans->getUkeKryss());
                $dok->set('journal', $krysseinstans->getKrysseInfo());
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_ukesrapport.php');
                break;
            case 'ukerapport_tabell':
                $Uke = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
                $Aar = $this->cd->getArg($this->cd->getAktuellArgPos() + 2);
                if (is_numeric($Uke)) {
                    $krysseinstans = new Journal($Uke, $Aar);
                } else {
                    $krysseinstans = new Journal();
                }
                $dok = new Visning($this->cd);
                $periode_start = $krysseinstans->getPeriode()['fra'];
                $drikke = Drikke::alle();
                $dok->set('drikke', $drikke);
                $dok->set('periode_start', $periode_start);
                $dok->set('krysseting', $krysseinstans->getUkeKryss());
                $dok->set('journal', $krysseinstans->getKrysseInfo());
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_ukesrapport_tabell.php');
                break;
            case 'detaljkryss':
                $beboerId = $this->cd->getSisteArg();
                $beboer = Beboer::medId($beboerId);
                if (is_numeric($beboerId) && $beboer != null) {
                    $beboersKrysseliste = Krysseliste::medBeboerId($beboerId);
                    $dok = new Visning($this->cd);
                    $dok->set('beboer', $beboer);
                    $dok->set('beboersKrysseliste', $beboersKrysseliste);
                    $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_detaljkryss.php');
                }
                break;

            case 'krysserapport_csv':
                $perioden = Krysseliste::periodeTilCSV();
                header("Content-type: text/csv");
                $ts = date('Y-m-d_H_i_s');
                header("Content-Disposition: attachment; filename=krysserapport-$ts.csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                $output = fopen('php://output', 'wb');
                foreach ($perioden as $line) {
                    fputcsv($output, $line);
                }

                fclose($output);
                break;
            case 'krysserapport':
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $dok = new Visning($this->cd);
                if (isset($_POST['settfakturert']) && $_POST['settfakturert'] == 1) {
                    $perioden = base64_encode(Krysseliste::CSVtoStr(Krysseliste::periodeTilCSV()));
                    Krysseliste::setPeriodeFakturert();
                    Funk::setSuccess("Perioden ble fakturert! Du skal ha mottatt en CSV-fil som omhandler den aktuelle perioden.");
                    Epost::sendEpost('data@singsaker.no', '[SING-INTERN] Kryssedata for nåværende periode.', $perioden);
                    exit();
                } elseif (isset($post['settfakturert']) && $post['settfakturert'] == 2 && isset($post['dato'])) {
                    $datoen = date('Y-m-d H:i:s', strtotime($post['dato']));
                    $now = strtotime(date('Y-m-d H:i:s'));
                    if (strtotime($post['dato']) > $now) {
                        Funk::setError("Kan ikke fakturere i fremtiden!");
                        exit();
                    }
                    $forrigeFaktura = Krysseliste::getSistFakturert();

                    if ($forrigeFaktura > $datoen) {
                        Funk::setError("Kan ikke fakturere over én eller flere perioder.");
                        exit();
                    }

                    Krysseliste::fakturerOppTil($datoen);
                }

                $beboerListe = BeboerListe::aktive();
                $beboerListe2_0 = array();
                foreach ($beboerListe as $beboer) {
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }
                $drikke = Drikke::alle();
                $krysseListeMonthListe = Krysseliste::getAllIkkeFakturert();
                $sistFakturert = Krysseliste::getSistFakturert();
                $dok->set('sistFakturert', $sistFakturert);
                $dok->set('drikke', $drikke);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListeMonthListe);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_krysserapport.php');
                break;
            case 'krysserapportutskrift':

                $periode = $this->cd->getSisteArg();

                $drikke = Drikke::alle();
                $beboerListe = BeboerListe::aktive();
                $beboerListe2_0 = array();
                foreach ($beboerListe as $beboer) {
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }
                $dok = new Visning($this->cd);

                $krysseListeMonthListe = Krysseliste::getAllIkkeFakturert();
                $sistFakturert = Krysseliste::getSistFakturert();

                if ('krysserapportutskrift' !== $periode) {
                    $ts = strtotime($periode);
                    if ($ts > strtotime($sistFakturert)) {
                        $krysseListeMonthListe = Krysseliste::getAllIkkeFakturertFDato(date('Y-m-d H:i:s', $ts));
                        $dok->set('dato', date('Y-m-d H:i:s', $ts));
                    }
                }

                $dok->set('sistFakturert', $sistFakturert);
                $dok->set('drikke', $drikke);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListeMonthListe);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_krysserapport_utskrift.php');
                break;

            case 'kryss':

                $alleKryss = array();

                foreach (BeboerListe::aktive() as $beboer) {
                    $alleKryss[$beboer->getFulltNavn()] = Krysseliste::medBeboerId($beboer->getId());
                }

                $dok = new Visning($this->cd);
                $dok->set('alleKryss', $alleKryss);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_kryss.php');
                break;
            case 'drekkefolge':
                // Endre rekkefølge. 'drekke' er slang for drikke. Det er morsomt, okay.
                if(!is_null(($drikken = Drikke::medId($this->cd->getSisteArg())))) {
                    $drikken->setForst();
                    print "Nice";
                    Funk::setSuccess("Satte {$drikken->getNavn()} til å være først i Journalen.");
                }
                break;
            case 'endre_drikke':
                if (($drikken = Drikke::medId($this->cd->getSisteArg())) != null) {

                    if (isset($_POST) && count($_POST) > 0) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if (isset($post['pris']) && isset($post['farge'])) {
                            //Har bestemt at man ikke kan endre navn på drikker. Det er bedre å sette de som inaktive
                            //fordi da skaper man mindre forvirring for brukere (trolig).
                            $st = DB::getDB()->prepare('UPDATE drikke SET pris=:pris, farge=:farge,
                                                                    aktiv=:aktiv, kommentar=:kommentar WHERE id=:id');

                            $aktiv = isset($post['aktiv']) && $post['aktiv'] == 'on' ? 1 : 0;
                            $st->execute([
                                'pris' => $post['pris'],
                                'farge' => $post['farge'],
                                'kommentar' => $post['kommentar'],
                                'aktiv' => $aktiv,
                                'id' => $this->cd->getSisteArg()
                            ]);

                            Funk::setSuccess('Du oppdaterte en drikke!');
                        }
                        header('Location: ?a=utvalg/vaktsjef/drikke');
                        exit();
                    }
                    $dok = new Visning($this->cd);
                    $dok->set('drikka', $drikken);
                    $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_endre_drikke.php');
                    break;
                }
            case 'drikke':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (count($post) > 0 && isset($post['navn']) && isset($post['pris']) && isset($post['farge']) && isset($post['drikke1'])
                    ) {
                        $st = DB::getDB()->prepare('INSERT INTO drikke (navn,pris,aktiv,farge,kommentar) 
                                                              VALUES(:navn,:pris,1,:farge,:kommentar)');

                        $st->execute([
                           'navn' => $post['navn'],
                           'pris' => $post['pris'],
                           'farge' => $post['farge'],
                           'kommentar' => $post['kommentar']
                        ]);

                        Funk::setSuccess('Du la til en ny drikke!');

                        $drikkeId = DB::getDB()->lastInsertId();
                        $denne_vakta = AltJournal::getLatest();
                        $aktuelt_krysseobjekt = $denne_vakta->getStatusByDrikkeId($post['type']);
                        if ($aktuelt_krysseobjekt == null) {
                            $obj = array(
                                'drikkeId' => $drikkeId,
                                'mottatt' => 0,
                                'avlevert' => 0,
                                'pafyll' => 0,
                                'utavskap' => 0
                            );
                            $denne_vakta->updateObject($obj);
                            $denne_vakta->calcAvlevert();

                        }
                    }
                }
                $drikke = Drikke::alle();
                $dok = new Visning($this->cd);
                $dok->set('drikke', $drikke);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_drikke.php');
                break;
            case 'vaktliste_utskrift':
                $dok = new Visning($this->cd);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_vaktliste_utskrivbar.php');
                break;
            case 'krysserapport_historie':
                $dok = new Visning($this->cd);

                $st = DB::getDB()->query('SELECT dato FROM fakturert');
                $rows = $st->fetchAll();
                $datoer = array();

                foreach ($rows as $row) {
                    $datoer[] = $row['dato'];
                }

                $dok->set('datoer', $datoer);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_krysserapport_historie.php');
                break;
            case 'krysserapport_historie_tabell':
                $lastArg = $this->cd->getSisteArg();
                $pos = $this->cd->getAktuellArgPos();
                $alleArgs = $this->cd->getAllArgs();
                if ($lastArg != $aktueltArg && count($alleArgs) != $pos) {
                    $fra = $alleArgs[$pos + 1];
                    $til = $alleArgs[$pos + 2];
                } else {
                    return;
                }

                $dok = new Visning($this->cd);

                $drikke = Drikke::alle();
                $beboerListe = BeboerListe::alle();
                $beboerListe2_0 = array();
                foreach ($beboerListe as $beboer) {
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }

                //$krysseListe = Krysseliste::getKryssByPeriode('2017-01-01', '2018-01-01');
                $krysseListe = Krysseliste::getKryssByPeriode($fra, $til);
                $sistFakturert = Krysseliste::getSistFakturert();

                $dok->set('sistFakturert', $sistFakturert);
                $dok->set('drikke', $drikke);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListe);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef_krysserapport_historie_tabell.php');
                break;
            default:
                $dok = new Visning($this->cd);
                $dok->vis('utvalg/vaktsjef/utvalg_vaktsjef.php');
                break;
        }
    }
}

?>