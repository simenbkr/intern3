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
        $gjestelista = $denne_helga->getGjesteliste();
        $aar = $denne_helga->getAar();
        $dag_array = array(
            0 => 'Torsdag',
            1 => 'Fredag',
            2 => 'Lørdag'
        );
        if (LogginnCtrl::getAktivBruker() != null && $beboer != null) {
            switch ($aktueltArg) {
                case 'general':
                    //Hvis bruker ikke er general går man til default. Ganske smart.
                    if ($beboer->erHelgaGeneral()) {
                        $dok = new Visning($this->cd);
                        if (isset($_POST)) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if (isset($post['endre']) && isset($post['epost_tekst'])) {
                                $denne_helga->setEpostTekst($post['epost_tekst']);
                                $dok->set('oppdatert', 1);
                            } elseif (isset($post['start']) && isset($post['aar'])) {
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
                        $denne_helga = Helga::getLatestHelga();
                        $alle_helga = Helga::getAlleHelga();
                        $dok->set('alle_helga', $alle_helga);
                        $dok->set('helga', $denne_helga);
                        $dok->vis('helga_general.php');
                        break;
                    }
                case 'inngang':
                    $dok = new Visning($this->cd);

                    if(isset($_POST)){
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        $bruker = LogginnCtrl::getAktivBruker();
                        if($bruker != null && $denne_helga->erHelgaGeneral($bruker->getPerson()->getId())
                        && isset($post['registrer']) && isset($post['gjestid']) && isset($post['verdi'])){
                            $gjesten = HelgaGjest::byId($post['gjestid']);
                            //data: 'registrer=ok&gjestid=' + id + "&verdi=" + verdi,
                            if($gjesten != null && $gjesten->getAar() == $denne_helga->getAar()){
                                $verdi = $post['verdi'] == 0 ? 0 : 1;
                                $gjesten->setInne($verdi);
                            }
                        }
                    }
                    $dagen = $this->cd->getSisteArg();
                    switch ($dagen) {
                        case 'fredag':
                            $dag = 1;
                            $dok->set('dag_tall', 1);
                            break;
                        case 'lordag':
                            $dag = 2;
                            $dok->set('dag_tall', 2);
                            break;
                        case 'torsdag':
                        default:
                            $dag = 0;
                            $dok->set('dag_tall', 0);
                    }
                    $gjesteliste_dag = HelgaGjesteListe::getGjesterUngrouped($denne_helga->getAar(), $dag);
                    $gjesteliste_dag_gruppert = HelgaGjesteListe::getGjesterGroupedbyHost($denne_helga->getAar(), $dag);
                    $beboerlista = array();
                    foreach(BeboerListe::aktive() as $beboer){
                        $beboerlista[$beboer->getId()] = $beboer;
                    }
                    $dok->set('gjesteliste_dag', $gjesteliste_dag);
                    $dok->set('gjesteliste_dag_gruppert', $gjesteliste_dag_gruppert);
                    //$dok->set('gjestelista', $gjestelista);
                    $dok->set('beboerliste', $beboerlista);
                    $dok->vis('helga_inngang.php');
                    break;
                case 'helga':
                default:
                    $dok = new Visning($this->cd);
                    if (isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if (isset($post['add']) && isset($post['navn']) && isset($post['epost']) && is_numeric($post['add'])) {
                            //Legg til gjest.
                            if (Funk::isValidEmail($post['epost'])) {
                                HelgaGjest::addGjest($post['navn'], $post['epost'], $beboer_id, $post['add'], $aar);
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
                        if (isset($post['send']) && isset($post['gjestid']) && is_numeric($post['gjestid'])) {
                            $gjesteid = $post['gjestid'];
                            $gjesten = HelgaGjest::byId($gjesteid);
                            if (HelgaGjest::belongsToBeboer($gjesteid, $beboer_id) && Funk::isValidEmail($gjesten->getEpost())) {
                                $dagen = $dag_array[$gjesten->getDag()];
                                $datoen = date('Y-m-d', strtotime($denne_helga->getStartDato() . " +" . $gjesten->getDag() . " days"));
                                $tittel = "[SING-HELGA] Du har blitt invitert til HELGA-" . $denne_helga->getAar();
                                $beskjed = "<html><body>Hei, " . $gjesten->getNavn() . "! <br/><br/>Du har blitt invitert til " . $denne_helga->getTema() . "-" . $denne_helga->getAar() . " av " . $beboer->getFulltNavn() . "<br/><br/>Denne invitasjonen gjelder for $dagen $datoen<br/><br/>Vi håper du ønsker å ta turen!<br/><br/>Med vennlig hilsen<br/>Singsaker Studenterhjem<br/><br/><br/><br/>Dette er en automatisert melding. Feil? Vennligst ta kontakt med data@singsaker.no.</body></html>";
                                //$beskjed = $denne_helga->getEpostTekst() . "<br/><br/>Denne invitasjonen gjelder for $dagen $datoen<br/><br/>Med vennlig hilsen<br/>" . $denne_helga->getTema() . "-Helga 2017";
                                Epost::sendEpost($gjesten->getEpost(), $tittel, $beskjed);
                                $gjesten->setSendt(1);
                                $dok->set('epostSendt', 1);
                            }
                        }
                    }
                    $dagen = $this->cd->getSisteArg();
                    switch ($dagen) {
                        case 'torsdag':
                            $dag_tall = 0;
                            break;
                        case 'fredag':
                            $dag_tall = 1;
                            break;
                        case 'lordag':
                            $dag_tall = 2;
                            break;
                        default:
                            $dag_tall = 0;
                    }
                    $beboers_gjester = HelgaGjesteListe::getGjesteListeDagByBeboerAar($dag_tall, $beboer_id, $aar);
                    $gjeste_count = HelgaGjesteListe::getGjesteCountDagBeboer($dag_tall, $beboer_id, $aar);
                    $max_gjeste_count = $denne_helga->getMaxGjester();
                    $dok->set('dag_tall', $dag_tall);
                    $dok->set('max_gjeste_count', $max_gjeste_count);
                    $dok->set('beboers_gjester', $beboers_gjester);
                    $dok->set('gjeste_count', $gjeste_count);
                    $dok->set('dagen', $dagen);
                    $dok->vis('helga.php');
                    exit();
            }
        }

    }
}

?>
