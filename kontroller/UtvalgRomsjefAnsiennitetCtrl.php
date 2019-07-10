<?php

namespace intern3;


class UtvalgRomsjefAnsiennitetCtrl extends AbstraktCtrl {
    
    public function bestemHandling(){
        
        $aktueltArg = $this->cd->getAktueltArg();
        $dok = new Visning($this->cd);
        $beboerliste = BeboerListe::aktive();
        
        switch($aktueltArg) {
            
            case 'bulkinkrement':
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $valgte = json_decode($post['valgte']);
                    
                    //Inkrementer alle UVALGTE
                    if($post['modus'] == 0){
                        
                        foreach($beboerliste as $beboer){
                            /* @var \intern3\Beboer $beboer */
                            if(!in_array($beboer->getId(), $valgte)){
                                $beboer->inkrementerAnsiennitet();
                            }
                        }
                        
                    //Inkrementer alle VALGTE
                    } elseif ($post['modus'] == 1){
                        
                        foreach($valgte as $id){
                            if(($beboer = Beboer::medId($id)) != null && $beboer->erAktiv()){
                                $beboer->inkrementerAnsiennitet();
                            }
                        }
                        
                    }
                }
                break;
            case 'inkrement':
                if($_SERVER['REQUEST_METHOD'] === 'POST'){
                    $sisteArg = $this->cd->getSisteArg();
                    if(is_numeric($sisteArg) && ($beboer = Beboer::medId($sisteArg)) !== null && $beboer->erAktiv()){
                        $beboer->inkrementerAnsiennitet();
                        die("woooo!");
                        break;
                    } else {
                        exit("Ikke gyldig beboer å oppdatere ansienniteten til!");
                    }
                } else {
                    exit("Må være POST!");
                }
            case 'oppdater':
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $sisteArg = $this->cd->getSisteArg();
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if(is_numeric($sisteArg) && ($beboer = Beboer::medId($sisteArg)) !== null && $beboer->erAktiv()
                       && isset($post['nyverdi']) && is_numeric($post['nyverdi'])){
                        $beboer->setAnsiennitet($post['nyverdi']);
                        die("asdasdasd");
                        break;
                    } else {
                        exit("Ikke gyldig beboer å oppdatere ansienniteten til!");
                    }
                } else {
                    exit("Må være POST!");
                }
            case 'eksporter':
                $fil = $this->lagCSVfil();
                header("Content-type: text/csv");
                $ts = date('Y-m-d_H_i_s');
                header("Content-Disposition: attachment; filename=ansiennitet-$ts.csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                $output = fopen('php://output', 'wb');
                foreach($fil as $line) {
                    fputcsv($output, $line);
                }

                fclose($output);

                break;
            case '':
            default:
                $dok->set('beboerliste', $beboerliste);
                $dok->vis('utvalg/romsjef/ansiennitet.php');
                break;
        }
        
    }
    

    private function lagCSVfil() : array {

        $out[0] = array('Navn','Rom','Ansiennitet','Født','Rolle');

        foreach(BeboerListe::aktive() as $beboer) {
            /* @var Beboer $beboer */

            $data = array($beboer->getFulltNavn(),$beboer->getRom()->getNavn(),$beboer->getAnsiennitet(),
                $beboer->getFodselsdato(),$beboer->getRolle()->getNavn());
            $out[] = $data;
        }

        return $out;
    }
    
    
}