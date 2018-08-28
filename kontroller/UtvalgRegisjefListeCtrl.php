<?php

namespace intern3;


class UtvalgRegisjefListeCtrl extends AbstraktCtrl
{

    public function bestemHandling(){


        $beboerliste = BeboerListe::aktive();

        $dok = new Visning($this->cd);
        $dok->set('beboerliste', $beboerliste);
        $dok->vis('utvalg/regisjef/utvalg_regisjef_regiliste.php');



    }


}