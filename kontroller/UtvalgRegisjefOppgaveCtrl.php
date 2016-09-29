<?php

namespace intern3;

class UtvalgRegisjefOppgaveCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        if (isset($_POST['godkjenn'])) {

            Oppgave::endreGodkjent($_POST['godkjenn'],1);
        }
        elseif(isset($_POST['fjern'])){
            Oppgave::endreGodkjent($_POST['fjern'],0);
        }


        $oppgaveListe = OppgaveListe::alle();
        $dok = new Visning($this->cd);
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('utvalg_regisjef_oppgave.php');
    }
}

?>
