<?php

namespace intern3;


class StorhybelCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {

        if(!Storhybelliste::finnesAktive()) {
            // Easter egg.
            exit('<img style="height:100%;width:100%" src="beboerkart/loading.gif">');
        }


        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();

        $lista = Storhybelliste::aktiv();
        $aktiv_beboer = $this->cd->getAktivBruker()->getPerson();
        $aktiv_velger = StorhybelVelger::medBeboerIdStorhybelId($aktiv_beboer->getId(), $lista->getId());
        $nummer = $aktiv_velger->getNummer();
        $beboers_rom = $aktiv_beboer->getRom();
        $min_tur = ($lista->getVelgerNr() == $aktiv_velger->getNummer());

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            switch($aktueltArg) {

                case 'velg':

                    /*
                     * Et rom kan kun velges dersom det:
                     *      - Eksisterer
                     *      - Det er beboerens tur
                     *      - Er 'up for grabs'.
                     */
                    if(  is_numeric($post['rom_id']) &&
                        ($rom = Rom::medId($post['rom_id'])) !== null &&
                         $min_tur &&
                        ($rom->getId() === $beboers_rom->getId() || isset($lista->getLedigeRom()[$rom->getId()]))
                    ) {

                        $lista->velgRom($aktiv_velger, $rom);
                        print 'Du har valgt rommet ' . $rom->getNavn() . ' som er av type '. $rom->getType()->getNavn() . '.';
                    }


                case '':
                default:

            }
        }

        elseif($_SERVER['REQUEST_METHOD'] === 'GET') {

            switch ($aktueltArg) {

                case 'modal':

                    if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) && ($rom = Rom::medId($sisteArg)) !== null) {

                        $ekstratekst = '';
                        if($rom->getId() === $aktiv_beboer->getRom()->getId()) {
                            $ekstratekst = 'Dette er ditt nåværende rom.';
                        }

                        $dok = new Visning($this->cd);
                        $dok->set('rom', $rom);
                        $dok->set('ekstratekst', $ekstratekst);
                        $dok->vis('storhybel/velg_modal.php');
                        exit();
                    }
                    break;
                case '':
                default:

                    $dok = new Visning($this->cd);
                    $dok->set('lista', $lista);
                    $dok->set('persnummer', $nummer);
                    $dok->set('min_tur', $min_tur);
                    $dok->set('beboers_rom', $beboers_rom);
                    $dok->vis('storhybel/storhybel.php');
                    break;
            }
        }
    }
}