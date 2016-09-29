<?php

namespace intern3;

class UtvalgSekretarCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'apmandsverv') {
            if(isset($_POST)){

                if (isset($_POST['fjern']) && isset($_POST['verv'])) {
                    $post = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
                    $id = $post['fjern'];
                    $vervid = $post['verv'];
                    Verv::deleteBeboerFromVerv($id,$vervid);
                }
                elseif (isset($_POST['vervet'])){
                    setcookie('du','posta');
                    $post = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
                    $postet = explode('&',$post['vervet']);
                    $beboer_id = $postet[0];
                    $verv_id = $postet[1];
                    Verv::updateVerv($beboer_id,$verv_id);
                }

            }

            $beboerListe = BeboerListe::aktive();
            $vervListe = VervListe::alle();
            $utvalg = Verv::erUtvalg();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vervListe', $vervListe);
            $dok->set('utvalg', $utvalg);
            $dok->vis('utvalg_sekretar_apmandsverv.php');
        } else if ($aktueltArg == 'utvalgsverv') {

            if (isset($_POST['endre'])) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $vervId = $post['vervid'];
                $personId = $post['beboerid'];
                Verv::updateVerv($personId, $vervId);
            }

            $beboerListe = BeboerListe::aktive();
            $vervListe = VervListe::alleUtvalg();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vervListe', $vervListe);
            $dok->vis('utvalg_sekretar_utvalgsverv.php');
        } else if ($aktueltArg == 'lister') {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg_sekretar_lister.php');
        } else if (is_numeric($aktueltArg)) {
            $beboer = Beboer::medId($aktueltArg);
            // Trenger feilhåndtering her.
            $dok = new Visning($this->cd);
            $dok->set('beboer', $beboer);
            $dok->vis('beboer_detaljer.php');
        } else {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg_sekretar.php');
        }
    }
}

?>
