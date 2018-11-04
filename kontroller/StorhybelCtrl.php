<?php

namespace intern3;


class StorhybelCtrl extends AbstraktCtrl
{

    public function bestemHandling() {

        $lista = Storhybelliste::aktiv();
        $nummer = $lista->nummerBeboer($this->cd->getAktivBruker()->getPerson()->getId());
        $min_tur = ($lista->getVelgerNr() === $nummer);


        $dok = new Visning($this->cd);
        $dok->set('lista', $lista);
        $dok->set('persnummer', $nummer);
        $dok->set('min_tur', $min_tur);
        $dok->vis('storhybel/storhybel.php');

    }


}