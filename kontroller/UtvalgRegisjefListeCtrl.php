<?php

namespace intern3;


class UtvalgRegisjefListeCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        $beboerliste = BeboerListe::aktive();
        $dok = new Visning($this->cd);

        //die($aktueltArg . ":" . $sisteArg);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            switch($aktueltArg) {

                case 'opprett':
                    if(isset($post['navn']) && strlen($post['navn']) > 0) {
                        Regiliste::opprett($post['navn'], json_decode($post['valgte']));
                    } else {
                        Funk::setError("Regilista trenger minst et navn! Lista ble ikke oppretta.");
                        exit();
                    }
                    break;
                case 'endre':
                    if(($regiliste = Regiliste::medId($sisteArg)) !== null){
                        $regiliste->endreNavn($post['navn']);

                        $valgte = json_decode($post['valgte']);
                        $regiliste->endreValgte($valgte);
                    }
                    break;
            }


        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if(is_numeric($sisteArg) && ($regiliste = Regiliste::medId($sisteArg)) !== null) {

                $dok->set('beboerliste', $beboerliste);
                $dok->set('regiliste', $regiliste);
                $dok->vis('utvalg/regisjef/utvalg_regisjef_regiliste_detaljer.php');
            } else {

                switch ($aktueltArg) {

                    case 'opprett':
                        $dok->set('beboerliste', $beboerliste);
                        $dok->vis('utvalg/regisjef/utvalg_regisjef_regiliste_ny.php');
                        break;
                    case '':
                    default:
                        $regilister = Regiliste::getAlleLister();

                        $dok->set('regilister', $regilister);
                        $dok->vis('utvalg/regisjef/utvalg_regisjef_regiliste.php');
                        break;
                }
            }
        }


    }


}