<?php

namespace intern3;

class JournalCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        if (LogginnCtrl::getAktivBruker() != null) {
            $aktivBruker = LogginnCtrl::getAktivBruker();
            if ($aktivBruker->getPerson()->harUtvalgVerv()) {
                $aktueltArg = $this->cd->getAktueltArg();
                switch ($aktueltArg) {
                    case 'hoved':
                    default:
                        setcookie('brukernavn', 'journal', NULL, NULL, NULL, NULL, TRUE);
                        $passord = '2h6Sh801kS9zPq8N'; // TODO må fikese!!
                        setcookie('passord', $passord, NULL, NULL, NULL, NULL, TRUE);
                        setcookie('du', '', -1);
                        Header('Location: ' . $_GET['ref']);
                        $dok = new Visning($this->cd);
                        $dok->set('success', 1);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('journal.php');
                        break;
                }
            }
        } else if (isset($_COOKIE['brukernavn']) && isset($_COOKIE['passord'])) {
            if ($_COOKIE['brukernavn'] == 'journal' && $_COOKIE['passord'] == '2h6Sh801kS9zPq8N') {
                $aktueltArg = $this->cd->getAktueltArg();
                switch ($aktueltArg) {
                    case 'hoved':
                        $dok = new Visning($this->cd);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('journal.php');
                        break;
                    case 'krysseliste':
                        $beboere = BeboerListe::aktive();
                        $dok = new Visning($this->cd);
                        $dok->set('beboere', $beboere);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('krysselista.php');
                        break;
                    case 'kryssing':
                        if (isset($_POST)) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if (isset($post['beboerId']) && isset($post['antall']) && isset($post['type'])) {
                                $beboerId = $post['beboerId'];
                                $antall = $post['antall'];
                                $drikkeid = $post['type'];
                                if (!Beboer::medId($beboerId)->harAlkoholdepositum()) {
                                    die('Du har ikke betalt alkoholdepositum og kan ikke krysse!');
                                    //TODO gjør noe mer fornuftig med detta. Vise error-page elns.
                                }
                                $krysselista = Krysseliste::medBeboerDrikkeId($beboerId, $drikkeid);
                                $krysselista->addKryss($antall);
                                $krysselista->oppdater();
                                //$krysselista->kryss($antall);
                                break;
                            }
                        }
                        $beboerId = $this->cd->getSisteArg();
                        $beboer = Beboer::medId($beboerId);
                        $dok = new Visning($this->cd);
                        $dok->set('skjulMeny', 1);
                        $dok->set('beboer', $beboer);
                        $dok->vis('kryss.php');
                        break;
                    case 'vaktbytte':
                        $denneVakt = VaktSesjon::getLatest();
                        if (isset($_POST) && isset($_POST['beboerId']) && is_numeric($_POST['beboerId'])) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if($post['beboerId'] >= 0 && Beboer::medId($post['beboerId']) != null){
                                $denneVakt->setBeboerId($post['beboerId']);
                            }
                            //Torild
                            elseif($post['beboerId'] == 0){
                                $denneVakt->setBeboerId($post['beboerId']);
                            }
                        }
                        if($denneVakt->getBeboerId() == 0){
                            //Torild
                            $vakta = Ansatt::medId(1);
                        } else {
                            $vakta = Beboer::medId($denneVakt->getBeboerId());
                        }
                        $dok = new Visning($this->cd);
                        $dok->set('denneVakt', $denneVakt);
                        $dok->set('vakt', $vakta);
                        $sistearg = $this->cd->getSisteArg();
                        if ($sistearg == 'vaktbytte') {
                            $beboere = BeboerListe::aktive();
                            $dok->set('beboere', $beboere);
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
                                    if (($beboer->getRolleId() == 1 || $beboer->getRolleId() == 2) && Funk::startsWith($beboer->getEtternavn(), $bokstav)) {
                                        $aktuelle[] = $beboer;
                                    }
                                }

                                $dok->set('aktuelle', $aktuelle);
                                $dok->set('skjulMeny', 1);
                                $dok->vis('vaktbytte_bokstav.php');
                                break;
                            }
                        }
                    default:
                        setcookie('brukernavn', '', -1);
                        setcookie('passord', '', -1);
                        setcookie('du', '', -1);
                        Header('Location: ' . $_GET['ref']);
                        $dok = new Visning($this->cd);
                        $dok->set('skjulMeny', 1);
                        $dok->set('visError', 1);
                        $dok->vis('logginn.php');
                }
            }
        } else {
            setcookie('brukernavn', '', -1);
            setcookie('passord', '', -1);
            setcookie('du', '', -1);
            Header('Location: ' . $_GET['ref']);
            $dok = new Visning($this->cd);
            $dok->set('skjulMeny', 1);
            $dok->set('visError', 1);
            $dok->vis('logginn.php');
            exit();
        }
    }
}

?>
