<?php

namespace intern3;


class RestCtrl extends AbstraktCtrl
{
    public function bestemHandling(){
        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        $dok = new Visning($this->cd);
        switch($aktueltArg){
            case 'beboere':
                if($sisteArg != $aktueltArg && is_numeric($sisteArg) && ($beboeren = Beboer::medId($sisteArg)) != null){
                    $objekt = array(
                        $beboeren->getId() => array(
                            "id" => $beboeren->getId(),
                            "navn" => $beboeren->getFulltNavn(),
                            "alko" => $beboeren->harAlkoholdepositum()
                        )
                    );
                    $dok->set('objekt', json_encode($objekt, true));
                    $dok->vis('api.php');
                    break;
                }
                $arr = array();
                foreach(BeboerListe::aktive() as $beboer){
                    if ($beboer == null){
                        continue;
                    }
                    /* @var $beboer \intern3\Beboer */
                    $arr[$beboer->getId()] = array(
                        "id" => $beboer->getId(),
                        "navn" => $beboer->getFulltNavn(),
                        "alko" => $beboer->harAlkoholdepositum()
                    );
                }
                $dok->set('objekt', json_encode($arr, true));
                $dok->vis('api.php');
                break;
            case 'vin':
                if($sisteArg != $aktueltArg && is_numeric($sisteArg) && ($vinen = Vin::medId($sisteArg)) != null){
                    $objekt = array(
                        $vinen->getId() => array(
                            "id" => $vinen->getId(),
                            "typeId" => $vinen->getTypeId(),
                            "type" => $vinen->getType()->getNavn(),
                            "navn" => $vinen->getNavn(),
                            "pris" => $vinen->getPris(),
                            "antall" => $vinen->getAntall()
                        )
                    );
                    $dok->set('objekt', json_encode($objekt, true));
                    $dok->vis('api.php');
                }
                $arr = array();
                foreach(Vin::getAlle() as $vin){
                    if($vin == null){
                        continue;
                    }
                    /* @var $vin \intern3\Vin */
                    $arr[$vin->getId()] = array(
                        "id" => $vin->getId(),
                        "typeId" => $vin->getTypeId(),
                        "type" => $vin->getType()->getNavn(),
                        "navn" => $vin->getNavn(),
                        "pris" => $vin->getPris(),
                        "antall" => $vin->getAntall()
                    );
                }
                $dok->set('objekt', json_encode($arr, true));
                $dok->vis('api.php');
                break;
            case 'type':
                $arr = array();
                foreach(Vintype::getAlle() as $type){
                    /* @var $type \intern3\Vintype */
                    if($type == null){
                        continue;
                    }
                    $arr[$type->getId()] = array(
                        "id" => $type->getId(),
                        "navn" => $type->getNavn()
                    );
                }
                $dok->set('objekt', json_encode($arr, true));
                $dok->vis('api.php');
        }
        return;
    }
}