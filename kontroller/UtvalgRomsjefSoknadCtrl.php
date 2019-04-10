<?php


namespace intern3;


class UtvalgRomsjefSoknadCtrl extends AbstraktCtrl
{

    public function bestemHandling() {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();


        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            switch ($aktueltArg) {

                case 'nybeboer':

                    $dok = new Visning($this->cd);

                    $skoleListe = SkoleListe::alle();
                    $studieListe = StudieListe::alle();
                    $rolleListe = RolleListe::alle();
                    $romListe = RomListe::alle();
                    $soknad = Soknad::medId($this->cd->getSisteArg());
                    $dok->set('soknad', $soknad);
                    $dok->set('skoleListe', $skoleListe);
                    $dok->set('studieListe', $studieListe);
                    $dok->set('rolleListe', $rolleListe);
                    $dok->set('romListe', $romListe);
                    $dok->vis('utvalg/romsjef/soknad/ny_beboer_soknad.php');
                    exit();

                case 'id':
                    if(($soknad = Soknad::medId($sisteArg)) !== null) {

                        $dok = new Visning($this->cd);
                        $dok->set('soknad', $soknad);
                        $dok->vis('utvalg/romsjef/soknad/soknad_detaljer.php');
                        exit();
                    }
                case '':
                default:

                    $soknader = Soknad::alle();
                    $dok = new Visning($this->cd);
                    $dok->set('soknader', $soknader);
                    $dok->vis('utvalg/romsjef/soknad/soknad.php');
                    exit();
            }


        } else {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


            exit();


        }
    }

}