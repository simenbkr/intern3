<?php

namespace intern3;

class UtvalgSekretarCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'apmandsverv') {
            if (isset($_POST)) {
                if (isset($_POST['fjern']) && isset($_POST['verv'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboerId = $post['fjern'];
                    $vervId = $post['verv'];
                    Verv::deleteBeboerFromVerv($beboerId, $vervId);
                } else if (isset($_POST['vervet'])) {
                    setcookie('du', 'posta');
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $postet = explode('&', $post['vervet']);
                    $beboerId = $postet[0];
                    if ($beboerId != 0) {
                        $vervId = $postet[1];
                        Verv::updateVerv($beboerId, $vervId);
                        $page = '?a=utvalg/sekretar/apmandsverv';
                        header('Location: ' . $page, true, 303);
                        exit;
                    }
                }
            }
            $beboerListe = BeboerListe::aktive();
            $vervListe = VervListe::alle();
            $utvalg = Verv::erUtvalg();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vervListe', $vervListe);
            $dok->set('utvalg', $utvalg);
            $dok->vis('utvalg_sekretar_apmandsverv.php');
        } else if ($aktueltArg == 'apmandsverv_modal') {
            $beboerListe = BeboerListe::aktive();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->vis('utvalg_sekretar_apmandsverv_modal.php');
        } else if ($aktueltArg == 'utvalgsverv') {
            if (isset($_POST)) {
                if (isset($_POST['fjern']) && isset($_POST['verv'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboerId = $post['fjern'];
                    $vervId = $post['verv'];
                    Verv::deleteBeboerFromVerv($beboerId, $vervId);
                } else if (isset($_POST['leggtil'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $vervId = $post['vervid'];
                    if ($vervId != 0) {
                        $beboerId = $post['beboerid'];
                        Verv::updateVerv($beboerId, $vervId);
                        $page = '?a=utvalg/sekretar/utvalgsverv';
                        header('Location: ' . $page, true, 303);
                        exit;
                    }
                }
            }
            $beboerListe = BeboerListe::aktive();
            $vervListe = VervListe::alleUtvalg();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vervListe', $vervListe);
            $dok->vis('utvalg_sekretar_utvalgsverv.php');
        } else if ($aktueltArg == 'helga') {
            $helga = Helga::getLatestHelga();
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                if (isset($post['fjern']) && is_numeric($post['fjern']) && $post['fjern'] > 0) {
                    $beboerId = $post['fjern'];
                    $helga->removeGeneral($beboerId);
                } elseif (isset($post['beboerid']) && is_numeric($post['beboerid']) && $post['beboerid'] > 0) {
                    $beboerId = $post['beboerid'];
                    $helga->addGeneral($beboerId);
                }
            }
            $alle_helga = Helga::getAlleHelga();
            $beboerliste = BeboerListe::aktive();
            $dok = new Visning($this->cd);
            $dok->set('BeboerListe', $beboerliste);
            $dok->set('alle_helga', $alle_helga);
            $dok->set('helga', $helga);
            $dok->vis('utvalg_sekretar_helga.php');
        } else if ($aktueltArg == 'lister') {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg_sekretar_lister.php');
        } else if (is_numeric($aktueltArg)) {
            $beboer = Beboer::medId($aktueltArg);
            // Trenger feilhåndtering her.
            $dok = new Visning($this->cd);
            $dok->set('beboer', $beboer);
            $dok->vis('beboer_detaljer.php');
        } else {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg_sekretar.php');
        }
    }
}

?>
