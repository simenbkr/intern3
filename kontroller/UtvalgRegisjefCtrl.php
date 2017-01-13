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
                $unix = $_SERVER['REQUEST_TIME'];
                $dok->set('tabeller', array(
                    'Har gjenværende regitimer' => BrukerListe::harRegiIgjen($unix),
                    'Har ikke gjenværende regitimer' => BrukerListe::harIkkeRegiIgjen($unix)
                ));
                $timer_brukt = Arbeid::getTimerBruktPerSemester();
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
