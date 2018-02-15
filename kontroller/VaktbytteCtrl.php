<?php

namespace intern3;


class VaktbytteCtrl extends AbstraktCtrl
{

    private function redirect()
    {
        header('Location: ?a=vakt/bytte');
        exit();
    }

    public function bestemHandling()
    {

        $aktueltArg = $this->cd->getAktueltArg();
        $bruker = LogginnCtrl::getAktivBruker();

        //API-endpoint for å endre ting.
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $sisteArg = $this->cd->getSisteArg();
            switch ($aktueltArg) {

                //Slette eget vaktbytte
                case 'slett':

                    if (($vaktbyttet = Vaktbytte::medId($sisteArg)) != null &&
                        $bruker->getId() == $vaktbyttet->getVakt()->getBrukerId()) {

                        $vaktbyttet->slett();
                        Funk::setSuccess("Vaktbyttet ditt ble slettet!");
                    } else {
                        Funk::setError("Noe galt skjedde. Vaktbyttet ble ikke sletta.");
                    }

                    $this->redirect();
                    break;

                //Legge til eget vaktbytte
                case 'leggtil':
                    //byttes=>[byttes|gisbort], passord=>[yes|no], passordtekst => passordtekst, merknad => merknad

                    if (!(($vakt = Vakt::medId($sisteArg)) != null || $vakt->getBrukerId() === $bruker->getId())
                        || $vakt->erStraffevakt()) {
                        Funk::setError("Denne vakten kan ikke byttes!");
                        break;
                    }

                    if($vakt->erFerdig()){
                        Funk::setError("Denne vakten er ferdig, eller kan ikke byttes!");
                    }

                    if (Vaktbytte::medVaktId($sisteArg) != null) {
                        Funk::setError("Denne vakten er allerede på byttemarkedet!");
                        break;
                    }

                    $gisbort = ($post['byttes'] == 'byttes' ? 0 : 1);
                    $harpw = ($post['passord'] == 'yes' ? 1 : 0);
                    Vaktbytte::nyttVaktBytte($sisteArg, $gisbort, $post['merknad'], $harpw, $post['passordtekst']);

                    Funk::setSuccess("Vaktbyttet ble oppretta for  " . $vakt->toString());

                    $this->redirect();
                    break;

                //Ta vakt som er gitt bort, med og uten passord
                case 'ta':
                    if (($vaktbyttet = Vaktbytte::medId($sisteArg)) != null &&
                        $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId()
                        && $vaktbyttet->getGisBort()) {

                        if (!$vaktbyttet->harPassord() ||
                            ($vaktbyttet->harPassord() && $vaktbyttet->stemmerPassord($post['passord']))) {

                            $vakta = $vaktbyttet->getVakt();
                            $vakta->fjernFraAlleBytter();

                            $vakta->setBruker($bruker->getId());
                            $vaktbyttet->slett();

                            Funk::setSuccess("Du tok vakta " . $vakta->toString());
                            $this->redirect();
                        } else {
                            Funk::setError("Feil passord!");
                            $this->redirect();
                        }
                    }

                    Funk::setError("Noe gikk galt. Du fikk ikke tatt vakta!");
                    $this->redirect();
                    break;

                //Legge til forslag på vaktbytte
                case 'forslag':

                    if (($vaktbyttet = Vaktbytte::medId($sisteArg)) == null || ($forslag_vakt = Vakt::medId($post['vakt'])) == null) {
                        Funk::setError("Det ser ut til at dette vaktbyttet eller den foreslåtte vakta ikke eksisterer!");
                        $this->redirect();
                    }

                    if(!$vaktbyttet->harPassord() ||
                        ($vaktbyttet->harPassord() && $vaktbyttet->stemmerPassord($post['passord']))){

                        $vaktbyttet->leggTilForslag($forslag_vakt->getId());
                        Funk::setSuccess("Vakta " . $forslag_vakt->toString() . " ble lagt til som forslag!");
                        $this->redirect();
                    } else {
                        Funk::setError("Noe gikk galt! Du fikk ikke foreslått vakt. Feil passord?");
                    }

                    break;
                //Godta bytteforslag
                case 'bytte':

                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) != null &&
                        $bruker->getId() == $vaktbyttet->getVakt()->getBrukerId() &&
                        ($forslag_vakt = Vakt::medId($post['vakt'])) != null &&
                         $forslag_vakt->getBrukerId() != $vaktbyttet->getVakt()->getBrukerId() &&
                        in_array($forslag_vakt->getId(), $vaktbyttet->getForslagIder())){

                        $bytte_vakt = $vaktbyttet->getVakt();

                        $forslag_bruker_id = $forslag_vakt->getBrukerId();
                        $bytte_bruker_id = $vaktbyttet->getVakt()->getBrukerId();

                        $forslag_vakt->setBruker($bytte_bruker_id);
                        $bytte_vakt->setBruker($forslag_bruker_id);

                        $forslag_vakt->fjernFraAlleBytter();
                        $bytte_vakt->fjernFraAlleBytter();

                        if($forslag_vakt->getVaktbytte() != null){
                            $forslag_vakt->getVaktbytte()->slett();
                        }

                        $vaktbyttet->slett();

                        Funk::setSuccess("Vaktbyttet ble gjennomført!");

                        $this->redirect();

                    } else {
                        Funk::setError("Du kan ikke godta denne vakta!");
                        $this->redirect();
                    }

