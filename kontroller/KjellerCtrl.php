<?php

namespace intern3;


class KjellerCtrl extends AbstraktCtrl
{

    private function leggTilVinMedBilde($path)
    {

        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $st = DB::getDB()->prepare('INSERT INTO vin (navn,bilde,pris,avanse,antall,typeId,beskrivelse, land) VALUES(
:navn,:bilde,:pris,:avanse,:antall,:typeId,:beskrivelse, :land)');

        $st->bindParam(':navn', $post['navn']);
        $st->bindParam(':bilde', $path);
        $st->bindParam(':pris', $post['pris']);
        $st->bindParam(':avanse', $post['avanse']);
        $st->bindParam(':antall', $post['antall']);
        $st->bindParam(':typeId', $post['type']);
        $st->bindParam(':beskrivelse', $post['beskrivelse']);
        $st->bindParam(':land', $post['land']);
        $st->execute();

        return Vin::medId(Funk::getLastVinId());
    }

    private function leggTilVinUtenBilde()
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $st = DB::getDB()->prepare('INSERT INTO vin (navn,pris,avanse,antall,typeId,beskrivelse, land) VALUES(
:navn,:pris,:avanse,:antall,:typeId,:beskrivelse,:land)');

        $st->bindParam(':navn', $post['navn']);
        $st->bindParam(':pris', $post['pris']);
        $st->bindParam(':avanse', $post['avanse']);
        $st->bindParam(':avanse', $post['avanse']);
        $st->bindParam(':antall', $post['antall']);
        $st->bindParam(':typeId', $post['type']);
        $st->bindParam(':beskrivelse', $post['beskrivelse']);
        $st->bindParam(':land', $post['land']);
        $st->execute();

