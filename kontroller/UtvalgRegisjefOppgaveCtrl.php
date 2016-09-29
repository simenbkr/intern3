<?php

namespace intern3;

class UtvalgRegisjefOppgaveCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        if (isset($_POST['godkjenn'])) {
            $post = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            Oppgave::endreGodkjent($post['godkjenn'],1);
        }
        elseif(isset($_POST['fjern'])){
            $post = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            Oppgave::endreGodkjent($post['fjern'],0);
        }
        elseif(isset($_POST['registrer'])){
            $post = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            $navn = $post['navn'];
            $pri = $post['prioritet'];
            $anslagtid = $post['timer'];
            $anslagpers = $post['personer'];
            $beskrivelse = $post['beskrivelse'];

            Oppgave::AddOppgave($navn,$pri,$anslagtid,$anslagpers,$beskrivelse);
        }

        $oppgaveListe = OppgaveListe::alle();
        $dok = new Visning($this->cd);
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('utvalg_regisjef_oppgave.php');
    }
}

?>