                    break;

                //Fjerne bytteforslag
                case 'slettbytte':

                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) != null &&
                        ($vakt = Vakt::medId($post['vakt'])) != null &&
                        $bruker->getId() == $vakt->getBrukerId() &&
                        in_array($vakt->getId(), $vaktbyttet->getForslagIder())) {

                        $vaktbyttet->slettForslag($vakt->getId());
                        Funk::setSuccess("Vaktforslaget ble slettet fra dette byttet!");

                        $this->redirect();
                    }

                    break;
            }

            //Modal-seksjon
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {

            $dok = new Visning($this->cd);
            $sisteArg = $this->cd->getSisteArg();
            //Her vil requests se slik ut: ?a=vakt/bytte/<case>/<id>
            switch ($aktueltArg) {
                //Modal for egen vakt
                case 'modal_egen':
                    if(($vakt = Vakt::medId($sisteArg)) == null) {
                        exit("Denne vakten ser ikke ut til å eksistere!");
                    }

                    $vakt = Vakt::medId($sisteArg);
                    $dok->set('vakt', $vakt);
                    $dok->vis('vakt_bytte_liste_modal.php');
                    exit();

                case 'modal_slett':
                    if ((($vaktbyttet = Vaktbytte::medId($sisteArg)) != null &&
                        $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId()) ||
                        $vaktbyttet->getVakt() == null) {

                        exit("Du kan ikke slette dette vaktbyttet!");
                    }

                    $dok->set('vaktbyttet', $vaktbyttet);
                    $dok->vis('vakt_bytte_modal_slett.php');
                    exit();

                case 'modal_bytt':

                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) == null ||
                        $vaktbyttet->getVakt() == null ||
                        $bruker->getId() == $vaktbyttet->getVakt()->getId()){

                        exit("Du kan ikke bytte denne vakta!");
                    }

                    $egne_vakter = VaktListe::medBrukerIdEtter($bruker->getId(), date('Y-m-d'));

                    $dok->set('vaktbyttet', $vaktbyttet);
                    $dok->set('egne_vakter', $egne_vakter);
                    $dok->vis('vakt_bytte_modal_bytt.php');
                    exit();

                case 'modal_gibort':

                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) == null ||
                        $vaktbyttet->getVakt() == null ||
                        $bruker->getId() == $vaktbyttet->getVakt()->getBrukerId()){

                        exit("Du kan ikke ta denne vakta!");
                    }
                    $dok->set('vaktbyttet', $vaktbyttet);

                    $dok->vis('vakt_bytte_modal_gibort.php');
                    exit();

                case 'modal_forslag':

                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) == null ||
                        $vaktbyttet->getVakt() == null ||
                        $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId()) {

                        exit("Du kan ikke se forslag her!");
                    }
                    $dok->set('vaktbyttet', $vaktbyttet);

                    $dok->vis('vakt_bytte_modal_forslag.php');
                    exit();
            }

        }


        $egne_vakter = VaktListe::medBrukerId($bruker->getId());
        $vaktbytter = Vaktbytte::getAlleMulige();

        $dok = new Visning($this->cd);

        $dok->set('beboer', $bruker->getPerson());
        $dok->set('egne_vakter', $egne_vakter);
        $dok->set('vaktbytter', $vaktbytter);

        if($bruker->getPerson()->harVakt()) {
            $dok->vis('vakt_bytte_liste_ny.php');
        } else {
            $dok->vis('vakt_bytte_uten_vakt.php');
        }
        return;

    }
}