        return Vin::medId(Funk::getLastVinId());
    }

    public function bestemHandling()
    {
        if (LogginnCtrl::getAktivBruker() == null || LogginnCtrl::getAktivBruker()->getPerson() == null ||
            !LogginnCtrl::getAktivBruker()->getPerson()->erKjellerMester()
        ) {
            header('Location: ?a=diverse');
            exit();
        }
        $aktueltArg = $this->cd->getAktueltArg();
        $vintypene = Vintype::getAlle();
        $vinene = Vin::getAlle();
        $dok = new Visning($this->cd);
        switch ($aktueltArg) {
            case 'leggtil':

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                        $tillatte_filtyper = array('jpg', 'jpeg', 'png', 'gif');
                        $bildet = $_FILES['image']['tmp_name'];
                        $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));
                        $bildets_navn = md5(Funk::generatePassword(15) . time()) . '.' . $file_ext;

                        if (in_array($file_ext, $tillatte_filtyper)) {
                            $vinbilder_path = dirname(__DIR__) . '/www/vinbilder/';
                            if (!move_uploaded_file($_FILES['image']['tmp_name'], $vinbilder_path . $bildets_navn)) {
                                Throw new \RuntimeException("dafuq");
                            }
                            chmod($vinbilder_path . $bildets_navn, 0644);

                            $vinen = $this->leggTilVinMedBilde($bildets_navn);

                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "Du la til en vin med navn " . $vinen->getNavn();

                            header('location: ?a=kjeller/admin/' . $vinen->getId());
                            exit();
                        }
                    }
                    $vinen = $this->leggTilVinUtenBilde();
                    $_SESSION['success'] = 1;
                    $_SESSION['msg'] = "Du la til en vin med navn " . $vinen->getNavn();

                    header('location: ?a=kjeller/admin/' . $vinen->getId());
                    exit();
                }


                $dok->set('vintyper', $vintypene);
                $dok->vis('kjeller_add.php');
                break;
            case 'admin':
                $sisteArg = $this->cd->getSisteArg();
                if ($sisteArg != 'admin' && is_numeric($sisteArg)) {
                    $vinen = Vin::medId($sisteArg);
                    if (isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                        $keys = array('navn' => $vinen->getNavn(),
                            'pris' => $vinen->getPris(),
                            'avanse' => $vinen->getAvanse(),
                            'type' => $vinen->getTypeId(),
                            'land' => $vinen->getLand(),
                            'beskrivelse' => $vinen->getBeskrivelse()
                        );

                        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                            $tillatte_filtyper = array('jpg', 'jpeg', 'png', 'gif');
                            $bildet = $_FILES['image']['tmp_name'];
                            $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));
                            $bildets_navn = md5(Funk::generatePassword(15) . time()) . '.' . $file_ext;

                            if (in_array($file_ext, $tillatte_filtyper)) {
                                $vinbilder_path = dirname(__DIR__) . '/www/vinbilder/';
                                if (!move_uploaded_file($_FILES['image']['tmp_name'], $vinbilder_path . $bildets_navn)) {
                                    Throw new \RuntimeException("dafuq");
                                }
                                chmod($vinbilder_path . $bildets_navn, 0644);

                                $this->updateVinMedBilde($vinen->getId(), $bildets_navn);

                                $_SESSION['success'] = 1;
                                $_SESSION['msg'] = "Du endret vinen med navn " . $vinen->getNavn();

                                header('location: ' . $_SERVER['REQUEST_URI']);
                                exit();
                            }
                        }

                        foreach ($post as $key => $val) {
                            if ($post[$key] != $keys[$key]) {
                                $this->updateVinUtenBilde($vinen->getId());
                                $_SESSION['success'] = 1;
                                $_SESSION['msg'] = "Du endret vinen med navn " . $vinen->getNavn();

                                header('location: ' . $_SERVER['REQUEST_URI']);
                                exit();
                                break;
                            }
                        }
                    }

                    //Redeclare i tilfelle endringer.
                    $vinen = Vin::medId($sisteArg);
                    $dok->set('vinen', $vinen);
                    $dok->set('vintyper', $vintypene);
                    $dok->vis('kjeller_endre_vin.php');
                    exit();
                }

                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($post['slett']) && is_numeric($post['slett']) && Vin::medId($post['slett']) != null) {
                        $st = DB::getDB()->prepare('UPDATE vin SET slettet=1 WHERE id=:id');
                        $st->bindParam(':id', $post['slett']);
                        $st->execute();
                        $vinene = Vin::getAlle();
                        $dok->set('vinene', $vinene);
                        $dok->vis('kjeller_admin.php');
                        exit();
                    }
                }
                $vinene = Vin::getAlle();
                $dok->set('vinene', $vinene);
                $dok->vis('kjeller_admin.php');
                break;
            case 'slettet_vin':
                $dok = new Visning($this->cd);
                if (isset($_POST) && count($_POST) > 0) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($post['slett']) && ($vinen = Vin::medId($post['slett'])) != null && Vinkryss::antallKryssVinId($post['slett']) == 0) {
                        $st = DB::getDB()->prepare('DELETE FROM vin WHERE id=:id');
                        $st->bindParam(':id', $post['slett']);
                        $st->execute();
                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Slettet vinen med navn " . $vinen->getNavn() . " permanent.";
                    } elseif (isset($post['slett']) && ($vinen = Vin::medId($post['slett'])) != null && Vinkryss::antallKryssVinId($post['slett']) != 0) {
                        $dok->set('tilbakemelding', "Kan ikke slette en vin som har blitt krysset!");
                    }
                }
                $vinene = Vin::getAlle();
                $dok->set('vinene', $vinene);
                $dok->vis('kjeller_slettet_admin.php');
                break;
            case 'regning':
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($post['beboer']) && is_numeric($post['beboer']) && isset($post['vin'])
                        && isset($post['antall']) && isset($post['dato'])
                    ) {

                        $beboeren = Beboer::medId($post['beboer']);
                        $vinen = Vin::medId($post['vin']);

                        if ($vinen != null && $beboeren != null && ($vinen->getAntall() >= $post['antall'])) {
                            $prisen = $post['antall'] * $vinen->getPris() * $vinen->getAvanse();
                            $st = DB::getDB()->prepare('INSERT INTO vinkryss (antall,tiden,fakturert,vinId,beboerId,prisen) VALUES(
                            :antall,:tiden,0,:vinId,:beboerId,:prisen)');
                            $st->bindParam(':antall', $post['antall']);
                            $st->bindParam(':tiden', $post['dato']);
                            $st->bindParam(':vinId', $post['vin']);
                            $st->bindParam(':beboerId', $post['beboer']);
                            $st->bindParam(':prisen', $prisen);
                            $st->execute();

                            $st_1 = DB::getDB()->prepare('UPDATE vin SET antall=:antall WHERE id=:id');
                            $st_1->bindParam(':id', $vinen->getId());
                            $nytt_antall = $vinen->getAntall() - $post['antall'];
                            $st_1->bindParam(':antall', $nytt_antall);
                            $st_1->execute();

                            $string = "Du registrerte " . $post['antall'] . " vin med navn " . $vinen->getNavn() . " på " . $beboeren->getFulltNavn();
                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = $string;
                        } elseif ($vinen->getAntall() < $post['antall']) {
                            $_SESSION['error'] = 1;
                            $_SESSION['msg'] = "La ikke til regning! Du kan ikke krysse flere vin av denne typen enn det vi har i varebeholdningen!
                            <br/>Det er forløpig igjen " . $vinen->getAntall() . " av vinen med navn " . $vinen->getNavn() . " og du forsøkte å krysse " . $post['antall'] . ".";
                        }
                    }
                }
                $beboerlista = BeboerListe::aktive();
                $dok->set('vinene', $vinene);
                $dok->set('beboerlista', $beboerlista);
                $dok->vis('kjeller_regning.php');
                break;
            case 'lister':
                $sisteArg = $this->cd->getSisteArg();
                if ($sisteArg != 'lister') {
                    switch ($sisteArg) {
                        case 'varebeholdning_utskrift':
                            $dok->set('vinene', $vinene);
                            $dok->vis('kjeller_lister_varebeholdning_utskrift.php');
                            exit();
                        case 'rapport':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach ($beboerlista as $beboer) {
                                $beboer_vin = array('beboer' => $beboer,
                                    'antall' => 0,
                                    'kostnad' => 0);
                                $alle_kryss = Vinkryss::getAlleIkkeFakturertByBeboerId($beboer->getId());
                                foreach ($alle_kryss as $krysset) {
                                    $beboer_vin['antall'] += round($krysset->getAntall(), 2);
                                    $beboer_vin['kostnad'] += round($krysset->getKostnad() * $krysset->getVin()->getAvanse(), 2);
                                }
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_antall_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_lister_rapport.php');
                            exit();
                        case 'beboere_vin':
                            if (isset($_POST) && isset($_POST['fakturer']) && $_POST['fakturer'] == 1) {
                                $st = DB::getDB()->prepare('UPDATE vinkryss SET fakturert=1 WHERE fakturert=0');
                                $st->execute();

                                $st2 = DB::getDB()->prepare('INSERT INTO vin_fakturert () VALUES ()');
                                $st2->execute();

                                $_SESSION['success'] = 1;
                                $_SESSION['msg'] = "Fakturerte periode!";
                                header('Location: ?a=kjeller/lister/beboere_vin');
                            }

                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach ($beboerlista as $beboer) {
                                $ikke_fakturert = Vinkryss::getAlleIkkeFakturertByBeboerId($beboer->getId());
                                if (count($ikke_fakturert) < 1) {
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach ($ikke_fakturert as $vin_kryss) {
                                    if (!isset($vin_array[$vin_kryss->getVinId()]) || $vin_array[$vin_kryss->getVinId()] == null) {
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2),
                                            'antall' => round($vin_kryss->getAntall(), 2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(), 2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin.php');
                            exit();
                        case 'beboere_vin_utskrift':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach ($beboerlista as $beboer) {
                                $ikke_fakturert = Vinkryss::getAlleIkkeFakturertByBeboerId($beboer->getId());
                                if (count($ikke_fakturert) < 1) {
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach ($ikke_fakturert as $vin_kryss) {
                                    if (!isset($vin_array[$vin_kryss->getVinId()]) || $vin_array[$vin_kryss->getVinId()] == null) {
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2),
                                            'antall' => round($vin_kryss->getAntall(), 2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(), 2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin_utskrift.php');
                            exit();
                        case 'beboere_vin_fakturerte':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach ($beboerlista as $beboer) {
                                $ikke_fakturert = Vinkryss::getAlleFakturertByBeboerId($beboer->getId());
                                if (count($ikke_fakturert) < 1) {
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach ($ikke_fakturert as $vin_kryss) {
                                    if (!isset($vin_array[$vin_kryss->getVinId()]) || $vin_array[$vin_kryss->getVinId()] == null) {
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2),
                                            'antall' => round($vin_kryss->getAntall(), 2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(), 2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin_fakturert.php');
                            exit();
                        case 'beboere_vin_utskrift_fakturerte':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach ($beboerlista as $beboer) {
                                $ikke_fakturert = Vinkryss::getAlleFakturertByBeboerId($beboer->getId());
                                if (count($ikke_fakturert) < 1) {
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach ($ikke_fakturert as $vin_kryss) {
                                    if (!isset($vin_array[$vin_kryss->getVinId()]) || $vin_array[$vin_kryss->getVinId()] == null) {
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2),
                                            'antall' => round($vin_kryss->getAntall(), 2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad() * $vin_kryss->getVin()->getAvanse(), 2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(), 2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin_fakturert_utskrift.php');
                            exit();
                    }
                }
                $dok->vis('kjeller_lister.php');
                break;
            case 'add_type':
                $sisteArg = $this->cd->getSisteArg();
                if ($sisteArg != 'add_type' && is_numeric($sisteArg) && Vintype::medId($sisteArg) != null) {
                    if (isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if (isset($post['navn'])) {
                            $st = DB::getDB()->prepare('UPDATE vintype SET navn=:navn WHERE id=:id');
                            $st->bindParam(':navn', $post['navn']);
                            $st->bindParam(':id', $sisteArg);
                            $st->execute();
                            header('Location: ?a=kjeller/add_type');
                        }
                    }
                    $vintypen = Vintype::medId($sisteArg);
                    $dok->set('vintypen', $vintypen);
                    $dok->vis('kjeller_endre_typen.php');
                    exit();
                }
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (LogginnCtrl::getAktivBruker()->getPerson()->erKjellerMester()) {
                        if (isset($post['navn'])) {
                            $st = DB::getDB()->prepare('INSERT INTO vintype (navn) VALUES(:navn)');
                            $st->bindParam(':navn', $post['navn']);
                            $st->execute();
                        } elseif (isset($post['slett']) && is_numeric($post['slett'])) {
                            if (Vintype::antallVinAvType($post['slett']) == 0) {
                                $st = DB::getDB()->prepare('DELETE FROM vintype WHERE id=:id');
                                $st->bindParam(':id', $post['slett']);
                                $st->execute();
                                $_SESSION['success'] = 1;
                                $_SESSION['msg'] = "Slettet en vintype!";
                            } else {
                                $_SESSION['error'] = 1;
                                $_SESSION['msg'] = "Du kan ikke slette en vintype som er assosiert med en vin!";
                            }

                        }
                    }
                }
                $vintyper = Vintype::getAlle();
                $dok->set('vintyper', $vintyper);
                $dok->vis('kjeller_add_type.php');
                break;
            case 'svinn':
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    if (isset($post['vin']) && is_numeric($post['vin']) && Vin::medId($post['vin']) != null
                        && Vin::medId($post['vin'])->getAntall() > 0 && isset($post['antall'])
                        && is_numeric($post['antall']) && Vin::medId($post['vin'])->getAntall() - $post['antall'] >= 0
                    ) {
                        $nytt_antall = Vin::medId($post['vin'])->getAntall() - $post['antall'];
                        $id = $post['vin'];
                        $st = DB::getDB()->prepare('UPDATE vin SET antall=:antall WHERE id=:id');
                        $st->bindParam(':antall', $nytt_antall);
                        $st->bindParam(':id', $id);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('INSERT INTO vinsvinn (antall,tidspunkt,vin_id) VALUES(:antall,:tid,:vin_id)');
                        $st2->bindParam(':antall', $post['antall']);
                        $st2->bindParam(':tid', $post['dato']);
                        $st2->bindParam(':vin_id', $post['vin']);
                        $st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Registrerte svinn!<br/>Antall: " . $post['antall'] . "<br/> Navn: " . Vin::medId($post['vin'])->getNavn();
                        header('Location: ?a=kjeller/svinn');
                        exit();
                    }
                }
                $vinene = Vin::getAlle();
                $dok->set('vinene', $vinene);
                $dok->vis('kjeller_svinn.php');
                break;
            case 'pafyll':
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($post['vin']) && Vin::medId($post['vin']) != null && isset($post['antall']) && is_numeric($post['antall'])) {
                        $vinen = Vin::medId($post['vin']);
                        $nytt_antall = $vinen->getAntall() + $post['antall'];
                        $st = DB::getDB()->prepare('UPDATE vin SET antall=:antall WHERE id=:id');
                        $st->bindParam(':antall', $nytt_antall);
                        $st->bindParam(':id', $post['vin']);

                        $st2 = DB::getDB()->prepare('INSERT INTO vinpafyll (antall,vin_id) VALUES(:antall,:vin_id)');
                        $st2->bindParam(':antall', $post['antall']);
                        $st2->bindParam(':vin_id', $post['vin']);

                        $st->execute();
                        $st2->execute();

                        $vinen = Vin::medId($post['vin']);
                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Fylte på vinen med navn " . $vinen->getNavn() . ". Antaller vi har av disse er nå: " . $vinen->getAntall();
                        header('Location: ?a=kjeller/pafyll');
                        exit();
                    }
                }

                $vinene = Vin::getAlle();
                $dok->set('vinene', $vinene);
                $dok->vis('kjeller_pafyll.php');
                break;
            case 'regler':
                
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if(strlen($post['regel']) > 1){
                        Vinregel::nyRegel($_POST['regel']);
                        Funk::setSuccess("Regelen ble endret!");
                        header('Location: ?a=kjeller/regler');
                        exit();
                    }
                }
                
                $regel = Vinregel::getRegel();
                $dok->set('regel', $regel);
                $dok->vis('kjeller_regel.php');
                break;
                
            case 'oversikt':
                $transaksjoner = Vinkryss::getAlle();

                $dok->set('transaksjoner', $transaksjoner);
                $dok->vis('kjeller_kryss_oversikt.php');
                break;
            default:
                $dok->vis('kjeller_hoved.php');
        }
    }

    private
    function insertVin($medbilde = false, $bildenavn)
    {
        $bildet = $medbilde ? $bildenavn : ' ';
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $st = DB::getDB()->prepare('INSERT INTO vin (navn,bilde,pris,avanse,antall,typeId,beskrivelse, land) VALUES(
:navn,:bilde,:pris,:avanse,:antall,:typeId,:beskrivelse, :land)');

        $st->bindParam(':navn', $post['navn']);
        $st->bindParam(':bilde', $bildet);
        $st->bindParam(':pris', $post['pris']);
        $st->bindParam(':avanse', $post['avanse']);
        $st->bindParam(':antall', $post['antall']);
        $st->bindParam(':typeId', $post['type']);
        $st->bindParam(':beskrivelse', $post['beskrivelse']);
        $st->bindParam(':land', $post['land']);
        $st->execute();
        return true;
    }

    private
    function updateVinUtenBilde($id)
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $st = DB::getDB()->prepare('UPDATE vin SET navn=:navn,pris=:pris,avanse=:avanse,
        typeId=:typeId,beskrivelse=:beskrivelse,land=:land WHERE id=:id');

        $st->bindParam(':id', $id);
        $st->bindParam(':navn', $post['navn']);
        $st->bindParam(':pris', $post['pris']);
        $st->bindParam(':avanse', $post['avanse']);
        $st->bindParam(':typeId', $post['type']);
        $st->bindParam(':beskrivelse', $post['beskrivelse']);
        $st->bindParam(':land', $post['land']);
        $st->execute();
        return true;
    }

    private function updateVinMedBilde($id, $path)
    {

        $st = DB::getDB()->prepare('UPDATE vin SET bilde=:bilde WHERE id=:id');
        $st->bindParam(':bilde', $path);
        $st->bindParam(':id', $id);
        $st->execute();

        $this->updateVinUtenBilde($id);

    }
}

?>