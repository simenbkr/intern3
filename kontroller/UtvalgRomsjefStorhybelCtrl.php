<?php

namespace intern3;


class UtvalgRomsjefStorhybelCtrl extends AbstraktCtrl
{

    private function handleListe(Storhybelliste $lista, $aktueltArg)
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        switch ($aktueltArg) {

            case 'oppdater':

                if ($lista->erFerdig()) {
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
                /*
                 * Deaktiver alle andre lister av samme type.
                 */
                $common = explode(' ', $lista->getNavn())[0];
                foreach (Storhybelliste::alle() as $liste) {
                    if ($liste->erAktiv() && strpos($liste->getNavn(), $common) !== false) {
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
                foreach ($post['beboere'] as $beboer_id) {
                    if (($beboer = Beboer::medId($beboer_id)) !== null) {
                        $beboere[] = $beboer;

                    }
                }

                if (count($beboere) < 1) {
                    Funk::setError("Det ser ut til at du har valgt null beboere. Kan dette stemme?");
                    header("Location: ?a=utvalg/romsjef/storhybel/liste/{$lista->getId()}");
                    exit();
                }

                if ($post['mode'] == 'dobbel') {

                    if (strpos($lista->getNavn(), 'Korr' !== false)) {
                        Funk::setError("Du kan ikke ha dobbel modus når du legger til velgere på Korrhybellista!");
                        header("Location: ?a=utvalg/romsjef/storhybel/liste/{$lista->getId()}");
                        exit();
                    }

                    // Opprett velger, legg til velgeren på lista.
                    $velger = StorhybelVelger::nyVelger($beboere);
                    $lista->leggTilVelger($velger->getVelgerId());
                    $velger->setStorhybel($lista->getId());

                    // Legg til velgernes rom på fordelinga

                    foreach ($beboere as $beboer) {
                        /* @var $beboer Beboer */
                        StorhybelFordeling::leggTilRom($lista->getId(), $velger->getVelgerId(), $beboer->getRomId());
                    }


                    $success = "La til {$velger->getNavn()} som en velger med {$velger->getAnsiennitet()} ansiennitetspoeng.";
                    Funk::setSuccess($success);
                    header('Location: ?a=utvalg/romsjef/storhybel/liste/' . $lista->getId());
                    exit($success);
                    break;
                } elseif ($post['mode'] == 'singel') {

                    if ((strpos($lista->getNavn(), 'Storparhybel') !== false)) {
                        Funk::setError("Du kan ikke ha singel modus når du legger til velgere på Storparhybelliste!");
                        header("Location: ?a=utvalg/romsjef/storhybel/liste/{$lista->getId()}");
                        exit();
                    }

                    $success = '';
                    foreach ($beboere as $beboer) {

                        $velger = StorhybelVelger::nyVelger(array($beboer));
                        $lista->leggTilVelger($velger->getVelgerId());
                        $velger->setStorhybel($lista->getId());

                        // Legg til velgernes rom på fordelinga
                        StorhybelFordeling::leggTilRom($lista->getId(), $velger->getVelgerId(), $beboer->getRomId());
                        $success .= "La til {$velger->getNavn()} som en velger med {$velger->getAnsiennitet()} ansiennitetspoeng.<br/>";
                    }
                    Funk::setSuccess($success);
                    header('Location: ?a=utvalg/romsjef/storhybel/liste/' . $lista->getId());
                    exit($success);
                    break;
                }
                break;
            case 'omgjor':
                if (($velger = StorhybelVelger::medVelgerId($post['velger_id'])) !== null) {
                    $lista->omgjor($post['velger_id']);
                    print "Omgjorde " . $velger->getNavn() . " sitt valg. Denne skal nå velge på nytt.";
                }
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

    private function validerParListe($liste)
    {
        $flattened = array();
        foreach ($liste as $sublist) {
            $flattened = array_merge($flattened, $sublist);
        }

        $unique = array_unique($flattened);

        if (count($unique) < count($flattened)) {
            die("Duplikat!");
        }

    }

    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

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

                    $ledige_rom = RomListe::ledigeStorhybelRom();
                    $beboerliste = BeboerListe::aktive();
                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));

                    $lista = Storhybelliste::nyListe($ledige_rom, $beboerliste);
                    $id = $lista->getId();

                    header("Location: ?a=utvalg/romsjef/storhybel/liste/$id");
                    exit();

                case 'korr':
                    $ledige_rom = RomListe::ledigeKorrhybler();
                    $beboerliste = array();

                    foreach (filter_var_array(json_decode($_POST['beboer_ids'])) as $id) {
                        $beboerliste[] = Beboer::medId($id);
                    }

                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));
                    $lista = Storhybelliste::nyListe($ledige_rom, $beboerliste, 'Korrhybelliste');
                    $id = $lista->getId();

                    print $id;
                    exit();
                case 'sp':

                    /*
                     * Sett opp velgerne først slik at det er i orden.
                     */
                    $parliste = filter_var_array(json_decode($_POST['parliste']), FILTER_VALIDATE_INT);
                    $this->validerParListe($parliste);
                    $velgere = array();
                    $beboere = array();

