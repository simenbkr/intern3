<?php

namespace intern3;


use Group\GroupManage;

class UtvalgRomsjefEpostCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $dok = new Visning($this->cd);
        $beboerliste = BeboerListe::aktive();
        $group = new GroupManage();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            switch ($aktueltArg) {
                case in_array($aktueltArg, MAIL_LISTS) && ($beboer = Beboer::medId($this->cd->getSisteArg())) != null:

                    if (!$group->inGroup($beboer->getEpost(), $aktueltArg)) {
                        $group->addToGroup($beboer->getEpost(), "MEMBER", $aktueltArg);
                        print $beboer->getEpost() . " ble lagt til i " . $aktueltArg . "!";
                        break;
                    } else {
                        print 0;
                        break;
                    }
            }

        } else {
            if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

                if (in_array($aktueltArg, MAIL_LISTS) && ($beboer = Beboer::medId($this->cd->getSisteArg())) != null) {

                    if ($group->inGroup($beboer->getEpost(), $aktueltArg)) {
                        $group->removeFromGroup($beboer->getEpost(), $aktueltArg);
                        print $beboer->getEpost() . " ble fjernet fra " . $aktueltArg . "!";
                    } else {
                        print $beboer->getEpost() . " er allerede fjernet fra " . $aktueltArg . "!";
                    }
                }
                elseif(in_array($aktueltArg, MAIL_LISTS) && Funk::isValidEmail($this->cd->getSisteArg())) {
                    error_log("YOU WOT MATE");
                    $group->removeFromGroup($this->cd->getSisteArg(), $aktueltArg);
                    print "Fjernet {$this->cd->getSisteArg()} fra {$aktueltArg}!";
                }
                else {
                    print "uhh";
                }

            } else {

                switch ($aktueltArg) {
                    case 'status':
                        if(($epostlista = "{$this->cd->getSisteArg()}@singsaker.no") != $aktueltArg && in_array($epostlista, MAIL_LISTS)) {


                            $beboerEposter = array_map(function(Beboer $b) { return strtolower($b->getEpost()); }, $beboerliste);
                            $deltakere = $group->listGroup($epostlista);
                            $behandla = array();

                            foreach($deltakere as $deltaker) {
                                if(in_array(strtolower($deltaker['email']), $beboerEposter)) {
                                    $behandla[] = array(true, $deltaker['email'], Bruker::medEpost($deltaker['email']));
                                } else {
                                    $behandla[] = array(false, $deltaker['email'], NULL);
                                }

                            }

                            $dok = new Visning($this->cd);
                            $dok->set('behandla', $behandla);
                            $dok->set('epostlista', $epostlista);
                            $dok->vis('utvalg/romsjef/epostliste_detalj.php');
                        }
                        break;
                    case is_numeric($aktueltArg):
                        if (($beboer = Beboer::medId($aktueltArg)) != null) {
                            $status = array();
                            $ret = '<td>' . $beboer->getFulltNavn() . "</td><td>" . $beboer->getEpost() . "</td>";
                            $id = $beboer->getId();

                            foreach (MAIL_LISTS as $lista) {
                                $classname = explode("@", $lista)[0];

                                if ($group->inGroup($beboer->getEpost(), $lista)) {
                                    $button_string = "✔ <button class='btn btn-danger' onclick='del($id, \"$lista\")'>Fjern</button>";
                                    $ret .= "<td class='$classname'>$button_string</td>";
                                } else {
                                    $button_string = "✗ <button class='btn btn-warning' onclick='leggTil($id, \"$lista\")'>Legg til</button>";
                                    $ret .= "<td class='$classname'>$button_string</td>";
                                }
                            }
                            //print '<td>' . $beboer->getFulltNavn() . "</td><td>" . $beboer->getEpost() . "</td><td>" .
                            //   implode('</td><td>', $status) . "</td>";
                            print $ret . "<td></td>";
                            break;
                        }
                    case '':
                    default:
                        $dok->set('beboerliste', $beboerliste);
                        $dok->vis('utvalg/romsjef/utvalg_romsjef_epostlister.php');
                }

            }
        }


    }

}