<?php

namespace intern3;

class UtvalgCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktivBruker = $this->cd->getAktivBruker();
        if ($aktivBruker->getPerson()->harUtvalgVerv()) {
            $aktueltArg = $this->cd->getAktueltArg();
            switch ($aktueltArg) {
                case 'romsjef':
                    $valgtCtrl = new UtvalgRomsjefCtrl($this->cd->skiftArg());
                    break;
                case 'regisjef':
                    $valgtCtrl = new UtvalgRegisjefCtrl($this->cd->skiftArg());
                    break;
                case 'sekretar':
                    $valgtCtrl = new UtvalgSekretarCtrl($this->cd->skiftArg());
                    break;
                case 'vaktsjef':
                    $valgtCtrl = new UtvalgVaktsjefCtrl($this->cd->skiftArg());
                    break;
                case 'kosesjef':
                    $valgtCtrl = new UtvalgKosesjefCtrl($this->cd->skiftArg());
                    break;
                case 'admin':
                    $valgtCtrl = new UtvalgAdminCtrl($this->cd->skiftArg());
                    break;
                default:
                    $valgtCtrl = new UtvalgDiverseCtrl($this->cd->skiftArg());
                    break;
            }
            $valgtCtrl->bestemHandling();
        }
    }
}

?>
