<?php

namespace intern3;

class JournalCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        $aktivBruker = LogginnCtrl::getAktivBruker();

        if ($aktivBruker != null && ($aktivBruker->getPerson()->erKjellerMester() || $aktivBruker->getPerson()->harUtvalgVerv())) {
            //Time to set token yo
            session_destroy();
            session_start();
            $token = Token::createToken('journal', 15768000);
            $_SESSION['token'] = $token->getToken();
            $_SESSION['success'] = 1;
            $_SESSION['msg'] = "Du har blitt logget ut av egen bruker, og inn på Journalen.";
        } elseif (
        !(
            isset($_SESSION['token']) ||
            ($token = Token::byToken($_SESSION['token'])) != null
            || $token->isValidToken('journal')
        )
        ) {
            header('Location: ?a=diverse');
            exit();
        }

        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
            case '':
            case 'hoved':
                $dok = new Visning($this->cd);
                $dok->set('skjulMeny', 1);
                $dok->vis('journal.php');
                break;

            case 'pinkode':
                $lastArg = $this->cd->getSisteArg();
                if (!is_numeric($lastArg) || ($beboer = Beboer::medId($lastArg)) == null) {
                    exit();
                }

                if (!$beboer->getPrefs()->harPinkode()) {
                    header('Location: ?a=journal/kryssing/' . $lastArg);
                    exit();
                }

                if ($_SESSION[md5($beboer->getFulltNavn())] > 0) {
                    header('Location: ?a=journal/kryssing/' . $lastArg);
                }

                if (count($_POST) > 0) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    if ($post['pinkode'] == $beboer->getPrefs()->getPinkode()) {
                        /* 2 fordi man skal få én visning av aktuell krysseside, samt ett POST for å krysse én gang. */
                        $_SESSION[md5($beboer->getFulltNavn())] = 2;
                    }
                }

                $dok = new Visning($this->cd);
                $dok->set('beboer', $beboer);
                $dok->vis("pinkode.php");
                break;
            case 'kryssing':
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $lastArg = $this->cd->getSisteArg();


                /*
                 * Det finnes to måter å få en visning, eller krysse. Når man får en visning (GET) er siste argumentet
                 * beboerIDen. Når man POSTer, dvs krysser på en spesifikk person, er beboerID del av POST-arrayen.
                 * Derfor må vi sjekke begge slik at man ikke kan krysse på de med pinkode, uten deres pinkode.
                 */

                if ( (is_numeric($lastArg) && ($beboer = Beboer::medId($lastArg)) != null
                    && $beboer->getPrefs()->harPinkode())
                    || (isset($post['beboerId']) && ($beboer = Beboer::medId($post['beboerId'])) != null
                    && $beboer->getPrefs()->harPinkode())
                ) {
                    if (!isset($_SESSION[md5($beboer->getFulltNavn())])) {
                        //Har ikke kake. Ayyy, get out you shit.
                        header('Location: ?a=journal/pinkode/' . $lastArg);
                        exit();
                    } else {
                        $_SESSION[md5($beboer->getFulltNavn())] = $_SESSION[md5($beboer->getFulltNavn())] - 1;
                    }
                }

                if (isset($_POST)) {
                    if (isset($post['beboerId']) && isset($post['antall']) && isset($post['type'])) {
                        $beboerId = $post['beboerId'];
                        $beboer = Beboer::medId($beboerId);
                        if ($beboer != null && $beboer->harAlkoholdepositum()
                            && Drikke::medId($post['type']) != null && Drikke::medId($post['type'])->getAktiv()
                        ) {
                            //Alt OK, lets kryss:
                            $antall = $post['antall'];
                            $drikkeid = $post['type'];
                            $krysselista = Krysseliste::medBeboerDrikkeId($beboerId, $drikkeid);
                            $krysselista->addKryss($antall);
                            $krysselista->oppdater();

                            $denne_vakta = AltJournal::getLatest();

                            $aktuelt_krysseobjekt = $denne_vakta->getStatusByDrikkeId($drikkeid);

                            if ($aktuelt_krysseobjekt == null) {
                                $obj = array(
                                    'drikkeId' => $drikkeid,
                                    'mottatt' => 0,
                                    'avlevert' => 0,
                                    'pafyll' => 0,
                                    'utavskap' => $antall
                                );
                                $denne_vakta->updateObject($obj);
                                $denne_vakta->calcAvlevert();
                            } else {
                                $aktuelt_krysseobjekt['utavskap'] += $antall;
                                $denne_vakta->updateObject($aktuelt_krysseobjekt);
                                $denne_vakta->calcAvlevert();
                            }
                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "Du krysset " . $post['antall'] . " " .
                                Drikke::medId($drikkeid)->getNavn() . " på " .
                                Beboer::medId($beboerId)->getFulltNavn();

                            if($beboer->getPrefs()->harPinkode()){
                                unset($_SESSION[md5($beboer->getFulltNavn())]);
                            }

                            $_SESSION['scroll'] = $beboerId;
                            break;
                        }
                    }
                }
                $beboerId = $this->cd->getSisteArg();
                $beboer = Beboer::medId($beboerId);

                if ($beboer != null && $beboer->harAlkoholdepositum()) {
                    $drikker = Drikke::alle();
                    $drikke_navn = array();
                    $drikke_farger = array();
                    $forste = $drikker[1]->getId();
                    foreach ($drikker as $drikke) {
                        $drikke_navn[$drikke->getId()] = $drikke->getNavn();
                        $drikke_farger[$drikke->getId()] = $drikke->getFarge();
                    }
                    $dok = new Visning($this->cd);
                    $dok->set('drikker', $drikker);
                    $dok->set('forste', $forste);
                    $dok->set('drikke_navn', $drikke_navn);
                    $dok->set('drikke_farger', $drikke_farger);
                    $dok->set('skjulMeny', 1);
                    $dok->set('beboer', $beboer);
                    $dok->vis('journal_kryss.php');

                    if ($_SESSION[md5($beboer->getFulltNavn())] < 1) {
                        setcookie('unsat', 'hehe');
                        unset($_SESSION[md5($beboer->getFulltNavn())]);
                    }

                    break;
                }
            case 'krysseliste':
                $beboere = BeboerListe::reseppListe();
                $dato = AltJournal::getLatest()->getDato();
                $krysseliste = Krysseliste::getAlleKryssetEtterDato($dato);
                $drikke = Drikke::alle();
                $denne_vakta = AltJournal::getLatest();
                $dok = new Visning($this->cd);
                $dok->set('denne_vakta', $denne_vakta);
                $dok->set('drikke', $drikke);
                $dok->set('krysseliste', $krysseliste);
                $dok->set('beboere', $beboere);
                $dok->set('skjulMeny', 1);
                $dok->vis('krysselista.php');
                break;
            case 'pafyll':
                $dok = new Visning($this->cd);
                $denne_vakta = AltJournal::getLatest();
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($post['pafyll']) && $post['pafyll'] == 1 && isset($post['antall'])
                        && is_numeric($post['antall']) && isset($post['type']) && is_numeric($post['type']
                        /*&& ($drikken = Drikke::medId($post['type'])) != null*/)
                    ) {

                        $aktuelt_krysseobjekt = $denne_vakta->getStatusByDrikkeId($post['type']);
                        if ($aktuelt_krysseobjekt == null) {
                            $obj = array(
                                'drikkeId' => $post['type'],
                                'mottatt' => 0,
                                'avlevert' => 0,
                                'pafyll' => $post['antall'],
                                'utavskap' => 0
                            );
                            $denne_vakta->updateObject($obj);
                            $denne_vakta->calcAvlevert();
                        } else {
                            $aktuelt_krysseobjekt['pafyll'] += $post['antall'];
                            $denne_vakta->updateObject($aktuelt_krysseobjekt);
                            $denne_vakta->calcAvlevert();
                        }
                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du fylte på " . $post['antall'] . " " . Drikke::medId($post['type'])->getNavn();
                    }
                }
                $vakta = Bruker::medId($denne_vakta->getBrukerId());

                if ($vakta == null) {
                    $vakta = Ansatt::getSisteAnsatt();
                } else {
                    $vakta = $vakta->getPerson();
                }

                $drikker = Drikke::aktive();
                $drikke_navn = array(); //Dummy for å ikke være nullindeksert (woo, spaghetti!)
                $drikke_farger = array();
                $forste = $drikker[1]->getId();
                foreach ($drikker as $drikke) {
                    $drikke_navn[$drikke->getId()] = $drikke->getNavn();
                    $drikke_farger[$drikke->getId()] = $drikke->getFarge();
                }
                $dok->set('drikker', $drikker);
                $dok->set('forste', $forste);
                $dok->set('drikke_navn', $drikke_navn);
                $dok->set('drikke_farger', $drikke_farger);
                $dok->set('skjulMeny', 1);
                $dok->set('vakta', $vakta);
                $dok->set('vaktSesj', $denne_vakta);
                $dok->vis('journal_pafyll.php');
                break;
            case 'vaktbytte':
                $denneVakt = AltJournal::getLatest();
                if (isset($_POST) && isset($_POST['brukerId']) && is_numeric($_POST['brukerId'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if ($post['brukerId'] >= 0 && Bruker::medId($post['brukerId']) != null) {
                        $denneVakt->setBrukerId($post['brukerId']);
                    } //Torild
                    elseif ($post['brukerId'] == 0) {
                        $denneVakt->setBrukerId($post['brukerId']);
                    }
                }
                if ($denneVakt->getBrukerId() == 0) {
                    //Torild
                    $vakta = Ansatt::getSisteAnsatt();
                } else {
                    $vakta = Bruker::medId($denneVakt->getBrukerId());
                }
                if ($vakta == null) {
                    $vakta = Ansatt::getSisteAnsatt();
                } else {
                    $vakta = $vakta->getPerson();
                }

                $dok = new Visning($this->cd);
                $dok->set('denneVakt', $denneVakt);
                $dok->set('vakt', $vakta);
                $sistearg = $this->cd->getSisteArg();
                if ($sistearg == 'vaktbytte') {
                    $denne_vakta = AltJournal::getLatest();
                    $beboere = BeboerListe::aktive();
                    $dok->set('beboere', $beboere);
                    $dok->set('denne_vakta', $denne_vakta);
                    $dok->set('skjulMeny', 1);
                    $dok->vis('vaktbytte.php');
                    break;
                } else {
                    if ($sistearg == "TORILD") {
                    } else {
                        $bokstav = $sistearg;
                        $beboere = BeboerListe::aktive();
                        $aktuelle = array();
                        foreach ($beboere as $beboer) {
                            if (($beboer->getRolleId() == 1 || $beboer->getRolleId() == 2) && Funk::startsWith($beboer->getEtternavn(), $bokstav) && !$beboer->harUtvalgVerv()) {
                                $aktuelle[] = $beboer;
                            }
                        }
                        $dok->set('aktuelle', $aktuelle);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('vaktbytte_bokstav.php');
                        break;
                    }
                }
            case 'signering':
                $denne_vakta = AltJournal::getLatest();
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($post['brukerId']) && is_numeric($post['brukerId'])) {
                        $denne_vakta = AltJournal::getLatest();
                        if (time() - strtotime($denne_vakta->getDato()) < 120) {
                            $_SESSION['error'] = 1;
                            $_SESSION['msg'] = "Vent litt før du avslutter enda en vakt!";
                        } else {
                            $denne_vakta = AltJournal::avsluttVakt($denne_vakta);
                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "Du avsluttet vakta!";
                        }
                    }
                }
                $denne_vakta = AltJournal::getLatest();

                $vaktaId = $denne_vakta->getBrukerId();
                $vakta = Bruker::medId($vaktaId);

                if ($vakta == null) {
                    $vakta = Ansatt::getSisteAnsatt();
                } else {
                    $vakta = $vakta->getPerson();
                }

                /*if (($vakta = Beboer::medId($vaktaId)) == null) {
                    $vakta = Ansatt::getSisteAnsatt();
                }*/
                $drikke = Drikke::alle();
                $drikke_med_ting = array();

                foreach ($drikke as $drikken) {
                    $drikke_med_ting[$drikken->getId()] = $drikken;
                }

                $dok = new Visning($this->cd);
                $dok->set('skjulMeny', 1);
                $dok->set('denne_vakta', $denne_vakta);
                $dok->set('vakta', $vakta);
                $dok->set('drikke_med_id', $drikke_med_ting);
                $dok->vis('journal_signering.php');
                break;
            case 'logout':
            default:
                setcookie('DESTROY', 'ALLSESSIONS0');
                session_destroy();
                setcookie('DESTROY', 'ALLSESSIONS0');
                header('Location: ' . $_GET['ref']);
                $dok = new Visning($this->cd);
                $dok->set('skjulMeny', 1);
                $dok->set('visError', 1);
                $dok->vis('logginn.php');
        }
    }
}

?>
