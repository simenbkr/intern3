<?php

namespace intern3;

class HovedCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        Session::refresh();

        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'vinkjeller') {
            $valgtCtrl = new VinkjellerCtrl($this->cd->skiftArg());
            $valgtCtrl->bestemHandling();
            return;
        }

        if ($aktueltArg == 'journal') {
            $valgtCtrl = new JournalCtrl($this->cd->skiftArg());
            $valgtCtrl->bestemHandling();
            return;
        }

        if($aktueltArg == 'extern') {
            $valgtCtrl = new ExternCtrl($this->cd->skiftArg());
            $valgtCtrl->bestemHandling();
            return;
        }

        $aktivBruker = $this->cd->getAktivBruker();
        if ($aktivBruker == null) {
            $valgtCtrl = new LogginnCtrl($this->cd->skiftArg());
            $valgtCtrl->bestemHandling();
            return;
        }
        $this->cd->setAktivBruker($aktivBruker);
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg <> 'logginn' && $aktivBruker->getPerson()->erBeboer()) {
            $kvittering = Kvittering::detteSemesterMedRomId($aktivBruker->getPerson()->getRomId());
            if (($kvittering == null || $kvittering->getId() == null) && false) {
                $valgtCtrl = new RomskjemaCtrl($this->cd->skiftArg());
                $valgtCtrl->tvungenRegistrering();
                return;
            }
        }
        $this->bestemKontroller();
    }

    public function bestemKontroller()
    {
        switch ($this->cd->getAktueltArg()) {
            case 'jubileum':
                $valgtCtrl = new JubileumCtrl($this->cd->skiftArg());
                break;
            case 'storhybel':
                $valgtCtrl = new StorhybelCtrl($this->cd->skiftArg());
                break;
            case 'beboer':
                $valgtCtrl = new BeboerCtrl($this->cd->skiftArg());
                break;
            case 'vakt':
                $valgtCtrl = new VaktCtrl($this->cd->skiftArg());
                break;
            case 'regi':
                $valgtCtrl = new RegiCtrl($this->cd->skiftArg());
                break;
            case 'verv':
                $valgtCtrl = new VervCtrl($this->cd->skiftArg());
                break;
            case 'kryss':
                $valgtCtrl = new KryssCtrl($this->cd->skiftArg());
                break;
            case 'wiki':
                $valgtCtrl = new WikiCtrl($this->cd->skiftArg());
                break;
            case 'utleie':
                $valgtCtrl = new UtleieCtrl($this->cd->skiftArg());
                break;
            case 'helga':
                $valgtCtrl = new HelgaCtrl($this->cd->skiftArg());
                break;
            case 'rombytte':
                $valgtCtrl = new RombytteCtrl($this->cd->skiftArg());
                break;
            case 'profil':
                $valgtCtrl = new ProfilCtrl($this->cd->skiftArg());
                break;
            case 'romskjema':
                $valgtCtrl = new RomskjemaCtrl($this->cd->skiftArg());
                break;
            case 'utflytting':
                $valgtCtrl = new UtflyttingCtrl($this->cd->skiftArg());
                break;
            case 'logginn':
                $valgtCtrl = new LogginnCtrl($this->cd->skiftArg());
                break;
            case 'utvalg':
                $valgtCtrl = new UtvalgCtrl($this->cd->skiftArg());
                break;
            case 'journal':
                $valgtCtrl = new JournalCtrl($this->cd->skiftArg());
                break;
            case 'kjeller':
                $valgtCtrl = new KjellerCtrl($this->cd->skiftArg());
                break;
            case 'vinkjeller':
                $valgtCtrl = new VinkjellerCtrl($this->cd->skiftArg());
                break;
            case 'diverse':
            default:
                $valgtCtrl = new DiverseCtrl($this->cd->skiftArg());
                break;
        }
        $valgtCtrl->bestemHandling();
    }
}

?>
