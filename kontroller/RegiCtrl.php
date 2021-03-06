<?php

namespace intern3;

class RegiCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
            case 'registatus':
            if(false){
                $dok = new Visning($this->cd);
                $unix = $_SERVER['REQUEST_TIME'];
                $dok->set('tabeller', array(
                    'Har gjenværende regitimer' => BrukerListe::harRegiIgjen($unix),
                    'Har ikke gjenværende regitimer' => BrukerListe::harIkkeRegiIgjen($unix)
                ));
                $dok->vis('regi/regi_registatus.php');
                return;
            }
            case 'rapport':
                $valgtCtrl = new RegiRapportCtrl($this->cd->skiftArg());
                break;
            case 'regivakt':
                $valgtCtrl = new RegiVaktCtrl($this->cd->skiftArg());
                break;
            case 'minregi':
                $valgtCtrl = new RegiMinregiCtrl($this->cd->skiftArg());
                break;
            case 'oppgave':
                $valgtCtrl = new RegiOppgaveCtrl($this->cd->skiftArg());
                break;

            case 'minregi':
                $dok = new Visning($this->cd);
                $unix = $_SERVER['REQUEST_TIME'];
                $dok->vis('regi/regi_minregi.php');
                return;
            case '':
            default:
                $dok = new Visning($this->cd);
                $dok->vis('regi/regi.php');
                return;
        }
        $valgtCtrl->bestemHandling();
    }
}