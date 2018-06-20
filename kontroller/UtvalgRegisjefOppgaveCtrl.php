<?php

namespace intern3;

class UtvalgRegisjefOppgaveCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $dok = new Visning($this->cd);
        if (isset($_POST)) {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            foreach ($post as $key => $val) {
                setcookie($key, $val);
            }

            if (isset($post['godkjenn'])) {
                Oppgave::endreGodkjent($post['godkjenn'], 1);
            }
            elseif (isset($post['fjern'])) {
                Oppgave::endreGodkjent($post['fjern'], 0);
            }
            elseif (isset($post['frys'])) {
                Oppgave::medId($post['frys'])->setFryst();
            }
            elseif( isset($post['afrys'])){
                Oppgave::medId($post['afrys'])->unFrys();
            }
            elseif (isset($post['registrer'])) {
                if (isset($post['navn']) && isset($post['prioritet']) && isset($post['timer']) && isset($post['personer']) && isset($post['beskrivelse'])
                    && $post['navn'] != null && $post['prioritet'] != null && $post['timer'] != null && $post['personer'] != null && $post['beskrivelse'] != null
                ) {
                    $navn = $post['navn'];
                    $pri = $post['prioritet'];
                    $anslagtid = $post['timer'];
                    $anslagpers = $post['personer'];
                    $beskrivelse = $post['beskrivelse'];
                    Oppgave::AddOppgave($navn, $pri, $anslagtid, $anslagpers, $beskrivelse);

                    if ($post['epost'] == 1) {

                        $tittel = "[SING-INTERN] Ny oppgave lagt ut: " . $navn;
                        $beskjed = "<html><body>Hei, <br/>Regisjef har lagt til en ny oppgave på internsidene." .
                            "<br/><br/>Anslag timer: " . $anslagtid .
                            "<br/>Anslag personer: " . $anslagpers .
                            "<br/>Beskrivelse:<br/>" . $beskrivelse .
                            "<br/>Meld deg på på <a href='https://intern.singsaker.no/?a=regi/oppgave'>Internsida</a>." .
                            "<br/><br/>Med vennlig hilsen, <br/>Internsida<br/>Singsaker Studenterhjem</body></html>";

                        $mottakere = '';
                        foreach (BeboerListe::aktiveMedRegi() as $beboer) {
                            /* @var Beboer $beboer */
                            if ($beboer->getBruker()->getRegisekunderMedSemester() <
                                $beboer->getRolle()->getRegitimer() * 60 * 60
                            ) {
                                $mottakere .= $beboer->getEpost() . ', ';
                            }
                        }
                        $mottakere = rtrim($mottakere, ', ');
                        $mottakere .= ', data@singsaker.no';

                        Epost::sendEpost($mottakere, $tittel, $beskjed);
                    }

                    header('Location:' . $_SERVER['REQUEST_URI']);
                    exit();
                } else {
                    $dok->set('feilSubmit', 1);
                }

            } //data: 'slett=' + id,
            elseif (isset($post['slett']) && is_numeric($post['slett'])) {
                $id = $post['slett'];
                $oppgaven = Oppgave::medId($id);
                $st = DB::getDB()->prepare('DELETE FROM oppgave WHERE id=:id');
                $st->bindParam(':id', $id);
                $st->execute();
                $_SESSION['success'] = 1; //('slettet', 1);
                $_SESSION['msg'] = "Du slettet oppgaven med navn" . $oppgaven->getNavn();
                header('Location:' . $_SERVER['REQUEST_URI']);
                exit();
            } elseif (isset($post['fjernFraOppgave']) && is_numeric($post['fjernFraOppgave']) &&
                ($beboeren = Beboer::medId($post['fjernFraOppgave'])) != null && isset($post['oppgaveId'])
                && ($oppgaven = Oppgave::medId($post['oppgaveId'])) != null
            ) {

                $oppgaven->fjernPerson($beboeren->getId());
            }
        }
        $oppgaveListe = OppgaveListe::alle();
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('utvalg/regisjef/utvalg_regisjef_oppgave.php');
        $dok->set('feilSubmit', null);
    }
}

?>
