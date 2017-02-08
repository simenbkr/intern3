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
            $sisteArg = $this->cd->getSisteArg();
            if ($sisteArg != $aktueltArg) {
                $aktuell_helga = Helga::getHelgaByAar($sisteArg);
                if ($aktuell_helga != null) {
                    if (is_numeric($sisteArg) && isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if (isset($post['sletthelga']) && $post['sletthelga'] == 1 && isset($post['aar']) && is_numeric($post['aar'])) {
                            $st = DB::getDB()->prepare('DELETE FROM helga WHERE aar=:aar');
                            $aar = $post['aar'];
                            $st->bindParam(':aar', $aar);
                            $st->execute();
                        }
                        if (isset($post['tema']) && isset($post['start'])) {
                            $aktuell_helga->changeTema($post['tema']);
                            $aktuell_helga->changeDato($post['start']);
                            header('Location: ?a=utvalg/sekretar/helga');
                            exit();
                        }
                    }
                    if (is_numeric($sisteArg)) {
                        $dok = new Visning($this->cd);
                        $dok->set('helgaen', $aktuell_helga);
                        $dok->vis('utvalg_sekretar_helga_endre.php');
                        return;
                    }
                }
            }
            $helga = Helga::getLatestHelga();
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                if (isset($post['ny_helga']) && isset($post['aar']) && is_numeric($post['aar'])) {
                    Helga::createBareBoneHelga($post['aar']);
                } elseif (isset($post['fjern']) && is_numeric($post['fjern'])) {
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
            $sisteArg = $this->cd->getSisteArg();
            if ($sisteArg == $aktueltArg) {
                $dok->vis('utvalg_sekretar_lister.php');
                return;
            }
            switch ($sisteArg) {
                case 'apmandsverv':
                    $apmandsverv = VervListe::alle();
                    $dok->set('apmandsverv', $apmandsverv);
                    $dok->vis('utvalg_sekretar_lister_apmandsverv.php');
                    return;
                case 'apmandsverv_utskrift':
                    $apmandsverv = VervListe::alle();
                    $dok->set('apmandsverv', $apmandsverv);
                    $dok->vis('utvalg_sekretar_lister_apmandsverv_utskrift.php');
                    return;
                case 'apmandsverv_beskrivelser':
                    $apmandsverv = VervListe::alle();
                    $dok->set('vervene', $apmandsverv);
                    $dok->vis('utvalg_sekretar_lister_apmandsverv_beskrivelser.php');
                    return;
                case 'apmandsverv_beskrivelser_utskrift':
                    $apmandsverv = VervListe::alle();
                    $dok->set('vervene', $apmandsverv);
                    $dok->vis('utvalg_sekretar_lister_apmandsverv_beskrivelser_utskrift.php');
                    return;
            }
        } else if ($aktueltArg == 'apmandstimer') {
            $dok = new Visning($this->cd);
            //data: 'endreTimer=1&timer=' + timer + "&vervId=" + id,
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                if (isset($post['endreTimer']) && $post['endreTimer'] == 1 && isset($post['timer']) && is_numeric($post['timer'])
                    && isset($post['vervId']) && is_numeric($post['vervId'])
                ) {
                    $aktueltVerv = Verv::medId($post['vervId']);
                    if ($aktueltVerv != null) {
                        $timer = $post['timer'];
                        $id = $post['vervId'];
                        $st = DB::getDB()->prepare('UPDATE verv SET regitimer=:timer WHERE id=:id');
                        $st->bindParam(':timer', $timer);
                        $st->bindParam(':id', $id);
                        $st->execute();
                    }
                }
            }

            $apmandsverv = VervListe::alle();
            $dok->set('apmandsverv', $apmandsverv);
            $dok->vis('utvalg_sekretar_apmandstimer.php');
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
