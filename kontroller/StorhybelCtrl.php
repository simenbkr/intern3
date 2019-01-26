<?php

namespace intern3;


class StorhybelCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {
        /*
         * Hent ut relevant data fra Database før vi kan gjøre noe.
         */
        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        $aktiv_beboer = $this->cd->getAktivBruker()->getPerson(); /* @var Beboer $aktiv_beboer */

        /*
         * Henter ut alle Storhybellister som er aktive OG der den aktive beboeren er registrert.
         */
        $lister_med_beboer = Storhybelliste::listerMedBeboer($aktiv_beboer->getId());


        if (!Storhybelliste::finnesAktive() || count($lister_med_beboer) < 1) {
            // Easter egg.
            exit('<img style="height:100%;width:100%" src="beboerkart/loading.gif">');
        }

        /*
         * Hvis beboeren bare står på én aktiv liste, send beboeren dit.
         */
        if (empty($aktueltArg) && count($lister_med_beboer) == 1) {
            header('Location: ?a=storhybel/' . $lister_med_beboer[0]->getId());
            exit();
        }

        /*
         * Hvis beboeren står på flere aktive lister, lar vi beboeren
         * velger blant disse.
         */
        if (empty($aktueltArg)) {
            $dok = new Visning($this->cd);
            $dok->set('listene', $lister_med_beboer);
            $dok->vis('storhybel/storhybel_lister.php');
            exit();
        } elseif (is_numeric($aktueltArg) && ($lista = Storhybelliste::medId($aktueltArg)) !== null) {


            $aktuell_velger = StorhybelVelger::medBeboerIdStorhybelId($aktiv_beboer->getId(), $lista->getId());
            $persnummer = array();
            $aktiv_velger = null;
            $min_tur = false;
            $kan_passe = false;

            /*
             * Fordi forfatteren var lite gjennomtenkt i utformingen av denne kontrolleren,
             * må vi skifte argument etter at vi har konstatert at det faktisk er snakk om
             * en spesifikk storhybelliste.
             * Noterer at en URI hit er på formatet: ?a=storhybel/<id>[/<request>/<arg1>/<arg2>/../<argN>]
             * Hvor det i klammeparantes er optional.
             */
            $aktueltArg = $this->cd->skiftArg()->getAktueltArg();

            foreach ($aktuell_velger as $velger) {

                /* @var StorhybelVelger $velger */

                // Sjekk om det er den aktive brukerens tur
                if ($lista->getVelgerNr() == $velger->getNummer()) {
                    $min_tur = true;
                    $aktiv_velger = $velger;
                    $velgers_rom = $lista->getFordeling()[$aktiv_velger->getVelgerId()]->getGammeltRomId();
                    $kan_passe = $lista->kanPasse($aktiv_beboer, $aktiv_velger);
                }

                $persnummer[] = $velger->getNummer();
            }

            if(count(Storhybelliste::listerMedBeboer($aktiv_beboer->getId())) > 1) {
                $kan_passe = true;
            }

            $persnummer = implode('., ', $persnummer);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $min_tur) {

                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                switch ($aktueltArg) {

                    case 'velg':

                        /*
                         * Et rom kan kun velges dersom det:
                         *      - Eksisterer
                         *      - Det er beboerens tur
                         *      - Er 'up for grabs'.
                         */
                        if (is_numeric($post['rom_id']) &&
                            ($rom = Rom::medId($post['rom_id'])) !== null
                            && (in_array($rom->getId(), $velgers_rom) || isset($lista->getLedigeRom()[$rom->getId()]))
                        ) {

                            $lista->velgRom($aktiv_velger, $rom);
                            print 'Du har valgt rommet ' . $rom->getNavn() . ' som er av type ' . $rom->getType()->getNavn() . '.';
                        }
                        break;
                    case 'pass':
                        if ($kan_passe && isset($post['sid']) && $post['sid'] == $lista->getId()) {
                            $lista->neste();
                            Funk::setSuccess("Passet til nestemann!");
                        } else {
                            Funk::setError("Kan ikke passe!");
                        }
                    case '':
                    default:

                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {


                switch ($aktueltArg) {

                    case 'modal':

                        if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) && ($rom = Rom::medId($sisteArg)) !== null) {

                            $ekstratekst = '';
                            if ($rom->getId() === $aktiv_beboer->getRom()->getId()) {
                                $ekstratekst = 'Dette er ditt nåværende rom.';
                            }

                            $dok = new Visning($this->cd);
                            $dok->set('rom', $rom);
                            $dok->set('ekstratekst', $ekstratekst);
                            $dok->set('id', $lista->getId());
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
        } else {
            //TODO fiks noe shizzle her kanskje
            print "Noe gikk galt. Denne listen eksisterer ikke, eller er ikke tilgjengelig.";
        }
    }
}