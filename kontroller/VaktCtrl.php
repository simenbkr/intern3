<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $vaktbytteListe = VaktbytteListe::etterVakttype();
        if ($aktueltArg == 'bytte') {
            $dok = new Visning($this->cd);
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
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


                        //Send epost til de som ønsker å bli opplyst om dette.
                        /*if ($passordet == 0) {
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
                        }*/
                    }

                    //data: 'vaktbytte=2&id=' + id +'&vaktId=' + vaktId,
                    //Slett eget vaktbytte (dvs ta egen vakt "tilbake").
                    elseif (isset($post['vaktbytte']) && $post['vaktbytte'] == 2 && isset($post['id']) && isset($post['vaktId'])) {
                        $id = $post['id'];
                        $vaktId = $post['vaktId'];
                        $vaktInstans = Vakt::medId($vaktId);
                        if ($vaktInstans != null && $vaktInstans->getBruker() == LogginnCtrl::getAktivBruker()) {
                            Vaktbytte::slettEgetVaktBytte($id, $vaktId);
                            $st = DB::getDB()->prepare('UPDATE vakt SET bytte=0 WHERE id=:id');
                            $st->bindParam(':id', $vaktId);
                            $st->execute();
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
                        }
                        if ($vaktByttet->harPassord() && !$vaktByttet->stemmerPassord($passordet)) {
                            $dok->set('feilPassord', 1);
                        }
                    }
                    //data: 'vaktbytte=5&fraId=' + fraId + '&tilId=' + tilId
                    //Legge til forslag for vaktbytte uten passord
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
                                $forslag = $tilVakt->getId();
                            } else {
                                $forslag[] = $tilVakt->getId();
                                $forslag = implode(',', array_filter($forslag));
                            }
                            $st = DB::getDB()->prepare('UPDATE vaktbytte SET forslag=:forslag WHERE id=:id');
                            $st->bindParam(':id', $vaktbyttet->getId());
                            $st->bindParam(':forslag', $forslag);
                            $st->execute();
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

                            $st = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id,bytte=0,bekreftet=1,autogenerert=0 WHERE id=:id');
                            $st->bindParam(':bruker_id', $tilVakt->getBrukerId());
                            $st->bindParam(':id', $fraVakt->getId());

                            $st2 = DB::getDB()->prepare('UPDATE vakt SET bruker_id=:bruker_id,bytte=0,bekreftet=1,autogenerert=0 WHERE id=:id');
                            $st2->bindParam(':bruker_id', $fraVakt->getBrukerId());
                            $st2->bindParam(':id', $tilVakt->getId());

                            $st3 = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
                            $st3->bindParam(':id', $Vaktbyttet->getId());

                            $st->execute();
                            $st2->execute();
                            $st3->execute();
                        }
                    }
                }
                $dok->set('vaktbytteListe', $vaktbytteListe);
                $dok->vis('vakt_bytte_liste.php');
            }
        } else {
            $dok = new Visning($this->cd);
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                //data: 'bekreft=1&id=' + id,
                if (isset($post['bekreft']) && $post['bekreft'] == 1 && isset($post['id']) && is_numeric($post['id'])) {
                    $aktuell_vakt = Vakt::medId($post['id']);
                    if ($aktuell_vakt != null && $aktuell_vakt->getBrukerId() == LogginnCtrl::getAktivBruker()->getId()) {
                        $st = DB::getDB()->prepare('UPDATE vakt SET bekreftet=1 WHERE id=:id');
                        $st->bindParam(':id', $aktuell_vakt->getId());
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