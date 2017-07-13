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
            case '':
                header('Location:' . rtrim($_SERVER['REQUEST_URI'], '/'));
                exit();
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

    private function isInt($num){

        return $num - floor($num) == 0;

    }


    private function handleKryss($dok){

        if(isset($_POST) && count($_POST) < 1){
            //AKA fuck off
            $dok->vis('vinkjeller_hoved.php');
            return;
        }
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $antall = $post['antall'];

        $beboerIDs = explode(',' , $post['beboerId']);
        $vinId = $post['vinid'];

        //Dette gjøres fordi JavaScript er å anse som svart magi. Det fungerer kun basert på empiri.
        $fordeling = array();
        foreach(explode(',' , $post['fordeling']) as $key => $val) {
            //Pls kill me now.
            if($key && $val){
                $fordeling[$key] = $val;
            }
        }

        $beboerene = array();
        foreach($beboerIDs as $id){
            if(($beboer = Beboer::medId($id)) == null){
                exit();
            }
            //Gotta do them docs yo.
            /* @var Beboer $beboer */
            $beboerene[] = $beboer;
        }

        /*if($antall < 1 || $antall > $vinen->getAntall() || !is_int($antall)) {
            setcookie('woopi',"doopi");
            exit();
        }*/

        if ( ($vinen = Vin::medId($vinId)) == null){
            setcookie('shit',"face");
            exit();
        }
        if($antall < 1){
            setcookie('woopi', "schmoopi");
            exit();
        }
        if($antall > $vinen->getAntall()){
            setcookie("qaahaha","nei");
            exit();
        }
        if(!$this->isInt($antall)){
            setcookie("stupid", "shit");
            exit();
        }

        if(round(array_sum($fordeling), 2) < 99.99 || round(array_sum($fordeling), 2) > 100.99){
            setcookie("udon","fook");
            exit();
        }

        //Ait, we gucci.
        $msg = "Du krysset " . $antall . "stk " . $vinen->getNavn() . " til " . $vinen->getPris() * $vinen->getAvanse()
            . "kr per stk på ";
        foreach($beboerene as $beboer){

            if(count($beboerene) == 1){
                $antallet = $antall;
            } else {
                $antallet = round($fordeling[$beboer->getId()]/100 * $antall, 3);
            }
            $pris = $antallet * $vinen->getPris() * $vinen->getAvanse();

            $st = DB::getDB()->prepare('INSERT INTO vinkryss (antall, tiden, fakturert, vinId, beboerId, prisen)
                                       VALUES(               :antall, NOW(), 0, :vinId, :beboerId, :prisen)');

            $st->bindParam(':antall', $antallet);
            $st->bindParam(':prisen', $pris);
            $st->bindParam(':vinId', $vinen->getId());
            $st->bindParam(':beboerId', $beboer->getId());

            $st->execute();

            $msg .= $beboer->getFulltNavn() . ', ';
        }

        $st = DB::getDB()->prepare('UPDATE vin SET antall=:antall WHERE id=:id');
        $nytt_antall = $vinen->getAntall() - $antall;
        $st->bindParam(':antall', $nytt_antall);
        $st->bindParam(':id', $vinen->getId());
        $st->execute();

        $_SESSION['success'] = 1;
        $_SESSION['msg'] = rtrim($msg, ', ') . '. Drikk (u)ansvarlig!';


    }
}