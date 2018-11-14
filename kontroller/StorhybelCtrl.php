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
        $aktuell_velger = StorhybelVelger::medBeboerIdStorhybelId($aktiv_beboer->getId(), $lista->getId());

        $persnummer = array();
        $aktiv_velger = null;
        $min_tur = false;
        $kan_passe = false;
        foreach($aktuell_velger as $velger) {

            /* @var StorhybelVelger $velger */

            // Sjekk om det er den aktive brukerens tur
            if($lista->getVelgerNr() == $velger->getNummer()) {
                $min_tur = true;
                $aktiv_velger = $velger;
                $velgers_rom = $lista->getFordeling()[$aktiv_velger->getVelgerId()]->getGammeltRomId();
                $kan_passe = $lista->kanPasse($aktiv_beboer, $aktiv_velger);
            }

            $persnummer[] = $velger->getNummer();
        }

        $persnummer = implode('., ', $persnummer);

        if($_SERVER['REQUEST_METHOD'] === 'POST' && $min_tur) {

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
                        ($rom = Rom::medId($post['rom_id'])) !== null
                        && (in_array($rom->getId(), $velgers_rom) || isset($lista->getLedigeRom()[$rom->getId()]))
                    ) {

                        $lista->velgRom($aktiv_velger, $rom);
                        print 'Du har valgt rommet ' . $rom->getNavn() . ' som er av type '. $rom->getType()->getNavn() . '.';
                    }

                case 'pass':
                    if($kan_passe && isset($post['sid']) && $post['sid'] == $lista->getId()) {
                        $lista->neste();
                        Funk::setSuccess("Passet til nestemann!");
                    } else {
                        Funk::setError("Kan ikke passe!");
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
                    $dok->set('min_tur', $min_tur);
                    $dok->set('kan_passe', $kan_passe);
                    $dok->set('persnummer', $persnummer);
                    $dok->set('aktiv_velger', $aktiv_velger);
                    $dok->set('aktiv_beboer', $aktiv_beboer);
                    $dok->vis('storhybel/storhybel.php');
                    break;
            }
        }
    }
}