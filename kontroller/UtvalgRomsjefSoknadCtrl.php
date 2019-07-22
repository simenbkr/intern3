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
                case 'year':
                    if(!is_numeric($sisteArg) || strlen($sisteArg) !== 4) {
                        return;
                    }

                    $soknader = Soknad::fraYear($sisteArg);
                    $dok = new Visning($this->cd);
                    $dok->set('soknader', $soknader);
                    $dok->vis('utvalg/romsjef/soknad/soknad.php');

                    break;

                case 'eksporter':
                    if(!is_numeric($sisteArg) || strlen($sisteArg) !== 4) {
                        return;
                    }

                    $soknader = Soknad::fraYear($sisteArg);
                    $csv = Soknad::SoknaderTilCSV($soknader);
                    header("Content-type: text/csv");
                    $ts = date('Y-m-d_H_i_s');
                    header("Content-Disposition: attachment; filename=soknad-$sisteArg-$ts.csv");
                    header("Pragma: no-cache");
                    header("Expires: 0");

                    $output = fopen('php://output', 'wb');
                    foreach($csv as $line) {
                        fputcsv($output, $line);
                    }

                    fclose($output);

                    break;
                case '':
                default:

                    //$soknader = Soknad::alle();
                    $dok = new Visning($this->cd);
                    //$dok->set('soknader', $soknader);
                    $dok->vis('utvalg/romsjef/soknad/meta_soknad.php');
                    exit();
            }


        } else {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


            exit();


        }
    }

}