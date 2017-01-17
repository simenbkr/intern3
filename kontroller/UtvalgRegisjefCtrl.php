<?php

namespace intern3;

class UtvalgRegisjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
            case 'registatus':
                $dok = new Visning($this->cd);

                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    //
                    //'endreRegi=1&halv=' + halv + "&full=" + full,
                    if (isset($post['endreRegi']) && $post['endreRegi'] == 1 && isset($post['halv']) && is_numeric($post['halv'])
                        && isset($post['full']) && is_numeric($post['full'])) {
                        $halv = $post['halv'];
                        $full = $post['full'];

                        $st = DB::getDB()->prepare('UPDATE rolle SET regitimer=:halv WHERE id=1');
                        $st->bindParam(':halv', $halv);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE rolle SET regitimer=:fullregi WHERE id=3');
                        $st2->bindParam(':fullregi', $full);
                        $st2->execute();
                    }
                }


                $unix = $_SERVER['REQUEST_TIME'];
                $dok->set('tabeller', array(
                    'Har gjenværende regitimer' => BrukerListe::harRegiIgjen($unix),
                    'Har ikke gjenværende regitimer' => BrukerListe::harIkkeRegiIgjen($unix)
                ));
                $timer_brukt = Arbeid::getTimerBruktPerSemester();
                $roller = RolleListe::alle();
                $dok->set('roller', $roller);
                $dok->set('timer_brukt', $timer_brukt);
                $dok->vis('regi_registatus.php');
                return;
            case 'arbeid':
                $valgtCtrl = new UtvalgRegisjefArbeidCtrl($this->cd->skiftArg());
                break;
            case 'oppgave':
                $valgtCtrl = new UtvalgRegisjefOppgaveCtrl($this->cd->skiftArg());
                break;
            default:
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_regisjef.php');
                return;
        }
        $valgtCtrl->bestemHandling();
    }
}

?>
