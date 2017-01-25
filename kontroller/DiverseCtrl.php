<?php

namespace intern3;

class DiverseCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $oppgaveListe = OppgaveListe::ikkeGodkjente();
        $dok = new Visning($this->cd);
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('diverse.php');
    }
}

?>
