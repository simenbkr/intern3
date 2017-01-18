<?php

namespace intern3;

class UtvalgRegisjefOppgaveCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $dok = new Visning($this->cd);
        if(isset($_POST)){
            $post = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);
            if (isset($post['godkjenn'])) {
                Oppgave::endreGodkjent($post['godkjenn'], 1);
            } elseif (isset($post['fjern'])) {
                Oppgave::endreGodkjent($post['fjern'], 0);
            } elseif (isset($post['registrer'])) {
                if(isset($post['navn']) && isset($post['prioritet']) && isset($post['timer']) && isset($post['personer']) && isset($post['beskrivelse'])
                && $post['navn'] != null && $post['prioritet'] != null && $post['timer'] != null && $post['personer'] != null && $post['beskrivelse'] != null){
                    $navn = $post['navn'];
                    $pri = $post['prioritet'];
                    $anslagtid = $post['timer'];
                    $anslagpers = $post['personer'];
                    $beskrivelse = $post['beskrivelse'];
                    Oppgave::AddOppgave($navn, $pri, $anslagtid, $anslagpers, $beskrivelse);
                } else {
                    $dok->set('feilSubmit', 1);
                }
            } //data: 'slett=' + id,
            elseif (isset($post['slett']) && is_numeric($post['slett'])) {
                $id = $post['slett'];
                $st = DB::getDB()->prepare('DELETE FROM oppgave WHERE id=:id');
                $st->bindParam(':id', $id);
                $st->execute();
            }
        }
        $oppgaveListe = OppgaveListe::alle();
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('utvalg_regisjef_oppgave.php');
        $dok->set('feilSubmit', null);
    }
}

?>
