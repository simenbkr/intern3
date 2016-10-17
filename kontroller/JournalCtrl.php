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
                        setcookie('brukernavn', 'journal');
                        $passord = '2h6Sh801kS9zPq8N'; // TODO må fikese!!
                        setcookie('passord', $passord);
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
                        if(isset($_POST)){
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if(isset($post['beboerId']) && isset($post['antall']) && isset($post['type'])){
                                $beboerId = $post['beboerId'];
                                $antall = $post['antall'];
                                $drikkeid = $post['type'];

                                $krysselista = KrysseListe::medBeboerDrikkeId($beboerId,$drikkeid);
                                $krysselista->addKryss($antall);
                                $krysselista->oppdater();
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
