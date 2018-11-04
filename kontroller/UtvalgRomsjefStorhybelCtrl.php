<?php

namespace intern3;


class UtvalgRomsjefStorhybelCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            switch ($aktueltArg) {

                case 'liste':
                    $nesteArg = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
                    if (($lista = Storhybelliste::medId($sisteArg)) !== null) {
                        $this->handleListe($lista, $nesteArg);
                    }
                    break;
                case 'ny':

                    $ledige_rom = RomListe::alleLedige();
                    $beboerliste = BeboerListe::aktive();
                    usort($beboerliste, array('\intern3\Beboer', 'storhybelSort'));

                    Storhybelliste::nyListe($ledige_rom, $beboerliste);

                    Funk::setSuccess("Opprettet en ny Storhybelliste!");

                    header('Location: ?a=utvalg/romsjef/storhybel');
                    exit();

            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

            switch ($aktueltArg) {


                case 'liste':

                    if ($sisteArg !== $aktueltArg && is_numeric($sisteArg) &&
                        ($lista = Storhybelliste::medId($sisteArg)) !== null) {

                        $alle_rom = array_udiff(RomListe::alle(), $lista->getLedigeRom(),
                            function(Rom $a, Rom $b) {
                                return $a->getId() - $b->getId();
                            });
                        $ledige_rom = RomListe::alleLedige();
                        $dok = new Visning($this->cd);
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


    private function handleListe(Storhybelliste $lista, $aktueltArg)
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        switch ($aktueltArg) {

            case 'oppdater':

                if ($lista->erAktiv()) {
                    print "Kan ikke flytte på rekkefølgen mens lista er aktiv!";
                    exit();
                }

                if (($beboer = Beboer::medId($post['beboer_id'])) !== null && is_numeric($post['nummer'])) {
                    $lista->flyttBeboer($beboer, $post['nummer']);
                    print "Flyttet " . $beboer->getFulltNavn() . " til posisjon $post[nummer]";
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
                    $liste->deaktiver();
                }

                $lista->aktiver();

                print "Aktiverte denne storhybellista!";
                break;

            case 'deaktiver':
                $lista->deaktiver();
                print "Deaktiverte denne storhybellista!";

            case 'fjernbeboer':
                if (($beboer = Beboer::medId($post['beboer_id'])) !== null) {
                    $lista->fjernBeboer($post['beboer_id']);
                    print "Fjerna beboeren " . $beboer->getFulltNavn() . " fra lista.";
                }
                break;
            case 'fjernrom':
                if(($rom = Rom::medId($post['rom_id'])) !== null) {
                    $lista->fjernRom($rom);
                    print "Fjerna romnummer " . $rom->getNavn() . " fra lista.";
                }
                break;
            case 'leggtilrom':
                if(($rom = Rom::medId($post['rom_id'])) !== null){
                    $lista->leggtilRom($rom);
                    print "La til romnummer " . $rom->getNavn() . " til lista.";
                }
                break;

        }


    }

}