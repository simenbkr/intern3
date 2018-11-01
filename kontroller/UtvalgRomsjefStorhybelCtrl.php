<?php

namespace intern3;


class UtvalgRomsjefStorhybelCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            switch ($aktueltArg) {

                case 'ny':

                    $ledige_rom = RomListe::alleLedige();
                    $beboerliste = BeboerListe::aktive();

                    Storhybelliste::nyListe($ledige_rom, $beboerliste);

                    Funk::setSuccess("Opprettet en ny Storhybelliste!");

                    header('Location: ?a=utvalg/romsjef/storhybel');
                    exit();

            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

            switch ($aktueltArg) {


                case 'liste':

                    if($sisteArg !== $aktueltArg && is_numeric($sisteArg) &&
                        ($lista = Storhybelliste::medId($sisteArg)) !== null) {

                        $dok = new Visning($this->cd);
                        $dok->set('lista', $lista);
                        $dok->vis('utvalg/romsjef/storhybel_liste_detaljer.php');
                        break;
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


}