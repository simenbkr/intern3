<?php

namespace intern3;


class VinkjellerCtrl extends AbstraktCtrl {

    public function bestemHandling(){
        $aktuelarg = $this->cd->getAktueltArg();

        $dok = new Visning($this->cd);
        $dok->set('skjulMeny', 1);
        switch($aktuelarg) {

            case 'kryssing':
                if(($sisteArg = $this->cd->getSisteArg()) != $aktuelarg){
                    $this->handleKryssing($dok);
                    break;
                }
                $dok->vis('vinkjeller_kryssing.php');
                break;

            case 'kryss_vin':
                $this->handleKryss($dok);
                break;
            case 'regler':
                $dok->vis('vinkjeller_regler.php');
                break;
            case '':
            default:
                $dok->vis('vinkjeller_hoved.php');
                break;
        }

    }

    private function handleKryssing($dok){
        /*
         * Hva om..
         * URL-bygges slik: vinkjeller/kryssing/[navn|type]/<vin_id>
         * Også hukes de som ønsker å krysse seg av på denne siden,
         * Dette postes til f.eks vinkjeller/kryss og føres inn i databasen.
         */
        $alleArgs = $this->cd->getAllArgs();
        $lastArg = end($alleArgs);

        if(count($alleArgs) < 1){
            /* @var $dok \intern3\Visning */
            $dok->vis('vinkjeller_kryssing.php');
            return;
        }

        switch($lastArg){
            case 'navn':
                $vinListe = Vin::getAktiveOrderedByNavn();
                $dok->set('vinListe', $vinListe);
                $dok->vis('vinkjeller_kryssing_navn.php');
                return;
            case 'type':
                $typeListe = Vintype::getAlle();
                $dok->set('typeListe', $typeListe);
                $dok->vis('vinkjeller_kryssing_type.php');
                return;
            case 'kryss':
                $this->handleKryss($dok);
                return;
        }

        if(in_array('type', $alleArgs) && 'type') {
            /* Type er i argumentene, men er IKKE siste argument. */
            if( ($typen = Vintype::medId($lastArg)) != null){
                $vinListe = Vin::getAktiveAvType($typen);
                $dok->set('vinListe', $vinListe);
                $dok->vis('vinkjeller_kryssing_navn.php');
                return;
            } else {
                $dok->vis('vinkjeller_hoved.php');
                return;
            }
        }


        /*if(!($this->cd->getAktuellArgPos() < count($alleArgs)) || !is_numeric($lastArg) || ($aktuell_vin = Vin::medId($lastArg)) == null){
            setcookie('ugh','niggawut');
            $dok->vis('vinkjeller_hoved.php');
        }*/

        if (count($this->cd->getAllArgs()) < 4) {
            $aktuell_vin = Vin::medId($lastArg);
            /* Forventet URL: vinkjeller/kryssing/<id> */
            $beboerListe = BeboerListe::aktiveMedAlko();
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vinen', $aktuell_vin);
            $dok->vis('vinkjeller_kryssing_vinen.php');
            return;
        }
        else {
            /* ?a=vinkjeller/kryssing/<vinID>/<beboerID-varargs> */
            $args = $this->cd->getAllArgs();

            $beboerene = [];
            for($i = 3; $i < count($args); $i++) {
                $beboerene[] = Beboer::medId($args[$i]);
            }


            $aktuell_vin = Vin::medId($args[2]);
            $dok->set('beboerene', $beboerene);
            $dok->set('vinen', $aktuell_vin);
            $dok->vis('vinkjeller_kryss.php');
            exit();
        }

    }

    private function handleKryss($dok){

        if(isset($_POST) && count($_POST) < 1){
            $dok->vis('vinkjeller_hoved.php');
            return;
        }
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $antall = $post['antall'];

        $beboerIDs = explode(',' , $post['beboerId']);
        $vinId = $post['vinid'];
        $fordeling = array();

        foreach(explode(',' , $post['fordeling']) as $key => $val) {
            //Pls kill me now.
            if($key && $val){
                $fordeling[$key] = $val;
            }
        }

        if ( ($vinen = Vin::medId($vinId)) == null){
            exit();
        }
        $beboerene = array();
        foreach($beboerIDs as $id){
            if(($beboer = Beboer::medId($id)) == null){
                exit();
            }
            /* @var Beboer $beboer */
            $beboerene[] = $beboer;
        }

        if($antall < 1 || $antall > $vinen->getAntall() || !is_int($antall)) {
            exit();
        }

        //Ait, we gucci.

        /*
         * $prisen = $post['antall'] * $vinen->getPris() * $vinen->getAvanse();
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
         */

        foreach($beboerene as $beboer){

            $antall = round($fordeling[$beboer->getId()],3);
            $pris = $antall * $vinen->getPris() * $vinen->getAvanse();

            $st = DB::getDB()->prepare('INSERT INTO vinkryss (antall, tiden, fakturert, vinId, beboerId, prisen)
                                       VALUES(
                                       :antall, NOW(), 0, :vinId, :beboerId, :prisen
                                       )');
            $st->bindParam(':antall', $antall);
            $st->bindParam(':pris', $pris);
            $st->bindParam(':vinId', $vinen->getId());
            $st->bindParam(':beboerId', $beboer->getId());
            $st->execute();

        }

        $st = DB::getDB()->prepare('UPDATE vin SET antall=:antall WHERE id=:id');
        $st->bindParam(':antall', $antall);
        $st->bindParam(':id', $vinen->getId());
        $st->execute();

    }
}