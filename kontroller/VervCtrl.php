<?php

namespace intern3;

class VervCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        $dok = new Visning($this->cd);
        if ($sisteArg != 'verv' && is_numeric($sisteArg)) {
            $vervet = Verv::medId($sisteArg);
            $kan_redigere_beskrivelse = in_array($vervet, LogginnCtrl::getAktivBruker()->getPerson()->getVervListe()) || LogginnCtrl::getAktivBruker()->getPerson()->harUtvalgVerv();
            $har_vervet = in_array($vervet, LogginnCtrl::getAktivBruker()->getPerson()->getVervListe());
            if ($vervet != null) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if ($kan_redigere_beskrivelse && isset($post['beskrivelse'])) {
                        $st = DB::getDB()->prepare('UPDATE verv SET beskrivelse=:beskrivelse WHERE id=:id');
                        $st->bindParam(':beskrivelse', $post['beskrivelse']);
                        $st->bindParam(':id', $sisteArg);
                        $st->execute();
                        header('Location: ' . $_SERVER['REQUEST_URI']);
                    }
                    if ($har_vervet && isset($post['melding'])) {
                        $st = DB::getDB()->prepare('INSERT INTO verv_melding (tekst,verv_id,beboer_id) VALUES(:tekst,:verv_id,:beboer_id)');
                        $st->bindParam(':tekst', $post['melding']);
                        $st->bindParam(':verv_id', $sisteArg);
                        $st->bindParam(':beboer_id', LogginnCtrl::getAktivBruker()->getPerson()->getId());
                        $st->execute();
                        header('Location: ' . $_SERVER['REQUEST_URI']);
                    }
                }

                $verv_meldinger = VervMelding::medVervId($sisteArg);
                $dok->set('sistearg', $sisteArg);
                $dok->set('kan_redigere_beskrivelse', $kan_redigere_beskrivelse);
                $dok->set('har_vervet', $har_vervet);
                $dok->set('verv_meldinger', $verv_meldinger);
                $dok->set('vervet', $vervet);
                $dok->vis('verv/verv_beskrivelse.php');
                exit();
            }
        }

        $vervListe = VervListe::alle();
        $dok->set('vervListe', $vervListe);
        $dok->vis('verv/verv.php');
    }
}

?>
