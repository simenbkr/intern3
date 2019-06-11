<?php

namespace intern3;


use Group\GroupManage;

class ProfilEpostCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $bruker = $this->cd->getAktivBruker();
        $beboer = $bruker->getPerson();
        $group = new GroupManage();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && in_array($aktueltArg, PUBLIC_MAIL)) {

            if (!$group->inGroup($beboer->getEpost(), $aktueltArg)) {
                $group->addToGroup($beboer->getEpost(), "MEMBER", $aktueltArg);
                print $beboer->getEpost() . " ble lagt til i " . $aktueltArg . "!";
            } else {
                print $beboer->getEpost() . " er allerede i " . $aktueltArg . "!";
            }

        } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && in_array($aktueltArg, PUBLIC_MAIL)){

            if($group->inGroup($beboer->getEpost(), $aktueltArg)){
                $group->removeFromGroup($beboer->getEpost(), $aktueltArg);
                print $beboer->getEpost() . " ble fjernet fra " . $aktueltArg . "!";
            } else {
                print $beboer->getEpost() . " er allerede fjernet fra " . $aktueltArg . "!";
            }

        }
        else {

            $ret = "<td>" . $beboer->getEpost() . "</td>";
            $id = $beboer->getId();

            $mail_lists = array();
            if($beboer->getKjonn() == 0) {
                $mail_lists = [SING_GUTTER];
            } elseif ($beboer->getKjonn() == 1) {
                $mail_lists = [SING_JENTER];
            }

            $mail_lists = array_merge($mail_lists, PUBLIC_MAIL);
            foreach ($mail_lists as $lista) {
                $classname = explode("@", $lista)[0];
                if ($group->inGroup($beboer->getEpost(), $lista)) {
                    $button_string = "✔ <button class='btn btn-danger' onclick='del($id, \"$lista\")'>Fjern</button>";
                    $ret .= "<td class='$classname'>$button_string</td>";
                } else {
                    $button_string = "✗ <button class='btn btn-warning' onclick='leggTil($id, \"$lista\")'>Legg til</button>";
                    $ret .= "<td class='$classname'>$button_string</td>";
                }
            }

            print $ret . "<td></td>";
        }


    }
}