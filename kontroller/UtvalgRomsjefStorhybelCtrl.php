<?php

namespace intern3;


class UtvalgRomsjefStorhybelCtrl extends AbstraktCtrl
{
    public function bestemHandling(){

        $ledige_rom = RomListe::alleLedige();
        $beboerliste = BeboerListe::aktive();

        usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));
        $dok = new Visning($this->cd);
        $dok->set('ledige_rom', $ledige_rom);
        $dok->set('beboerliste', $beboerliste);
        $dok->vis('utvalg/romsjef/storhybel_start.php');
    }

}