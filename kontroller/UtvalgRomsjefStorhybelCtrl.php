<?php

namespace intern3;


class UtvalgRomsjefStorhybelCtrl extends AbstraktCtrl
{

    private function handleListe(Storhybelliste $lista, $aktueltArg)
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        switch ($aktueltArg) {

            case 'oppdater':

                if($lista->erFerdig()) {
                    Funk::setError("Du kan ikke flytte på rekkefølgen når lista er ferdig.");
                    header("Location: ?a=utvalg/romsjef/storhybel/liste/{$lista->getId()}");
                    exit();
                }

                if (($velger = StorhybelVelger::medVelgerId($post['velger_id'])) !== null && is_numeric($post['nummer'])) {
                    $lista->flyttVelger($velger, $post['nummer']);
                    print "Flyttet " . $velger->getNavn() . " til posisjon $post[nummer]";
                    exit();
                }
                break;
            case 'slett':
                $out = "Sletta Storhybellisten med navn " . $lista->getNavn() . ".";
                $lista->slett();
                Funk::setSuccess($out);
                break;
            case 'aktiver':
                foreach (Storhybelliste::alle() as $liste) {
                    if ($liste->erAktiv()) {
                        $liste->deaktiver();
                    }
                }

                $lista->aktiver();

                print "Aktiverte denne storhybellista!";
                break;

            case 'deaktiver':
                $lista->deaktiver();
                print "Deaktiverte denne storhybellista!";
                break;
            case 'fjernvelger':
                if (($velger = StorhybelVelger::medVelgerId($post['velger_id'])) !== null) {
                    $lista->fjernVelger($post['velger_id']);
                    print "Fjerna velgeren " . $velger->getNavn() . " fra lista.";
                }
                break;
            case 'fjernrom':
                if (($rom = Rom::medId($post['rom_id'])) !== null) {
                    $lista->fjernRom($rom);
                    print "Fjerna romnummer " . $rom->getNavn() . " fra lista.";
                }
                break;
            case 'leggtilrom':
                if (($rom = Rom::medId($post['rom_id'])) !== null) {
                    $lista->leggtilRom($rom);
                    print "La til romnummer " . $rom->getNavn() . " til lista.";
                }
                break;
            case 'leggtilvelger':
                $beboere = array();
                foreach($post['beboere'] as $beboer_id) {
                    if(($beboer = Beboer::medId($beboer_id)) !== null) {
                        $beboere[] = $beboer;

                    }
                }

                if(count($beboere) < 1) {
                    Funk::setError("Det ser ut til at du har valgt null beboere. Kan dette stemme?");
                    header("Location: ?a=utvalg/romsjef/storhybel/liste/{$lista->getId()}");
                    exit();
                }

                // Opprett velger, legg til velgeren på lista.
                $velger = StorhybelVelger::nyVelger($beboere);
                $lista->leggTilVelger($velger->getVelgerId());
                $velger->setStorhybel($lista->getId());

                // Legg til velgernes rom på fordelinga

                foreach($beboere as $beboer) {
                    /* @var $beboer Beboer */
                    StorhybelFordeling::leggTilRom($lista->getId(), $velger->getVelgerId(), $beboer->getRomId());
                }


                $success = "La til {$velger->getNavn()} som en velger med {$velger->getAnsiennitet()} ansiennitetspoeng.";
                Funk::setSuccess($success);
                header('Location: ?a=utvalg/romsjef/storhybel/liste/' . $lista->getId());
                exit($success);

                break;
            case 'neste':
                $lista->neste();
                print "Det er nå " . $lista->getVelger()->getNavn() . ' sin tur!';
                break;
            case 'forrige':
                $lista->forrige();
                print "Det er nå " . $lista->getVelger()->getNavn() . ' sin tur!';
                break;
            case 'commit':
                $lista->commit();
                print "Lista ble lagret, og rom fordelt.";
                break;
        }
    }

    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            switch ($aktueltArg) {

                case 'liste':
                    $nesteArg = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
                    if (($lista = Storhybelliste::medId($sisteArg)) !== null && !$lista->erFerdig()) {
                        $this->handleListe($lista, $nesteArg);
                    } else {
                        print "Lista er ferdig, og kan derfor ikke endres.";
                    }
                    break;
                case 'ny':

                    $ledige_rom = RomListe::alleLedige();
                    $beboerliste = BeboerListe::aktive();
                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));

                    $lista = Storhybelliste::nyListe($ledige_rom, $beboerliste);
                    $id = $lista->getId();

                    header("Location: ?a=utvalg/romsjef/storhybel/liste/$id");
                    exit();

            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

            switch ($aktueltArg) {


                case 'liste':

                    if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) && !is_null($sisteArg) &&
                        ($lista = Storhybelliste::medId($sisteArg))) {


                        $alle_rom = array_udiff(RomListe::alle(), $lista->getLedigeRom(),
                            function (Rom $a, Rom $b) {
                                return $a->getId() - $b->getId();
                            });


                        $beboerliste = array_udiff(BeboerListe::aktive(), BeboerListe::singleStorhybelliste($lista->getId()),
                            function(Beboer $a, Beboer $b) {
                                return $a->getId() - $b->getId();
                            });

                        $ledige_rom = RomListe::alleLedige();
                        $dok = new Visning($this->cd);
                        $dok->set('beboerliste', $beboerliste);
                        $dok->set('lista', $lista);
                        $dok->set('alle_rom', $alle_rom);
                        $dok->set('ledige_rom', $ledige_rom);
                        $dok->vis('utvalg/romsjef/storhybel_liste_detaljer.php');
                        exit();
                    }

                    $lista = Storhybelliste::alle();

                    $dok = new Visning($this->cd);

                    $dok->set('lista', $lista);
                    $dok->vis('utvalg/romsjef/storhybel_liste.php');
                    break;

                case 'beboerliste':
                    if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) &&
                        ($lista = Storhybelliste::medId($sisteArg)) !== null) {

                        /*$beboerliste = array_udiff(BeboerListe::aktive(), BeboerListe::fraStorhybelliste($lista->getId()),
                            function (Beboer $a, Beboer $b) {
                                return $a->getId() - $b->getId();
                            });
                        */

                        $beboerliste_alle = BeboerListe::aktive();
                        $ikke_reg_beboerliste = array_udiff($beboerliste_alle, BeboerListe::singleStorhybelliste($lista->getId()),
                            function(Beboer $a, Beboer $b) {
                                return $a->getId() - $b->getId();
                            });


                        $dok = new Visning($this->cd);
                        $dok->set('lista', $lista);
                        $dok->set('ikke_reg_beboerliste', $ikke_reg_beboerliste);
                        $dok->set('beboerliste_alle', $beboerliste_alle);
                        $dok->vis('utvalg/romsjef/velg_modal.php');
                        break;

                    }

                case '':
                default:


                    $ledige_rom = RomListe::alleLedige();
                    $beboerliste = BeboerListe::aktive();

                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));
                    $dok = new Visning($this->cd);
                    $dok->set('ledige_rom', $ledige_rom);
                    $dok->set('beboerliste', $beboerliste);
                    $dok->vis('utvalg/romsjef/storhybel_start.php');
            }
        }
    }


}