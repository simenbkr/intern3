<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $vaktbytteListe = VaktbytteListe::etterVakttype();
        $sisteArg = $this->cd->getSisteArg();

        if($sisteArg == 'setvar'){
            $_SESSION['semester'] = "var";
        }
        elseif($sisteArg == 'sethost'){
            $_SESSION['semester'] = "host";
        }
        elseif($sisteArg == 'setna'){
            $_SESSION['semester'] = "frana";
        }
        elseif ($aktueltArg == 'bytte') {
            $dok = new Visning($this->cd);
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                //foreach($post as $key => $val){setcookie($key,$val);}
                //data: 'vaktbytte=1&id=' + id +'&action=' + action + "&passord=" + passord + "&passordet=" + passordet + "&merknad=" + merknad,
                //var action = 0; //1 = gisbort, 0 = byttes
                //Legg til vakt i "byttemarked".
                if (isset($post['vaktbytte']) && $post['vaktbytte'] == 1 && isset($post['action']) && isset($post['id']) && is_numeric($post['id'])) {
                    $action = $post['action'];
                    $id = $post['id'];
                    $merknad = $post['merknad'];
                    $passord = $post['passord'];
                    $passordet = $post['passordet'];

                    $vaktInstans = Vakt::medId($id);
                    if ($vaktInstans != null && ($vaktInstans->getBruker() == LogginnCtrl::getAktivBruker()) || in_array($action, array(0, 1, '0', '1'))) {
                        Vaktbytte::nyttVaktBytte($id, $action, $merknad, $passord, $passordet);
                        $st = DB::getDB()->prepare('UPDATE vakt SET bytte=1 WHERE id=:id');
                        $st->bindParam(':id', $id);
                        $st->execute();

                        $vaktbyttetId = Vaktbytte::medVaktId($id)->getId();
                        $st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=:vaktbytte_id WHERE id=:id');
                        $st2->bindParam(':id', $id);
                        $st2->bindParam(':vaktbytte_id',$vaktbyttetId);
                        $st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du la til et vaktbytte for " . $vaktInstans->toString();

                        //Send epost til de som ønsker å bli opplyst om dette.
                        if ($passordet == 0) {
                            $mottakere = "";
                            foreach (BeboerListe::harVakt() as $beboer) {
                                if($beboer->vilHaBytteGiVarsel()){
                                    $mottakere .= $beboer->getEpost() . ",";
                                }
                            }
                            $mottakere = rtrim($mottakere, ',');
                            $tittel = "[SING-INTERN] Det er en ledig vakt på byttemarkedet!";
                            $tekst = "<html>(Dette er en automatisert beskjed)<br/><br/>" . $vaktInstans->getBruker()->getPerson()->getFulltNavn() .
                                " har lagt ut en vakt på byttemarkedet. Dette er en " . $vaktInstans->getVakttype() . ". vakt " . $vaktInstans->getDato() .
                                "<br/> Logg inn på <a href='https://intern.singsaker.no'>Internsida</a> for å se mer.<br/><br/></html>";
                            Epost::sendEpost($mottakere, $tittel, $tekst);
                        }
                    }
                }

                //data: 'vaktbytte=2&id=' + id +'&vaktId=' + vaktId,
                //Slett eget vaktbytte (dvs ta egen vakt "tilbake").
                elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 2 && isset($post['id']) && isset($post['vaktId'])) {
                    $id = $post['id'];
                    $vaktId = $post['vaktId'];
                    $vaktInstans = Vakt::medId($vaktId);
                    $vaktbyttet = Vaktbytte::medId($post['id']);
                    if ($vaktInstans != null && $vaktInstans->getBruker() == LogginnCtrl::getAktivBruker()) {
                        Vaktbytte::slettEgetVaktBytte($id, $vaktId);
                        $st = DB::getDB()->prepare('UPDATE vakt SET bytte=0 WHERE id=:id');
                        $st->bindParam(':id', $vaktId);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=NULL WHERE id=:id');
                        $st2->bindParam(':id', $vaktId);
                        $st2->execute();

                        $ting = "(id=" . implode(' OR id=', $vaktbyttet->getForslagIder()) . ")";
                        DB::getDB()->query('UPDATE vakt SET vaktbytte_id=0 WHERE ' . $ting);

                        $_SESSION['error'] = 1;
                        $_SESSION['msg'] = "Du slettet ditt eget vaktbytte for " . $vaktInstans->toString();
                    }
                }
                //data: 'vaktbytte=3&id=' + id + '&vaktId=' + vaktId,
                //Ta en vakt som er blitt gitt bort
                elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 3 && isset($post['id']) && isset($post['vaktId'])) {
                    $id = $post['id'];
                    $vaktId = $post['vaktId'];
                    $vaktInstans = Vakt::medId($vaktId);
                    $vaktByttet = Vaktbytte::medId($id);
                    if ($vaktInstans != null && $vaktInstans->getBytte() == 1 && $vaktByttet != null && $vaktByttet->getVaktId() == $vaktId) {
                        $brukerId = LogginnCtrl::getAktivBruker()->getId();
                        Vaktbytte::taVakt($vaktId, $brukerId);
                        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
                        $st->bindParam(':id', $id);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=NULL WHERE id=:id');
                        $st2->bindParam(':id', $vaktId);
                        $st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du tok en " . $vaktInstans->toString() . " som ble gitt bort.";
                    }
                }
                //data: 'vaktbytte=4&id=' + id + '&vaktId=' + vaktId + "&passordet=" + passordet,
                //Ta en passordbeskyttet vakt som er blitt gitt bort.
                elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 4 && isset($post['id']) && isset($post['vaktId'])
                    && isset($post['passordet'])
                ) {
                    $id = $post['id'];
                    $vaktId = $post['vaktId'];
                    $vaktInstans = Vakt::medId($vaktId);
                    $vaktByttet = Vaktbytte::medId($id);
                    $passordet = $post['passordet'];
                    if ($vaktInstans != null && $vaktInstans->getBytte() == 1 && $vaktByttet != null && $vaktByttet->getVaktId() == $vaktId
                        && $vaktByttet->harPassord() && $vaktByttet->stemmerPassord($passordet)
                    ) {

                        $brukerId = LogginnCtrl::getAktivBruker()->getId();
                        Vaktbytte::taVakt($vaktId, $brukerId);
                        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
                        $st->bindParam(':id', $id);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=NULL WHERE id=:id');
                        $st2->bindParam(':id', $vaktId);
                        $st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du tok en passordbeskyttet " . $vaktInstans->toString();
                    }
                    if ($vaktByttet->harPassord() && !$vaktByttet->stemmerPassord($passordet)) {
                        $dok->set('feilPassord', 1);
                        $_SESSION['error'] = 1;
                        $_SESSION['msg'] = "Feil passord!";
                    }
                }
                //data: 'vaktbytte=5&fraId=' + fraId + '&tilId=' + tilId
                //Legge til forslag for vaktbytte uten passord.
                elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 5 && isset($post['fraId']) && isset($post['tilId'])) {
                    $fraVakt = Vakt::medId($post['fraId']);
                    $tilVakt = Vakt::medId($post['tilId']);
                    $vaktbyttet = Vaktbytte::medVaktId($fraVakt->getId());
                    if (!($fraVakt == null || $tilVakt == null || $vaktbyttet == null || $tilVakt->getBruker() != LogginnCtrl::getAktivBruker())) {
                        //Ok, all good, lets go!
                        $forslag = $vaktbyttet->getForslagIder();
                        if (in_array($tilVakt->getId(), $forslag)) {
                            exit();
                        }
                        if (sizeof($forslag) == 0) {
                            //Ingen forslag fra før av.
                            $forslag = $tilVakt->getId();
                        } else {
                            //Har minst ett forslag fra før av.
                            $forslag[] = $tilVakt->getId();
                            $forslag = implode(',', array_filter($forslag));
                        }
                        $st = DB::getDB()->prepare('UPDATE vaktbytte SET forslag=:forslag WHERE id=:id');
                        $st->bindParam(':id', $vaktbyttet->getId());
                        $st->bindParam(':forslag', $forslag);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=:vaktbytte_id WHERE id=:id');
                        $st2->bindParam(':id', $post['tilId']);
                        // $vaktbyttet_id = (($dummy = $tilVakt->getVaktbytteDenneErMedIId())[] = $vaktbyttet->getId());
                        $vaktbyttet_id = $tilVakt->getVaktbytteDenneErMedIId();
                        $vaktbyttet_id[] = $vaktbyttet->getId();
                        $vaktbyttet_id = implode(',', array_filter($vaktbyttet_id));
                        $st2->bindParam(':vaktbytte_id', $vaktbyttet_id);
                        $st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du la til et bytteforslag for " . $fraVakt->toString() . " mot " . $tilVakt->toString();
                    }

                }
                //data: 'vaktbytte=6&fraId=' + fraId + '&tilId=' + tilId + "&passordet=" + passordet,
                //Legge til forslag for vaktbytte med passord
                elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 6 && isset($post['fraId']) && isset($post['tilId']) && isset($post['passordet'])) {
                    $fraVakt = Vakt::medId($post['fraId']);
                    $tilVakt = Vakt::medId($post['tilId']);
                    $vaktbyttet = Vaktbytte::medVaktId($fraVakt->getId());
                    if (!$vaktbyttet->stemmerPassord($post['passordet'])) {
                        $dok->set('feilPassord', 1);
                        $_SESSION['error'] = 1;
                        $_SESSION['msg'] = "Feil passord for " . $fraVakt->toString();
                    } elseif (!($fraVakt == null || $tilVakt == null || $vaktbyttet == null || $tilVakt->getBruker() != LogginnCtrl::getAktivBruker()
                        || !$vaktbyttet->stemmerPassord($post['passordet']))
                    ) {

                        //Ok, all good, lets go!
                        $forslag = $vaktbyttet->getForslagIder();
                        if (in_array($tilVakt->getId(), $forslag)) {
                            exit();
                        }
                        if (sizeof($forslag) == 0) {
                            $forslag = $tilVakt->getId();
                        } else {
                            $forslag[] = $tilVakt->getId();
                            $forslag = implode(',', array_filter($forslag));
                        }
                        $st = DB::getDB()->prepare('UPDATE vaktbytte SET forslag=:forslag WHERE id=:id');
                        $st->bindParam(':id', $vaktbyttet->getId());
                        $st->bindParam(':forslag', $forslag);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=:vaktbytte_id WHERE id=:id');
                        $st2->bindParam(':id', $post['tilId']);
                        //$vaktbyttet_id = (($dummy = $tilVakt->getVaktbytteDenneErMedIId())[] = $vaktbyttet->getId());
                        $vaktbyttet_id = $tilVakt->getVaktbytteDenneErMedIId();
                        $vaktbyttet_id[] = $vaktbyttet->getId();
                        $vaktbyttet_id = implode(',', array_filter($vaktbyttet_id));
                        $st2->bindParam(':vaktbytte_id', $vaktbyttet_id);
                        $st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du la til et vaktbytteforslag for " . $fraVakt->toString() . " mot " . $tilVakt->toString();
                    }
                }
                //data: 'vaktbytte=7&fraId=' + fraId + '&tilId=' + tilId,
                //Godta et bytteforslag
                elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 7 && isset($post['fraId']) && isset($post['tilId'])) {
                    $fraVakt = Vakt::medId($post['fraId']);
                    $tilVakt = Vakt::medId($post['tilId']);
                    $Vaktbyttet = Vaktbytte::medVaktId($fraVakt->getId());
                    $aktivBruker = LogginnCtrl::getAktivBruker();
                    if ($fraVakt != null && $tilVakt != null && $Vaktbyttet != null && $fraVakt->getBruker() == $aktivBruker
                        && $fraVakt->getBytte() && in_array($tilVakt->getId(), $Vaktbyttet->getForslagIder())
                    ) {

                        $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id,bytte=0,bekreftet=1,autogenerert=0,vaktbytte_id=0 WHERE id=:id');
                        $st->bindParam(':bruker_id', $tilVakt->getBrukerId());
                        $st->bindParam(':id', $fraVakt->getId());

                        $st2 = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id,bytte=0,bekreftet=1,autogenerert=0,vaktbytte_id=0 WHERE id=:id');
                        $st2->bindParam(':bruker_id', $fraVakt->getBrukerId());
                        $st2->bindParam(':id', $tilVakt->getId());

                        $st3 = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
                        $st3->bindParam(':id', $Vaktbyttet->getId());


                        //Jesus mother fucking christ foreach loops fy faen fuck this shit man hvorfor har vi ikke bare
                        //en egen mange-til-mange-tabell?! Because legacy before launch thats why bitch.
                        foreach($Vaktbyttet->getForslagVakter() as $vakt){
                            $vakt->slettVaktbytteIdFraInstans($Vaktbyttet->getId());
                        }
                        foreach($fraVakt->getVaktbytteDenneErMedIId() as $id){
                            Vaktbytte::medId($id)->slettForslag($fraVakt->getId());
                        }
                        foreach($tilVakt->getVaktbytteDenneErMedIId() as $id){
                            Vaktbytte::medId($id)->slettForslag($tilVakt->getId());
                        }
                        $fraVakt->slettVaktbytteIdFraInstans($Vaktbyttet->getId());
                        $tilVakt->slettVaktbytteIdFraInstans($Vaktbyttet->getId());
                        $st->execute();
                        $st2->execute();
                        $st3->execute();

                       // $ting = "(id=" . implode(' OR id=', $Vaktbyttet->getForslagIder()) . ")";

                        //DB::getDB()->query('UPDATE vakt SET vaktbytte_id=0 WHERE ' . $ting);

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du godtok en byttehandel - du ga bort " . $fraVakt->toString() . " og mottok
                         " . $tilVakt->toString();
                    }
                }
                //data: 'vaktbytte=8&vaktbyttet=' + vaktbytte + '&vakt=' + vakt,
                //Trekk et forslag fra et bytte.
                elseif(isset($post['vaktbytte']) && $post['vaktbytte'] == 8 && isset($post['vaktbyttet']) && isset($post['vakt'])){
                    $vaktbyttet = Vaktbytte::medId($post['vaktbyttet']);
                    $vakta = Vakt::medId($post['vakt']);

                    if($vakta != null && $vaktbyttet != null && in_array($vaktbyttet->getId(), $vakta->getVaktbytteDenneErMedIId())
                    && LogginnCtrl::getAktivBruker()->getPerson()->getId() == $vakta->getBruker()->getPerson()->getId()){
                        $nye_forslag = array();
                        foreach($vaktbyttet->getForslagIder() as $id){
                            if($id == $vakta->getId()){
                                continue;
                            }
                            $nye_forslag[] = $id;
                        }
                        $nye_forslag = implode(',', $nye_forslag);
                        $st = DB::getDB()->prepare('UPDATE vaktbytte SET forslag=:forslag WHERE id=:id');
                        $st->bindParam(':forslag', $nye_forslag);
                        $st->bindParam(':id', $vaktbyttet->getId());
                        $st->execute();

                        $vakta->slettVaktbytteIdFraInstans($vaktbyttet->getId());
                        //$st2 = DB::getDB()->prepare('UPDATE vakt SET vaktbytte_id=:vakt_bytte WHERE id=:id');
                        //$st2->bindParam(':id', $vakta->getId());
                        //$st2->execute();

                        $_SESSION['success'] = 1;
                        $_SESSION['msg'] = "Du trakk vakta di fra vaktbyttet!";

                    }
                }
            }
            $egne_vakter_vaktbytter = array();
            foreach(VaktListe::medBrukerId(LogginnCtrl::getAktivBruker()->getId()) as $vakt){
                if(!$vakt->erFerdig() && $vakt->getBytte()){
                    //$egne_vakter_vaktbytter[] = Vaktbytte::medId($vakt->getVaktbytteDenneErMedIId());
                    //foreach($vakt->getVaktbytteDenneErMedIId() as $id){
                      //  $egne_vakter_vaktbytter[] = Vaktbytte::medId($id);
                    //}
                    $egne_vakter_vaktbytter[] = Vaktbytte::medVaktId($vakt->getId());
                }
            }
            $dok->set('egne_vakter_vaktbytter', $egne_vakter_vaktbytter);
            $vaktbytteListe = VaktbytteListe::etterVakttype();
            $dok->set('vaktbytteListe', $vaktbytteListe);
            if(LogginnCtrl::getAktivBruker()->getPerson()->harVakt()) {
                $dok->vis('vakt_bytte_liste.php');
            } else {
                $dok->vis('vakt_bytte_uten_vakt.php');
            }
        }
        else {
            $dok = new Visning($this->cd);
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                //data: 'bekreft=1&id=' + id,
                if (isset($post['bekreft']) && $post['bekreft'] == 1 && isset($post['id']) && is_numeric($post['id'])) {
                    $aktuell_vakt = Vakt::medId($post['id']);
                    if ($aktuell_vakt != null && $aktuell_vakt->getBrukerId() == LogginnCtrl::getAktivBruker()->getId()) {
                        $id = $aktuell_vakt->getId();
                        $st = DB::getDB()->prepare('UPDATE vakt SET bekreftet=1 WHERE id=:id');
                        $st->bindParam(':id', $id);
                        $st->execute();
                    }
                }
            }
            $egne_vakter = VaktListe::medBrukerId(LogginnCtrl::getAktivBruker()->getId());
            $dok->set('egne_vakter', $egne_vakter);
            $dok->set('denneUka', @date('W'));
            $dok->set('detteAret', @date('Y'));
            $dok->vis('vakt_vaktliste.php');
        }
    }
}

?>