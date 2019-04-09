<?php


namespace intern3;


class UtvalgRomsjefSoknadCtrl extends AbstraktCtrl
{

    public function bestemHandling() {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();


        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            switch ($aktueltArg) {

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