                    foreach ($parliste as $par) {

                        $tmp = array();
                        foreach ($par as $id) {
                            $tmp[] = ($beb = Beboer::medId($id));
                            $beboere[] = $beb;
                        }

                        $velger = StorhybelVelger::nyVelger($tmp);
                        $velgere[] = $velger;
                    }

                    /*
                     * Opprett tom liste, sleng inn ledige store parhybler.
                     */
                    $lista = Storhybelliste::nyTomListe('Storparhybelliste');
                    $lista->setLedigeRom(RomListe::ledigeStoreParhybler());

                    /*
                     * Sorter velgere. Likner på StorhybelSort i Beboer-modellen.
                     */
                    usort($velgere, function (StorhybelVelger $a, StorhybelVelger $b) {
                        if ($a->getAnsiennitet() > $b->getAnsiennitet()) {
                            return -1;
                        }

                        if ($a->getAnsiennitet() == $b->getAnsiennitet() && $a->getMaxKlassetrinn() > $b->getMaxKlassetrinn()) {
                            return -1;
                        }

                        if ($a->getAnsiennitet() == $b->getAnsiennitet() && $a->getMaxKlassetrinn() == $b->getMaxKlassetrinn()) {
                            return 0;
                        }

                        return 1;
                    });

                    $lista->setRekkefolge($velgere);

                    foreach ($velgere as $nr => $velger) {
                        /* @var $velger StorhybelVelger */

                        $lista->leggTilVelger($velger->getVelgerId(), $nr + 1);
                        $velger->setStorhybel($lista->getId());

                        foreach ($velger->getBeboere() as $beboer) {
                            /* @var $beboer Beboer */
                            StorhybelFordeling::leggTilRom($lista->getId(), $velger->getVelgerId(), $beboer->getRomId());
                        }
                    }
                    Funk::setSuccess("En Storparhybelliste ble oppretta!");
                    print "{$lista->getId()}";
            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

            switch ($aktueltArg) {

                case 'liste':

                    if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) && !is_null($sisteArg) &&
                        ($lista = Storhybelliste::medId($sisteArg))) {

                        if (strpos($lista->getNavn(), 'Korrhybelliste') !== false) {
                            $base = RomListe::alleKorrhybler();
                        } elseif (strpos($lista->getNavn(), 'Storparhybelliste') !== false) {
                            $base = RomListe::alleStoreParhybler();
                        } else {
                            $base = RomListe::alle();
                        }

                        $alle_rom = array_udiff($base, $lista->getLedigeRom(),
                            function (Rom $a, Rom $b) {
                                return $a->getId() - $b->getId();
                            });

                        $dok = new Visning($this->cd);
                        $dok->set('beboerliste', BeboerListe::aktive());
                        $dok->set('lista', $lista);
                        $dok->set('alle_rom', $alle_rom);
                        $dok->vis('utvalg/romsjef/storhybel_liste_detaljer.php');
                        exit();
                    }

                    $lista = Storhybelliste::alle();

                    $dok = new Visning($this->cd);

                    $dok->set('lista', $lista);
                    $dok->vis('utvalg/romsjef/storhybel_liste.php');
                    break;

                case 'korr':
                    $ledige_rom = RomListe::ledigeKorrhybler();
                    $beboerliste = BeboerListe::aktive();

                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));
                    $dok = new Visning($this->cd);
                    $dok->set('ledige_rom', $ledige_rom);
                    $dok->set('beboerliste', $beboerliste);
                    $dok->vis('utvalg/romsjef/storhybel_korrhybel.php');
                    break;

                case 'storparhybel';
                    $ledige_rom = RomListe::ledigeStoreParhybler();
                    $beboerliste = BeboerListe::aktive();

                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));
                    $dok = new Visning($this->cd);
                    $dok->set('ledige_rom', $ledige_rom);
                    $dok->set('beboerliste', $beboerliste);
                    $dok->vis('utvalg/romsjef/storhybel_storparhybel.php');
                    break;

                case 'storparhybel_select':

                    $beboerliste = BeboerListe::aktive();
                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));
                    $dok = new Visning($this->cd);
                    $dok->set('beboerliste', $beboerliste);
                    $dok->vis('utvalg/romsjef/storhybel_parhybel_select.php');
                    break;

                case 'beboerliste':
                    if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) &&
                        ($lista = Storhybelliste::medId($sisteArg)) !== null) {

                        $beboerliste_alle = BeboerListe::aktive();

                        $ikke_reg_beboerliste = array_udiff($beboerliste_alle, BeboerListe::singleStorhybelliste($lista->getId()),
                            function (Beboer $a, Beboer $b) {
                                return $a->getId() - $b->getId();
                            });

                        $bare_manglende = (strpos($lista->getNavn(), 'Korr') !== false);
                        $bare_manglende = $bare_manglende || (strpos($lista->getNavn(), 'Storparhybel') !== false);

                        $dok = new Visning($this->cd);
                        $dok->set('lista', $lista);
                        $dok->set('bare_manglende', $bare_manglende);
                        $dok->set('ikke_reg_beboerliste', $ikke_reg_beboerliste);
                        $dok->set('beboerliste_alle', $beboerliste_alle);
                        $dok->vis('utvalg/romsjef/velg_modal.php');
                        break;

                    }

                case '':
                default:

                    $ledige_rom = RomListe::ledigeStorhybelRom();
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