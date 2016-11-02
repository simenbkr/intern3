<?php

namespace intern3;

class JournalCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $passord = '2h6Sh801kS9zPq8N'; // TODO må fikese!!
        $passord_hash = hash('sha256', $passord);
        if (LogginnCtrl::getAktivBruker() != null) {
            $aktivBruker = LogginnCtrl::getAktivBruker();
            if ($aktivBruker->getPerson()->harUtvalgVerv()) {
                $aktueltArg = $this->cd->getAktueltArg();
                switch ($aktueltArg) {
                    case 'hoved':
                    default:
                        setcookie('brukernavn', 'journal', NULL, NULL, NULL, NULL, TRUE);
                        setcookie('passord', $passord_hash, NULL, NULL, NULL, NULL, TRUE);
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
            if ($_COOKIE['brukernavn'] == 'journal' && $_COOKIE['passord'] == $passord_hash) {
                $aktueltArg = $this->cd->getAktueltArg();
                switch ($aktueltArg) {
                    case 'hoved':
                        $dok = new Visning($this->cd);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('journal.php');
                        break;
                    case 'kryssing':
                        if (isset($_POST)) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if (isset($post['beboerId']) && isset($post['antall']) && isset($post['type'])) {
                                $beboerId = $post['beboerId'];
                                if (Beboer::medId($beboerId) != null && Beboer::medId($beboerId)->harAlkoholdepositum()) {
                                    //Alt OK, lets kryss:
                                    $antall = $post['antall'];
                                    $drikkeid = $post['type'];
                                    $krysselista = Krysseliste::medBeboerDrikkeId($beboerId, $drikkeid);
                                    $krysselista->addKryss($antall);
                                    $krysselista->oppdater();

                                    //Loggfører til journal.utavskap.
                                    $denne_vakta = VaktSesjon::getLatest();
                                    switch($drikkeid) {
                                        //var drikker = ['','Pant','Øl','Cider','Carlsberg','Rikdom'];
                                        case 2:
                                            $nyOl = $denne_vakta->getOl();
                                            $nyOl['utavskap'] += $post['antall'];
                                            $denne_vakta->setOl($nyOl);
                                            break;
                                        case 3:
                                            $nyCider = $denne_vakta->getCider();
                                            $nyCider['utavskap'] += $post['antall'];
                                            $denne_vakta->setCider($nyCider);
                                            break;
                                        case 4:
                                            $nyCarls = $denne_vakta->getCarlsberg();
                                            $nyCarls['utavskap'] += $post['antall'];
                                            $denne_vakta->setCarlsberg($nyCarls);
                                            break;
                                        case 5:
                                            $nyRikdom = $denne_vakta->getRikdom();
                                            $nyRikdom['utavskap'] += $post['antall'];
                                            $denne_vakta->setRikdom($nyRikdom);
                                            break;
                                    }
                                    break;
                                }
                            }
                        }
                        $beboerId = $this->cd->getSisteArg();
                        $beboer = Beboer::medId($beboerId);
                        if ($beboer != null && $beboer->harAlkoholdepositum()) {
                            $dok = new Visning($this->cd);
                            $dok->set('skjulMeny', 1);
                            $dok->set('beboer', $beboer);
                            $dok->vis('kryss.php');
                            break;
                        }
                    case 'krysseliste':
                        $beboere = BeboerListe::aktive();
                        $dok = new Visning($this->cd);
                        $dok->set('beboere', $beboere);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('krysselista.php');
                        break;
                    case 'pafyll':
                        $dok = new Visning($this->cd);
                        $denne_vakta = VaktSesjon::getLatest();
                        if (isset($_POST)) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if(isset($post['pafyll']) && $post['pafyll'] == 1 && isset($post['antall'])
                                && is_numeric($post['antall']) && isset($post['type']) && is_numeric($post['type'])){
                                switch($post['type']){
                                    //var drikker = ['','Pant','Øl','Cider','Carlsberg','Rikdom'];
                                    case 2:
                                        $nyOl = $denne_vakta->getOl();
                                        $nyOl['pafyll'] += $post['antall'];
                                        $denne_vakta->setOl($nyOl);
                                        $dok->set('pafylt', 1);
                                        break;
                                    case 3:
                                        $nyCider = $denne_vakta->getCider();
                                        $nyCider['pafyll'] += $post['antall'];
                                        $denne_vakta->setCider($nyCider);
                                        $dok->set('pafylt', 1);
                                        break;
                                    case 4:
                                        $nyCarls = $denne_vakta->getCarlsberg();
                                        $nyCarls['pafyll'] += $post['antall'];
                                        $denne_vakta->setCarlsberg($nyCarls);
                                        $dok->set('pafylt', 1);
                                        break;
                                    case 5:
                                        $nyRikdom = $denne_vakta->getRikdom();
                                        $nyRikdom['pafyll'] += $post['antall'];
                                        $denne_vakta->setRikdom($nyRikdom);
                                        $dok->set('pafylt', 1);
                                        break;
                                }
                            }
                        }
                        $vaktaId = $denne_vakta->getBeboerId();
                        if ($vaktaId == 0 || Beboer::medId($vaktaId) == null) {
                            //TODO få til noe bedre her?
                            $vakta = Ansatt::medId(1);
                        } else {
                            $vakta = Beboer::medId($vaktaId);
                        }
                        if ($vakta == null) {
                            $vakta = Ansatt::medId(1);
                        }
                        $dok->set('skjulMeny', 1);
                        $dok->set('vakta', $vakta);
                        $dok->set('vaktSesj', $denne_vakta);
                        $dok->vis('journal_pafyll.php');
                        break;
                    case 'vaktbytte':
                        $denneVakt = VaktSesjon::getLatest();
                        if (isset($_POST) && isset($_POST['beboerId']) && is_numeric($_POST['beboerId'])) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if ($post['beboerId'] >= 0 && Beboer::medId($post['beboerId']) != null) {
                                $denneVakt->setBeboerId($post['beboerId']);
                            } //Torild
                            elseif ($post['beboerId'] == 0) {
                                $denneVakt->setBeboerId($post['beboerId']);
                            }
                        }
                        if ($denneVakt->getBeboerId() == 0) {
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
                    case 'signering':
                        $denne_vakta = VaktSesjon::getLatest();
                        if (isset($_POST)) {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if(isset($post['beboerId']) && is_numeric($post['beboerId'])){
                                $denne_vakta = VaktSesjon::avsluttVakt($denne_vakta);
                            }
                        }
                        $vaktaId = $denne_vakta->getBeboerId();
                        if ($vaktaId == 0 || Beboer::medId($vaktaId) == null) {
                            //TODO få til noe bedre her?
                            $vakta = Ansatt::medId(1);
                        } else {
                            $vakta = Beboer::medId($vaktaId);
                        }
                        if ($vakta == null) {
                            $vakta = Ansatt::medId(1);
                        }
                        $dok = new Visning($this->cd);
                        $dok->set('skjulMeny', 1);
                        $dok->set('denne_vakta', $denne_vakta);
                        $dok->set('vakta', $vakta);
                        $dok->vis('journal_signering.php');
                        break;
                    default:
                        $dok = new Visning($this->cd);
                        $dok->set('skjulMeny', 1);
                        $dok->vis('journal.php');
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
