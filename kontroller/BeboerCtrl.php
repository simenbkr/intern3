<?php

namespace intern3;

class BeboerCtrl extends AbstraktCtrl
{
    private $histogram = array();

    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'utskrift') {
            $beboerListe = BeboerListe::aktive();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('skjulMeny', 1);
            $dok->vis('beboer_utskrift.php');
        } else if ($aktueltArg == 'statistikk') {
            $this->visStatistikk();
        } else if ($aktueltArg == 'kart') {
            $dok = new Visning($this->cd);
            $beboerlista = BeboerListe::aktive();
            $dok->set('beboerlista', $beboerlista);
            $dok->vis('beboer_kart.php');
        } else if ($aktueltArg == 'gamle') {
            $dok = new Visning($this->cd);
            $beboerListe = BeboerListe::ikkeAktive();
            $dok->set('beboerlista', $beboerListe);
            $dok->vis('beboer_gamle.php');
        } else if($aktueltArg == 'olstat'){
            $dok = new Visning($this->cd);

            $dok->vis('beboer_olstat.php');
        }

        else if (is_numeric($aktueltArg)) {
            $beboer = Beboer::medId($aktueltArg);
            // Trenger feilhåndtering her.
            $dok = new Visning($this->cd);
            $dok->set('beboer', $beboer);
            $dok->vis('beboer_detaljer.php');
        } else {
            $beboerListe = BeboerListe::aktive();
            
            $fullvakt = 0;
            $fullregi = 0;
            $halv     = 0;
            
            foreach($beboerListe as $beboer){
                /* @var Beboer $beboer */
                switch($beboer->getRolle()->getRegitimer()){
                  case 0:
                      $fullvakt++;
                      break;
                  case 18:
                      $halv++;
                      break;
                  case 48:
                      $fullregi++;
                }
            }
            
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('fullregi',$fullregi);
            $dok->set('fullvakt', $fullvakt);
            $dok->set('halv', $halv);
            $dok->vis('beboer_beboerliste.php');
        }
    }

    private function visStatistikk()
    {
        $beboerListe = BeboerListe::aktive();
        $this->histogram = array();
        foreach ($beboerListe as $beboer) {
            $fodtar = $beboer->getFodselsar();
            if ($fodtar <> '0000' && $fodtar != 1920) {
                $this->addStatistikk('Når beboerne er født', $fodtar);
            }
            $bebodd = $beboer->getRomhistorikk()->getAntallSemestre();
            $this->addStatistikk('Antall semestre på huset', $bebodd);
            $trinn = $beboer->getKlassetrinn();
            if ($trinn <> '0') {
                $this->addStatistikk('Fordelt på klassetrinn', $trinn);
            }
            $studie = $beboer->getStudie()->getNavn();
            $this->addStatistikk('Fordelt på studier', $studie);
        }
        foreach ($this->histogram as $navn => $hist) {
            ksort($hist, SORT_NUMERIC);
            $this->histogram[$navn] = $hist;
        }
        $dok = new Visning($this->cd);
        $dok->set('histogram', $this->histogram);
        $dok->vis('beboer_statistikk.php');
    }

    private function addStatistikk($navn, $nokkel, $verdi = 1)
    {
        if (!isset($this->histogram[$navn][$nokkel])) {
            $this->histogram[$navn][$nokkel] = 0;
        }
        $this->histogram[$navn][$nokkel] += $verdi;
    }
}

?>
