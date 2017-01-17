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

                if (isset($_POST)) {
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
            case 'detaljkryss':
                $beboerId = $this->cd->getSisteArg();
                $beboer = Beboer::medId($beboerId);
                if (is_numeric($beboerId) && $beboer != null) {
                    $beboersKrysseliste = Krysseliste::medBeboerId($beboerId);
                    $dok = new Visning($this->cd);
                    $dok->set('beboer', $beboer);
                    $dok->set('beboersKrysseliste', $beboersKrysseliste);
                    $dok->vis('utvalg_vaktsjef_detaljkryss.php');
                    break;
                }
            case 'krysserapport':
                $dok = new Visning($this->cd);
                if (isset($_POST['settfakturert']) && $_POST['settfakturert'] == 1) {
                    Krysseliste::setPeriodeFakturert();
                    $dok->set('periodeFakturert', 1);
                }
                $beboerListe = BeboerListe::aktive();
                $beboerListe2_0 = array();
                foreach ($beboerListe as $beboer) {
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }
                $krysseListeMonthListe = Krysseliste::getAllIkkeFakturert();
                $sistFakturert = Krysseliste::getSistFakturert();
                $dok->set('sistFakturert', $sistFakturert);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListeMonthListe);
                $dok->vis('utvalg_vaktsjef_krysserapport.php');
                break;
            case 'krysserapportutskrift':
                $beboerListe = BeboerListe::aktive();
                $beboerListe2_0 = array();
                foreach ($beboerListe as $beboer) {
                    $beboerListe2_0[$beboer->getId()] = $beboer;
                }
                $dok = new Visning($this->cd);
                $krysseListeMonthListe = Krysseliste::getAllIkkeFakturert();
                $sistFakturert = Krysseliste::getSistFakturert();

                $dok->set('sistFakturert', $sistFakturert);
                $dok->set('beboerListe', $beboerListe2_0);
                $dok->set('krysseListeMonthListe', $krysseListeMonthListe);
                $dok->vis('utvalg_vaktsjef_krysserapport_utskrift.php');
                break;
            case 'kryss':
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_vaktsjef_kryss.php');
                break;
            case 'vaktstyring_dobbelvakt':
                if (isset($_POST['vaktId_1']) && isset($_POST['dobbelvakt'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vaktId_1 = $post['vaktId_1'];
                    $dobbelvakt = $post['dobbelvakt'];
                    Vakt::setDobbelVakt($vaktId_1, $dobbelvakt);
                }
                break;
            case 'vaktliste_utskrift':
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_vaktsjef_vaktliste_utskrivbar.php');
                break;
            default:
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_vaktsjef.php');
                break;
        }
    }
}

?>
