<?php

namespace intern3;

class UtvalgSekretarCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'apmandsverv') {

            /* Endre åpmandsverv-visning */
            if (($sisteArg = $this->cd->getSisteArg()) != $aktueltArg && is_numeric($sisteArg)
                && ($vervet = Verv::medId($sisteArg)) != null) {

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    /* Legg til beboer til det aktuelle vervet */
                    if (isset($post['vervet'])) {
                        $postet = explode('&', $post['vervet']);
                        $beboerId = $postet[0];
                        if ($beboerId != 0 && ($beboeren = Beboer::medId($beboerId)) != null) {
                            $vervId = $postet[1];
                            Verv::updateVerv($beboerId, $vervId);
                            Funk::setSuccess("La til " . $beboeren->getFulltNavn() . " på åpmandsvervet " . $vervet->getNavn() . "!");
                            header('Location: ?a=utvalg/sekretar/apmandsverv/' . $vervet->getId());
                            exit();
                        }
                    }

                    /* Fjern beboere fra verv, og slett vervet. */
                    if (isset($post['slett']) && $post['slett'] == $vervet->getId()) {
                        Funk::setSuccess("Vervet " . $vervet->getNavn() . " ble slettet!");
                        $vervet->slett();
                        header('Location: ?a=utvalg/sekretar/apmandsverv');
                        exit();
                    }

                    /* Endre vervet */
                    if(isset($post['navn']) && isset($post['beskrivelse'])) {
                        $vervet->setNavn($post['navn'], true);
                        $vervet->setBeskrivelse($post['beskrivelse'], true);
                        $vervet->setUtvalg($post['utvalg'] == 1 ? 1 : 0, true);
                        $vervet->setEpost($post['epost'], true);
                        $vervet->setRegitimer($post['regitimer'], true);

                        Funk::setSuccess("Du endret dette vervet!");
                        header('Location: ?a=utvalg/sekretar/apmandsverv/' . $vervet->getId());
                        exit();
                    }

                    Funk::setError("Det ser ut som noe gikk galt der. Prøv på nytt. Dersom problemet vedvarer, hør med Data.");

                }

                $dok = new Visning($this->cd);
                $dok->set('vervet', $vervet);
                $dok->vis('utvalg/sekretar/utvalg_sekretar_apmandsverv_endre.php');
                exit();
            }

            /* Liste over åpmandsverv */
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                //Add verv
                if (isset($_POST['navn']) && isset($_POST['beskrivelse']) && isset($_POST['regitimer'])) {
                    $utvalg = ($post['utvalg'] == 1);
                    $epost = Funk::isValidEmail($post['epost']) ? $post['epost'] : '';

                    /*
                    if($epost != $post['epost'])
                        Funk::setError("Vervet ble lagt til, men ikke med den innsendte eposten. Den var det noe galt med.");

                    if(!is_numeric($post['regitimer']))
                        Funk::setError("Advarsel: Regitimer kan ikke være tekst. Vervet ble oppretta med 0 regitimer.");
                    */

                    Verv::opprett($post['navn'], $post['beskrivelse'], intval($post['regitimer']), $epost, $utvalg);
                    Funk::setSuccess("La til nytt åpmandsverv!");
                    header('Location: ?a=utvalg/sekretar/apmandsverv');
                    exit();
                } //Fjern beboer fra verv
                elseif (isset($_POST['fjern']) && isset($_POST['verv'])) {
                    $beboerId = $post['fjern'];
                    $vervId = $post['verv'];
                    Verv::deleteBeboerFromVerv($beboerId, $vervId);
                } //Legg til beboer til verv
                else if (isset($_POST['vervet'])) {
                    $postet = explode('&', $post['vervet']);
                    $beboerId = $postet[0];
                    if ($beboerId != 0) {
                        $vervId = $postet[1];
                        Verv::updateVerv($beboerId, $vervId);
                        $page = '?a=utvalg/sekretar/apmandsverv';
                        Funk::setSuccess("La til beboeren på dette vervet!");
                        header('Location: ' . $page, true, 303);
                        exit();
                    }
                }
            }

            $beboerListe = BeboerListe::aktive();
            $vervListe = VervListe::alle();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vervListe', $vervListe);
            $dok->vis('utvalg/sekretar/utvalg_sekretar_apmandsverv.php');
        } else if ($aktueltArg == 'apmandsverv_modal') {
            $beboerListe = BeboerListe::aktive();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->vis('utvalg/sekretar/utvalg_sekretar_apmandsverv_modal.php');
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
            $dok->vis('utvalg/sekretar/utvalg_sekretar_utvalgsverv.php');
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
                        $dok->vis('utvalg/sekretar/utvalg_sekretar_helga_endre.php');
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
            $dok->vis('utvalg/sekretar/utvalg_sekretar_helga.php');
        } else if ($aktueltArg == 'lister') {
            $dok = new Visning($this->cd);
            $sisteArg = $this->cd->getSisteArg();
            if ($sisteArg == $aktueltArg) {
                $dok->vis('utvalg/sekretar/utvalg_sekretar_lister.php');
                return;
            }
            switch ($sisteArg) {
                case 'apmandsverv':
                    $apmandsverv = VervListe::alle();
                    $dok->set('apmandsverv', $apmandsverv);
                    $dok->vis('utvalg/sekretar/utvalg_sekretar_lister_apmandsverv.php');
                    return;
                case 'apmandsverv_utskrift':
                    $apmandsverv = VervListe::alle();
                    $dok->set('apmandsverv', $apmandsverv);
                    $dok->vis('utvalg/sekretar/utvalg_sekretar_lister_apmandsverv_utskrift.php');
                    return;
                case 'apmandsverv_beskrivelser':
                    $apmandsverv = VervListe::alle();
                    $dok->set('vervene', $apmandsverv);
                    $dok->vis('utvalg/sekretar/utvalg_sekretar_lister_apmandsverv_beskrivelser.php');
                    return;
                case 'apmandsverv_beskrivelser_utskrift':
                    $apmandsverv = VervListe::alle();
                    $dok->set('vervene', $apmandsverv);
                    $dok->vis('utvalg/sekretar/utvalg_sekretar_lister_apmandsverv_beskrivelser_utskrift.php');
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
            $dok->vis('utvalg/sekretar/utvalg_sekretar_apmandstimer.php');
        } else if (is_numeric($aktueltArg)) {
            $beboer = Beboer::medId($aktueltArg);
            // Trenger feilhåndtering her.
            $dok = new Visning($this->cd);
            $dok->set('beboer', $beboer);
            $dok->vis('beboer/beboer_detaljer.php');
        } else {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg/sekretar/utvalg_sekretar.php');
        }
    }
}

?>
