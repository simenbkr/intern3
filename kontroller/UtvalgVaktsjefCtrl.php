<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
            case 'generer':
                $valgtCtrl = new UtvalgVaktsjefGenererCtrl($this->cd->skiftArg());
                $valgtCtrl->bestemHandling();
                break;
            case 'vaktoversikt':
                $beboerListe = BeboerListe::harVakt();
                $antallVakter = Vakt::antallVakter();
                $antallUfordelte = Vakt::antallUfordelte();
                $antallUbekreftet = Vakt::antallUbekreftet();
                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe);
                $dok->set('antallVakter', $antallVakter);
                $dok->set('antallUfordelte', $antallUfordelte);
                $dok->set('antallUbekreftet', $antallUbekreftet);
                $dok->vis('utvalg_vaktsjef_vaktoversikt.php');
                break;
            case 'vaktstyring':
                if (isset($_POST['vaktId_1']) && isset($_POST['vaktId_2'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    $vaktId_2 = $post['vaktId_2'];
                    Vakt::byttVakt($vaktId_1, $vaktId_2);
                    $page = '?a=utvalg/vakstsjef/vaktstyring';
                    header('Location: ' . $page, true, 303);
                    exit;
                }
                $beboerListe = BeboerListe::harVakt();
                $torild = Ansatt::medId(1);
                $dok = new Visning($this->cd);
                $dok->set('torild', $torild);
                $dok->set('denneUka', @date('W'));
                $dok->set('detteAret', @date('Y'));
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg_vaktsjef_vaktstyring.php');
                break;
            case 'vaktstyring_settvakt':
                if (isset($_POST['beboerId'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboerId = $post['beboerId'];
                    $beboer = Beboer::medId($beboerId);
                    if ($beboer == NULL) {
                        exit();
                    } else {
                        $dok = new Visning($this->cd);
                        $dok->set('visFerdig', 1);
                        $dok->set('beboer', $beboer);
                        $dok->vis('utvalg_vaktsjef_vaktstyring_settvakt.php');
                    }
                }
                break;
            case 'vaktstyring_settvakt_lagre':
                if (isset($_POST['$brukerId']) && isset($_POST['vaktId_1'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboerId = $post['beboerId'];
                    $vaktId_1 = $post['vaktId_1'];
                    $beboer = Beboer::medId($beboerId);
                    if ($beboer == NULL) {
                        exit();
                    } else {
                        $brukerId = $beboer->getBrukerId();
                        Vakt::settVakt($brukerId, $vaktId_1);
                    }
                }
                break;
            case 'vaktstyring_modal':
                $beboerListe = BeboerListe::harVakt();
                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg_vaktsjef_vaktstyring_modal.php');
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
                $dok->vis('utvalg_vaktsjef_ukesrapport.php');
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
                $dok->set('krysseting', $krysseinstans->getUkeKryss());
                $dok->set('journal', $krysseinstans->getKrysseInfo());
                $dok->vis('utvalg_vaktsjef_ukesrapport_tabell.php');
                break;
            case 'krysserapport':
                $beboerListe = BeboerListe::aktive();
                $beboerListe2_0 = array();
                $krysseListeMonthListe = array();
                foreach ($beboerListe as $beboer) {
                    if (!$beboer->harAlkoholdepositum()) {
                        continue;
                    }
                    $krysseListeMonthListe[$beboer->getId()] = Krysseliste::getKryssByMonth($beboer->getId());
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }

                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListeMonthListe);
                $dok->vis('utvalg_vaktsjef_krysserapport.php');
                break;
            case 'krysserapportutskrift':
                $beboerListe = BeboerListe::aktive();
                $beboerListe2_0 = array();
                $krysseListeMonthListe = array();
                foreach ($beboerListe as $beboer) {
                    if (!$beboer->harAlkoholdepositum()) {
                        continue;
                    }
                    $krysseListeMonthListe[$beboer->getId()] = Krysseliste::getKryssByMonth($beboer->getId());
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }

                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListeMonthListe);
                $dok->vis('utvalg_vaktsjef_krysserapport_utskrift.php');
                break;
            case 'vaktstyring_dobbelvakt':
                if (isset($_POST['vaktId_1']) && isset($_POST['dobbelvakt'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    $dobbelvakt = $post['dobbelvakt'];
                    Vakt::setDobbelVakt($vaktId_1, $dobbelvakt);
                }
                break;
            default:
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_vaktsjef.php');
                break;
        }
    }
}

?>
