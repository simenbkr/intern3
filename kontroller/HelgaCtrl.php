<?php

namespace intern3;

class HelgaCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $beboer = $this->cd->getAktivBruker()->getPerson();
        $beboer_id = LogginnCtrl::getAktivBruker()->getPerson()->getId();
        $denne_helga = Helga::getLatestHelga();
        $aar = $denne_helga->getAar();
        switch ($aktueltArg) {
            case 'general':
                //Hvis bruker ikke er general går man til default. Ganske smart.
                if ($beboer->erHelgaGeneral()) {
                    $dok = new Visning($this->cd);
                    if (isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                        //foreach($post as $key => $value) {setcookie($key, $value);}

                        if (isset($post['start']) && isset($post['aar'])) {
                            $klar = $post['klar'] == 'on' ? 1 : 0;
                            $st = DB::getDB()->prepare('UPDATE helga SET start_dato=:start, tema=:tema, klar=:klar, max_gjest=:max_gjest WHERE aar=:aar');
                            $st->bindParam(':start', $post['start']);
                            $st->bindParam(':tema', $post['tema']);
                            $st->bindParam(':aar', $post['aar']);
                            $st->bindParam(':klar', $klar);
                            $st->bindParam(':max_gjest', $post['max_gjest']);
                            $st->execute();
                            $dok->set('oppdatert', 1);
                        }
                    }
                    $alle_helga = Helga::getAlleHelga();
                    $dok->set('alle_helga', $alle_helga);
                    $dok->set('helga', $denne_helga);
                    $dok->vis('helga_general.php');
                    break;
                }
            case 'helga':
            default:
                $dok = new Visning($this->cd);
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    if (isset($post['add']) && isset($post['navn']) && isset($post['epost'])) {
                        //Legg til gjest.
                        if (Funk::isValidEmail($post['epost'])) {
                            HelgaGjest::addGjest($post['navn'], $post['epost'], $beboer_id, $aar);
                        } else {
                            $dok->set('epostError', 1);
                        }
                    }

                    if (isset($post['fjern']) && isset($post['gjestid']) && is_numeric($post['gjestid'])) {
                        //Fjern gjest.
                        $id = $post['gjestid'];
                        if (HelgaGjest::belongsToBeboer($id, $beboer_id)) {
                            HelgaGjest::removeGjest($post['gjestid']);
                        } else {
                            $dok->set('VisError', 1);
                        }
                    }

                    if (isset($post['send']) && isset($post['gjestid'])) {
                        $gjesteid = $post['gjestid'];
                        if (HelgaGjest::belongsToBeboer($gjesteid, $beboer_id)) {
                            $gjesten = HelgaGjest::byId($gjesteid);
                            $tittel = "[SING-HELGA] Du har blitt invitert til HELGA-" . $denne_helga->getAar();
                            $beskjed = "<html><body>Hei, " . $gjesten->getNavn() ."! <br/><br/>Du har blitt invitert til" . $denne_helga->getTema() ."-" . $denne_helga->getAar() . " av " . $beboer->getFulltNavn() ."<br/><br/>Vi håper du ønsker å ta turen!<br/><br/>Med vennlig hilsen<br/>Singsaker Studenterhjem</body></html>";
                            Epost::sendEpost($gjesten->getEpost(), $tittel, $beskjed);
                            $gjesten->setSendt(1);
                        }
                    }
                }
                $beboers_gjester = HelgaGjesteListe::getGjesteListeByBeboerAar($beboer_id, $aar);
                $gjeste_count = HelgaGjesteListe::getGjesteCountBeboer($beboer_id, $aar);
                $max_gjeste_count = $denne_helga->getMaxGjester();
                $dok->set('max_gjeste_count', $max_gjeste_count);
                $dok->set('beboers_gjester', $beboers_gjester);
                $dok->set('gjeste_count', $gjeste_count);
                $dok->vis('helga.php');
                exit();
        }
    }
}

?>
