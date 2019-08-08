<?php

namespace intern3;

class RegiOppgaveCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            //Melder aktiv bruker på aktuell regi-oppgave.
            //data: 'meldPa=1&id=' + id,
            if (isset($post['meldPa']) && $post['meldPa'] == 1 && isset($post['id']) && is_numeric($post['id'])) {
                $oppgaven = Oppgave::medId($post['id']);

                if($oppgaven->erFryst()){
                    Funk::setError("Du kan ikke melde deg på - oppgaven er fryst.");

                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }

                $beboeren = $this->cd->getAktivBruker()->getPerson();
                if ($oppgaven != null && $this->cd->getAktivBruker() != null && $beboeren != null && $oppgaven->getAnslagPersoner() > sizeof($oppgaven->getPameldteId())) {

                    $paameldt_array = $oppgaven->getPameldteId();
                    if ($paameldt_array == null) {
                        $paameldt_array = array($beboeren->getId());
                    } else {
                        $paameldt_array[] = $beboeren->getId();
                    }
                    $paameldt_json = json_encode($paameldt_array);
                    $id = $oppgaven->getId();
                    $st = DB::getDB()->prepare('UPDATE oppgave SET paameldte=:paameldte WHERE id=:id');
                    $st->bindParam(':paameldte', $paameldt_json);
                    $st->bindParam(':id', $id);
                    $st->execute();
                }
            }
            //Melder aktiv bruker AV på aktuell regi-oppgave.
            //data: 'meldAv=1&id=' + id,
            elseif (isset($post['meldAv']) && $post['meldAv'] == 1 && isset($post['id']) && is_numeric($post['id'])) {
                $oppgaven = Oppgave::medId($post['id']);
                if($oppgaven->erFryst()){
                    Funk::setError("Du kan ikke melde deg av - oppgaven er fryst.");
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }
                $beboeren = $this->cd->getAktivBruker()->getPerson();
                if ($oppgaven != null && $this->cd->getAktivBruker() != null && $beboeren != null && in_array($beboeren, $oppgaven->getPameldteBeboere())) {
                    $paameldt_array = $oppgaven->getPameldteId();
                    $ny_paameldt_array = array();
                    foreach ($paameldt_array as $id) {
                        if ($id == $beboeren->getId()) {
                            continue;
                        }
                        $ny_paameldt_array[] = $id;
                    }
                    $ny_paameldt_json = json_encode($ny_paameldt_array);
                    $id = $oppgaven->getId();
                    $st = DB::getDB()->prepare('UPDATE oppgave SET paameldte=:paameldte WHERE id=:id');
                    $st->bindParam(':paameldte', $ny_paameldt_json);
                    $st->bindParam(':id', $id);
                    $st->execute();
                    
                    $tittel = "[SING-INTERN] En beboer har meldt seg av en oppgave.";
                    $beskjed = $beboeren->getFulltNavn() . " har meldt seg av oppgaven " . $oppgaven->getNavn() . '.';
                    if ($oppgaven->getTidUtfore() !== null){
                        $beskjed .= ' Oppgaven skulle utføres ' . $oppgaven->getTidUtfore();
                    }
                    Epost::sendEpost('regisjef@singsaker.no', $tittel, $beskjed);
                }

            }
        }
        $oppgaveListe = OppgaveListe::ikkeGodkjente();

        uasort($oppgaveListe, function ($a, $b) {
            /* @var Oppgave $a $b */
            if ($a->erFryst() && $b->erFryst()) {
                return 0;
            }
            if ($a->erFryst()) {
                return 1;
            }
            return -1;
        });

        $dok = new Visning($this->cd);
        $aktuell_beboer = $this->cd->getAktivBruker()->getPerson();
        $dok->set('aktuell_beboer', $aktuell_beboer);
        $dok->set('oppgaveListe', $oppgaveListe);
        $dok->vis('regi/regi_oppgaver.php');
    }
}

