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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            switch($aktueltArg){
                case in_array($aktueltArg, MAIL_LISTS) && ($beboer = Beboer::medId($this->cd->getSisteArg())) != null:
                    $group = new GroupManage();
                    if(!$group->inGroup($beboer->getEpost(), $aktueltArg)){
                        $group->addToGroup($beboer->getEpost(), "MEMBER", $aktueltArg);
                        print $beboer->getEpost() . " ble lagt til i " . $aktueltArg . "!";
                        break;
                    } else {
                        print 0;
                        break;
                    }
            }

        } else {

            switch ($aktueltArg) {
                case is_numeric($aktueltArg):
                    if (($beboer = Beboer::medId($aktueltArg)) != null) {
                        $status = array();
                        $group = new GroupManage();
                        $ret = '<td>' . $beboer->getFulltNavn() . "</td><td>" . $beboer->getEpost() . "</td>";
                        foreach(MAIL_LISTS as $lista){
                            if($group->inGroup($beboer->getEpost(), $lista)){
                                $status[] = "✔";
                                $ret .= "<td class='$lista'>✔</td>";
                            } else {
                                $id = $beboer->getId();
                                $classname = explode("@", $lista)[0];
                                $button_string = "✗ <button class='btn btn-warning' onclick='leggTil($id, \"$lista\")'>Legg til</button>";
                                $ret .= "<td class='$classname'>$button_string</td>";
                            }
                        }
                        //print '<td>' . $beboer->getFulltNavn() . "</td><td>" . $beboer->getEpost() . "</td><td>" .
                         //   implode('</td><td>', $status) . "</td>";
                        print $ret;
                        break;
                    }
                case '':
                default:
                    $dok->set('beboerliste', $beboerliste);
                    $dok->vis('utvalg_romsjef_epostlister.php');
            }

        }


    }

}