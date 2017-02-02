<?php

namespace intern3;

class DiverseCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $oppgaveListe = OppgaveListe::ikkeGodkjente();
        $verv_meldinger = VervMelding::getTreSiste();
        $dok = new Visning($this->cd);
        $dok->set('verv_meldinger', $verv_meldinger);
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('diverse.php');
    }
}

?>
