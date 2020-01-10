<?php

namespace intern3;

class HelgaCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $beboer = $this->cd->getAktivBruker()->getPerson();
        $beboer_id = $beboer->getId();
        $denne_helga = Helga::getLatestHelga();
        //$gjestelista = $denne_helga->getGjesteliste();
        //$aar = $denne_helga->getAar();
        $dag_array = array(
            0 => 'Torsdag',
            1 => 'Fredag',
            2 => 'Lørdag'
        );

        switch ($aktueltArg) {

            case 'endregjest':
                if (($gjest = HelgaGjest::medId($this->cd->getSisteArg())) !== null
                    && $gjest->getVertId() === $beboer_id
                    && $gjest->getSendt() == 0

                ) {

                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    if (Funk::isValidEmail($post['epost'])) {
                        $gjest->setEpost($post['epost']);

                        Funk::setSuccess('Endret gjesten sin e-post!');
                    } else {
                        Funk::setError("{$post['epost']} er ikke en gyldig epostadresse!");
                    }

                    header('Location: ?a=helga/' . Helga::DAGER[$gjest->getDag()]);
                    exit();
                    break;
                }
            case 'gjestmodal':
                if (($gjest = HelgaGjest::medId($this->cd->getSisteArg())) !== null) {

                    $dok = new Visning($this->cd);
                    $gjest = HelgaGjesteObjekt::medAarEpost($gjest->getAar(), $gjest->getEpost());
                    $dok->set('gjest', $gjest);
                    $dok->vis('helga/gjestmodal.php');
                    break;
                }
            case 'beboermodal':
                if (!empty(Session::get('helga')) || !empty(Session::get('data'))) {
                    $beboer = Beboer::medId($this->cd->getSisteArg());

                    if (!is_null($beboer)) {
                        $oppretta = false;
                        if (isset($denne_helga->medEgendefinertAntall()[$beboer->getId()])) {
                            $oppretta = true;
                        }

                        $dok = new Visning($this->cd);
                        $dok->set('oppretta', $oppretta);
                        $dok->set('denne_helga', $denne_helga);
                        $dok->set('aar', $denne_helga->getAar());
                        $dok->set('beboer', $beboer);
                        $dok->vis('helga/beboermodal.php');
                        break;
                    }
                }
            case 'vervmodal':
                if (!empty(Session::get('helga')) || !empty(Session::get('data'))) {
                    $beboerListe = BeboerListe::aktive();
                    $dok = new Visning($this->cd);
                    $dok->set('beboerListe', $beboerListe);
                    $dok->vis('helga/helga_vervmodal.php');
                    break;
                }
            case 'general':
                if (!empty(Session::get('helga')) || !empty(Session::get('data'))) {
                    $dok = new Visning($this->cd);
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        $helga = Helga::medAar($post['aar']);

                        if ($this->cd->getSisteArg() === 'egendefinert') {
                            $beboer = Beboer::medId($post['beboer_id']);
                            //$dager = array($post['torsdag'], $post['fredag'], $post['lordag']);

                            $denne_helga->setMaxGjest($beboer->getId(), $post['torsdag'], $post['fredag'],
                                $post['lordag']);
                            Funk::setSuccess("Satte opp {$beboer->getFulltNavn()} med egetdefinert antall.");
                        } elseif ($this->cd->getSisteArg() === 'slettegendefinert') {
                            $beboer = Beboer::medId($post['beboer_id']);

                            $denne_helga->slettEgendefinert($beboer->getId());
                            Funk::setSuccess("Fjerna {$beboer->getFulltNavn()} fra å ha egetdefinert antall.");
                        } elseif (isset($post['verv']) && ($vervet = Helgaverv::medId($post['verv'])) != null) {
                            if (isset($post['fjern'])) {
                                $vervet->fjern($post['fjern']);
                                $_SESSION['success'] = 1;
                                $_SESSION['msg'] = "Fjerna beboeren!";
                            }
                        } elseif (isset($post['vervet'])) {
                            $postet = explode('&', $post['vervet']);
                            $beboerId = $postet[0];
                            $vervId = $postet[1];
                            if (($beboer = Beboer::medId($beboerId)) != null
                                && ($verv = Helgaverv::medId($vervId)) != null) {
                                $verv->leggTil($beboerId);

                                $_SESSION['success'] = 1;
                                $_SESSION['msg'] = "La til beboeren!";

                                header('Location: ?a=helga/general');
                                exit();
                            }
                        } elseif (isset($post['form']) == 'addverv') {
                            $st = DB::getDB()->prepare('INSERT INTO helgaverv (navn,tilgang) VALUES(:navn,:tilgang)');
                            $st->bindParam(':navn', $post['navn']);
                            $st->bindParam(':tilgang', $post['tilgang']);
                            $st->execute();

                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "La til vervet!";

                            header('Location: ?a=helga/general');
                            exit();
                        } elseif (isset($post['fjernverv'])) {
                            $st = DB::getDB()->prepare('DELETE FROM helgaverv_beboer WHERE id=:id');
                            $st->bindParam(':id', $post['fjernverv']);
                            $st->execute();

                            $st = DB::getDB()->prepare('DELETE FROM helgaverv WHERE id=:id');
                            $st->bindParam(':id', $post['fjernverv']);
                            $st->execute();

                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "Sletta vervet!";
                        } elseif (isset($post['endretilgang'])
                            && ($vervet = Helgaverv::medId($post['endretilgang'])) != null) {

                            $nyval = $vervet->getTilgang() > 0 ? 0 : 1;
                            $vervet->setTilgang($nyval);

                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "Endra tilgangen for dette vervet!";

                        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            if ($post['klar'] == 'on') {
                                $helga->setKlar();
                            }

                            $same = false;
                            foreach ($post as $key => $val) {

                                if ($key == 'SameMax') {
                                    $same = true;
                                }

                                $tmp = 'set' . $key;
                                if (is_string($tmp)) {
                                    if (is_callable(array($helga, $tmp))) {// method_exists($helga, $tmp)) {
                                        $helga->$tmp($val);
                                    }
                                }
                            }

                            if (!$same) {
                                $helga->setSameMax('off');
                            }
                        }

                        header('Location: ?a=helga/general');
                        exit();
                    }
                    $denne_helga = Helga::getLatestHelga();
                    $verv = Helgaverv::getAlle();
                    $beboerListe = BeboerListe::aktive();

                    $alle_helga = Helga::getAlleHelga();
                    $dok->set('beboerListe', $beboerListe);
                    $dok->set('helgaverv', $verv);
                    $dok->set('alle_helga', $alle_helga);
                    $dok->set('helga', $denne_helga);
                    $dok->vis('helga/helga_general.php');
                    break;
                }
            case 'inngang':
                if (!empty(Session::get('helga')) || !empty(Session::get('data')) || !empty(Session::get('helga_inngang'))) {
                    $dok = new Visning($this->cd);
                    if ($_SERVER['REQUkEST_METHOD'] === 'POST') {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if (isset($post['registrer']) && isset($post['gjestid']) && isset($post['verdi'])) {
                            $gjesten = HelgaGjest::byId($post['gjestid']);
                            //data: 'registrer=ok&gjestid=' + id + "&verdi=" + verdi,
                            if ($gjesten != null && $gjesten->getAar() == $denne_helga->getAar()) {
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
                    $antall_inne = $denne_helga->getAntallInnePerDag()[$dagen];
                    $antall_inviterte = $denne_helga->getAntallPerDag()[$dagen];
                    $gjesteliste_dag = HelgaGjesteListe::getGjesterUngrouped($denne_helga->getAar(), $dag);
                    $gjesteliste_dag_gruppert = HelgaGjesteListe::getGjesterGroupedbyHost($denne_helga->getAar(),
                        $dag);
                    $beboerlista = array();
                    foreach (BeboerListe::aktive() as $beboer) {
                        $beboerlista[$beboer->getId()] = $beboer;
                    }
                    $dok->set('antall_inne', $antall_inne);
                    $dok->set('antall_inviterte', $antall_inviterte);
                    $dok->set('gjesteliste_dag', $gjesteliste_dag);
                    $dok->set('gjesteliste_dag_gruppert', $gjesteliste_dag_gruppert);
                    //$dok->set('gjestelista', $gjestelista);
                    $dok->set('beboerliste', $beboerlista);
                    $dok->vis('helga/helga_inngang.php');
                    break;
                } else {
                    header('Location: ?a=helga');
                }

            case 'gjesteliste':
                if ($beboer->harHelgaTilgang()) {
                    $dagen = $this->cd->getSisteArg();

                    if (!in_array($dagen, array('fredag', 'lordag', 'torsdag'))) {
                        break;
                    }

                    switch ($dagen) {
                        case 'fredag':
                            $dag = 1;
                            break;
                        case 'lordag':
                            $dag = 2;
                            break;
                        case 'torsdag':
                        default:
                            $dag = 0;
                    }

                    $gjesteliste_dag = HelgaGjesteListe::getGjesterUngrouped($denne_helga->getAar(), $dag);
                    $beboerlista = array();
                    foreach (BeboerListe::aktive() as $beboer) {
                        $beboerlista[$beboer->getId()] = $beboer;
                    }
                    $dok = new Visning($this->cd);
                    $dok->set('gjesteliste_dag', $gjesteliste_dag);
                    $dok->set('beboerliste', $beboerlista);
                    $dok->vis('helga/helga_gjesteliste.php');
                    exit();
                }
            case 'gjestavkryss':
                if (!empty(Session::get('helga')) || !empty(Session::get('data')) || !empty(Session::get('helga_inngang'))) {
                    $dagen = $this->cd->getSisteArg();

                    if (!in_array($dagen, array('fredag', 'lordag', 'torsdag'))) {
                        break;
                    }

                    switch ($dagen) {
                        case 'fredag':
                            $dag = 1;
                            break;
                        case 'lordag':
                            $dag = 2;
                            break;
                        case 'torsdag':
                        default:
                            $dag = 0;
                    }

                    $gjesteliste_dag_gruppert = HelgaGjesteListe::getGjesterGroupedbyHost($denne_helga->getAar(),
                        $dag);
                    $beboerlista = array();
                    foreach (BeboerListe::aktive() as $beboer) {
                        $beboerlista[$beboer->getId()] = $beboer;
                    }
                    $dok = new Visning($this->cd);
                    $dok->set('gjesteliste_dag_gruppert', $gjesteliste_dag_gruppert);
                    $dok->set('beboerliste', $beboerlista);
                    $dok->vis('helga/helga_gjestavkryss.php');
                    exit();
                }

            case 'gjest':
                $sisteArg = $this->cd->getSisteArg();
                if ($sisteArg != $aktueltArg && is_numeric($sisteArg) &&
                    ($gjest = HelgaGjest::byId($sisteArg)) != null) {

                    $dok = new Visning($this->cd);
                    $dok->set('gjest', $gjest);
                    $dok->vis('helga/helga_gjest.php');

                }
                break;
            case 'registrer':
                if ($beboer->harHelgaTilgang()) {
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if (($gjesten = HelgaGjest::byId($post['gjestid'])) != null) {
                            if ($gjesten->getInne()) {
                                $gjesten->setInne(0);
                            } else {
                                $gjesten->setInne(1);
                            }
                        }
                    }
                    $dagen = $post['dag'];
                    switch ($dagen) {
                        case 'fredag':
                            $dag = 1;
                            break;
                        case 'lordag':
                            $dag = 2;
                            break;
                        case 'torsdag':
                        default:
                            $dag = 0;
                    }
                    $dok = new Visning($this->cd);

                    $antall_inne = $denne_helga->getAntallInnePerDag()[$dagen];
                    $antall_inviterte = $denne_helga->getAntallPerDag()[$dagen];
                    $gjesteliste_dag = HelgaGjesteListe::getGjesterUngrouped($denne_helga->getAar(), $dag);
                    $gjesteliste_dag_gruppert = HelgaGjesteListe::getGjesterGroupedbyHost($denne_helga->getAar(),
                        $dag);
                    $beboerlista = array();
                    foreach (BeboerListe::aktive() as $beboer) {
                        $beboerlista[$beboer->getId()] = $beboer;
                    }
                    $dok->set('antall_inne', $antall_inne);
                    $dok->set('antall_inviterte', $antall_inviterte);
                    $dok->set('gjesteliste_dag', $gjesteliste_dag);
                    $dok->set('gjesteliste_dag_gruppert', $gjesteliste_dag_gruppert);
                    //$dok->set('gjestelista', $gjestelista);
                    $dok->set('beboerliste', $beboerlista);
                    $dok->set('dag_tall', $dag);
                    $dok->vis('helga/helga_inngang.php');
                    break;
                }
            case 'reg':
                $sisteArg = $this->cd->getSisteArg();
                if ($sisteArg != 'reg' && strlen($sisteArg) == 128) {
                    $dok = new Visning($this->cd);

                    $st = DB::getDB()->prepare('SELECT * FROM helgagjest WHERE api_nokkel=:nokkel');
                    $st->bindParam(':nokkel', $sisteArg);
                    $st->execute();
                    $gjesten = null;
                    if ($st->rowCount() > 0) {
                        $gjesten = HelgaGjest::init($st);
                        if ($gjesten != null) {
                            $dok->set('gjesten', $gjesten);
                            $dok->set('success', 1);
                            $gjesten->setInne(1);
                        }
                    }
                    $dok->vis('helga/helga_reg_gjest.php');
                    exit();
                } else {
                    $dok = new Visning($this->cd);
                    $dok->set('success', 0);
                    $dok->vis('helga/helga_reg_gjest.php');
                    exit();
                }
            case 'helga':
            default:

                $bruker = $this->cd->getAktivBruker();
                $beboer = $bruker->getPerson();
                $helga = Helga::getLatestHelga();
                $gjester = HelgaGjesteObjekt::medAarVert($helga->getAar(), $beboer->getId());
                $count = HelgaGjesteListe::getGjesteCountBeboer($beboer->getId(), $helga->getAar());


                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $arg = $this->cd->getSisteArg();

                    switch ($arg) {

                        case 'slett':
                            $gjest = HelgaGjest::medId($post['id']);
                            $navn = $gjest->getNavn();
                            $alle_instanser = HelgaGjesteObjekt::medAarEpost($helga->getAar(), $gjest->getEpost());
                            $alle_instanser->slett();
                            print "Slettet gjesten $navn.";
                            break;
                        case 'endre':
                            $gjest = HelgaGjest::medId($post['id']);
                            $alle_instanser = HelgaGjesteObjekt::medAarEpost($helga->getAar(), $gjest->getEpost());
                            $days = HelgaCtrl::daysToNumeric(explode(';', $post['dager']));

                            if (count($days) > 3) {
                                exit("Plsno");
                            }

                            //die(var_dump(HelgaCtrl::day_equality($days, $alle_instanser->getDager())));
                            if (!HelgaCtrl::day_equality($days, $alle_instanser->getDager())) {
                                $navn = $gjest->getNavn();
                                $epost = $gjest->getEpost();
                                $alle_instanser->slett();
                                HelgaGjesteObjekt::addMultidayGjest($navn, $epost, $helga->getAar(), $days, $beboer);
                                $_SESSION['woopidoop'] = "yike";
                            }

                            if ($post['epost'] != $gjest->getEpost() && strlen($post['epost']) > 1 && strpos($post['epost'],
                                    "@") !== false) {
                                $alle_instanser->setEpost($post['epost']);
                                Funk::setSuccess("Endret epost til {$alle_instanser->getNavn()}.");
                                foreach ($alle_instanser->getInstanser() as $gjesten) {
                                    $gjesten->setSendt(0);
                                }
                                header('Location: ?a=helga');
                                exit();
                            }

                            print "Endret på gjesten {$gjest->getNavn()}.";

                            break;
                        case 'add':
                            $days = HelgaCtrl::daysToNumeric(explode(';', $post['dager']));

                            if (count($days) > 3) {
                                exit("Plsno");
                            }

                            if(count($days) < 1) {
                                print "Du må velge minst en dag!";
                                exit();
                            }

                            $actual = array();
                            foreach ($days as $day) {
                                if ($helga->kanLeggeTil($beboer->getId(), $day)) {
                                    $actual[] = $day;
                                }
                            }

                            if (count($actual) > 0) {
                                HelgaGjesteObjekt::addMultidayGjest($post['navn'], $post['epost'], $helga->getAar(),
                                    $days,
                                    $beboer);
                                print "La til gjestene!";
                            } else {
                                print "Du har dessverre gått tom for invitasjoner for denne invitasjonen. Prøv gjerne igjen!";
                            }

                            break;
                        case 'send_epost':
                            $gjest = HelgaGjest::medId($post['id']);
                            $alle_instanser = HelgaGjesteObjekt::medAarEpost($helga->getAar(), $gjest->getEpost());

                            foreach ($alle_instanser->getInstanser() as $gjesten) {
                                /* @var HelgaGjest $gjesten */
                                if (!$gjesten->getSendt()) {
                                    $gjesten->sendEpost();
                                }
                            }
                            print "Sendte epost med invitasjon(er) til {$gjesten->getNavn()}";
                            break;
                        default:
                            exit();
                    }
                    exit();
                }

                $status = array();
                foreach ([0, 1, 2] as $day) {
                    $status[$helga::DAGER[$day]] = HelgaGjesteListe::getGjesteCountDagBeboer($day, $beboer->getId(), $helga->getAar());
                }

                $dok = new Visning($this->cd);
                $dok->set('helga', $helga);
                $dok->set('beboer', $beboer);
                $dok->set('gjester', $gjester);
                $dok->set('count', $count);
                $dok->set('status', $status);
                $dok->vis('helga/helga_alt.php');
                exit();

            /*

                        $dok = new Visning($this->cd);
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                            if (isset($post['add']) && isset($post['navn']) && isset($post['epost']) && is_numeric($post['add'])) {
                                //Legg til gjest.
                                if (Funk::isValidEmail($post['epost'])) {
                                    $max_gjester = $denne_helga->getMaxGjest($this->cd->getAktivBruker()->getPerson()->getId(),
                                        $post['add']);

                                    $num_gjester_aktuell_dag = HelgaGjesteListe::getGjesteCountDagBeboer($post['add'],
                                        $this->cd->getAktivBruker()->getPerson()->getId(), $denne_helga->getAar());

                                    if ($num_gjester_aktuell_dag < $max_gjester) {
                                        //HelgaGjest::addGjest($post['navn'], $post['epost'], $beboer_id, $post['add'], $aar);
                                        $st = DB::getDB()->prepare('INSERT INTO helgagjest (navn, aar, epost, vert, dag ,inne, sendt_epost, api_nokkel)
                                        VALUES(:navn, :aar, :epost, :vert, :dag, :inne, :sendt_epost, :nokkel)');
                                        $nokkel = hash('sha512', Funk::generatePassword(30));
                                        $null = 0;
                                        $st->bindParam(':navn', $post['navn']);
                                        $st->bindParam(':aar', $aar);
                                        $st->bindParam(':epost', $post['epost']);
                                        $st->bindParam(':vert', $beboer_id);
                                        $st->bindParam(':inne', $null);
                                        $st->bindParam(':sendt_epost', $null);
                                        $st->bindParam(':dag', $post['add']);
                                        $st->bindParam(':nokkel', $nokkel);
                                        $st->execute();

                                        \PHPQRCode\QRcode::png("http://intern.singsaker.no/?a=helga/reg/" . $nokkel,
                                            PATH . '/www/qrkoder/' . $nokkel . ".png",
                                            'L',
                                            4,
                                            2);

                                    } else {
                                        print "Du har nådd maks gjestekapasitet for denne dagen!";
                                        exit();
                                    }

                                } else {
                                    print "Ugyldig epost!";
                                    exit();
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
                                if (HelgaGjest::belongsToBeboer($gjesteid,
                                        $beboer_id) && Funk::isValidEmail($gjesten->getEpost())) {
                                    $nettsiden = "http://intern.singsaker.no/qrkoder/" . $gjesten->getNokkel() . ".png";
                                    $dagen = $dag_array[$gjesten->getDag()];
                                    $datoen = date('Y-m-d',
                                        strtotime($denne_helga->getStartDato() . " +" . $gjesten->getDag() . " days"));
                                    $tittel = "[SING-HELGA] Du har blitt invitert til HELGA-" . $denne_helga->getAar();
                                    $beskjed = "<html><body>Hei, " . $gjesten->getNavn() . "! <br/><br/>Du har blitt invitert til "
                                        . $denne_helga->getTema() . "-" . "HELGA" . " av " . $beboer->getFulltNavn() .
                                        "<br/><br/>Denne invitasjonen gjelder for $dagen $datoen<br/><br/>
                                            Vi håper du ønsker å ta turen! Din billett for dagen finnes <a href='" . $nettsiden . "'>her</a><br/><br/>
                                            Med vennlig hilsen<br/>HELGA-" . $denne_helga->getAar() . "<br/><br/>
                                            <br/><br/><p>Dette er en automatisert melding. Feil? Vennligst ta kontakt
                                             med data@singsaker.no.</p></body></html>";

                                    Epost::sendEpost($gjesten->getEpost(), $tittel, $beskjed);
                                    $gjesten->setSendt(1);
                                    $dok->set('epostSendt', 1);
                                }
                                header('Location: ' . $_SERVER['REQUEST_URI']);
                                exit();
                            }
                            exit();
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
                        $max_gjeste_count = $denne_helga->getMaxGjest($this->cd->getAktivBruker()->getPerson()->getId(),
                            $dag_tall);
                        $ledige = $max_gjeste_count - $gjeste_count;
                        $dok->set('ledige', $ledige);
                        $dok->set('dag_tall', $dag_tall);
                        $dok->set('max_gjeste_count', $max_gjeste_count);
                        $dok->set('beboers_gjester', $beboers_gjester);
                        $dok->set('gjeste_count', $gjeste_count);
                        $dok->set('dagen', $dagen);
                        $dok->vis('helga/helga.php');
                        exit();*/
        }
    }

    public static function daysToNumeric($d)
    {
        $days = array();
        $valid = array(
            'torsdag' => 0,
            'fredag' => 1,
            'lordag' => 2
        );
        foreach ($d as $dag) {
            if (in_array($dag, $valid)) {
                $days[] = $valid[$dag];
            }
        }

        return $days;
    }

    public static function day_equality($a, $b) {

        if(count($a) != count($b)) {
            return false;
        }

        if(array_sum($a) != array_sum($b)) {
            return false;
        }

        return true;

    }
}