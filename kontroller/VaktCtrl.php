<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $vaktbytteListe = VaktbytteListe::etterVakttype();
        $sisteArg = $this->cd->getSisteArg();

        if(!isset($_SESSION['semester'])){
          $_SESSION['semester'] = 'frana';
        }
        
        if ($sisteArg == 'setvar') {
            $_SESSION['semester'] = "var";
        } elseif ($sisteArg == 'sethost') {
            $_SESSION['semester'] = "host";
        } elseif ($sisteArg == 'setna') {
            $_SESSION['semester'] = "frana";
        } elseif ($aktueltArg == 'bytte') {
            $valgtCtrl = new VaktbytteCtrl($this->cd->skiftArg());
            $valgtCtrl->bestemHandling();
            return;
        }
        $dok = new Visning($this->cd);
        //$egne_vakter = VaktListe::medBrukerIdEtter(LogginnCtrl::getAktivBruker()->getid(), date('Y-m-d'));
        $egne_vakter = VaktListe::medBrukerId(LogginnCtrl::getAktivBruker()->getId());
        $dok->set('egne_vakter', $egne_vakter);
        $dok->vis('vakt_vaktliste.php');
        return;
    }
}

?>