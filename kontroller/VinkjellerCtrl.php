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
                $vinListe = Vin::getAktive();
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


        if(!($this->cd->getAktuellArgPos() < count($alleArgs)) || !is_numeric($lastArg) || ($aktuell_vin = Vin::medId($lastArg)) == null){
            $dok->vis('vinkjeller_hoved.php');
        }

        /* Forventet URL: vinkjeller/kryssing/<id> */
        $beboerListe = BeboerListe::aktiveMedAlko();
        $dok->set('beboerListe', $beboerListe);
        $dok->set('vinen', $aktuell_vin);
        $dok->vis('vinkjeller_kryssing_vinen.php');
        return;

    }

    private function handleKryss($dok){

        if(isset($_POST) && count($_POST) < 1){
            $dok->vis('vinkjeller_hoved.php');
            return;
        }
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    }
}