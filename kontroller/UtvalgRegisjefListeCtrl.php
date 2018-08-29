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

                        if(strlen($post['navn']) > 0) {
                            $regiliste->endreNavn($post['navn']);
                        }

                        if(isset($post['add']) && ($beboer = Beboer::medId($post['add'])) !== null){
                            Regiliste::addBeboerToListe($sisteArg, $post['add']);
                        } elseif(isset($post['del']) && ($beboer = Beboer::medId($post['del'])) !== null) {
                            Regiliste::removeBeboerFromListe($sisteArg, $post['del']);
                        }

                    }
                    break;
                case 'slett':
                    if(($regiliste = Regiliste::medId($sisteArg)) !== null) {
                        $regiliste->slett();
                        Funk::setSuccess("Slettet regilista!");
                    }
                    break;
                case 'regi':

                    $navn = "[GENERERT " . date('Y-m-d') . "] Alle med regi.";
                    $idliste = array();

                    foreach(BeboerListe::aktiveMedRegi() as $beboer){
                        /* @var \intern3\Beboer $beboer */
                        if($beboer->harUtvalgVerv()){
                            continue;
                        }

                        $idliste[] = $beboer->getId();
                    }

                    Regiliste::opprett($navn, $idliste);
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