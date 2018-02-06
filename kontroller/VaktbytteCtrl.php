<?php

namespace intern3;


class VaktbytteCtrl extends AbstraktCtrl
{

    private function redirect(){
        header('Location: ?a=vakt/bytte');
        exit();
    }
    
    public function bestemHandling()
    {
        
        $aktueltArg = $this->cd->getAktueltArg();
        $bruker = LogginnCtrl::getAktivBruker();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $sisteArg = $this->cd->getSisteArg();
            switch ($aktueltArg) {
                
                //Slette eget vaktbytte
                case 'slett':

                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) != null && $bruker->getId() == $vaktbyttet->getVakt()->getBrukerId()){
                        $vaktbyttet->slett();
                        Funk::setSuccess("Vaktbyttet ditt ble slettet!");
                    } else {
                        Funk::setError("Noe galt skjedde. Vaktbyttet ble ikke sletta.");
                    }

                    $this->redirect();

                //Legge til eget vaktbytte
                case 'leggtil':
                    //byttes=>[byttes|gisbort], passord=>[yes|no], passordtekst => passordtekst, merknad => merknad
                    
                    $sisteArg = $this->cd->getSisteArg();
                    if(!(($vakt = Vakt::medId($sisteArg)) != null || $vakt->getBrukerId() === $bruker->getId())){
                        Funk::setError("Denne vakten kan ikke byttes!");
                        break;
                    }
                    
                    if(Vaktbytte::medVaktId($sisteArg) != null){
                        Funk::setError("Denne vakten er allerede på byttemarkedet!");
                        break;
                    }
                    
                    $gisbort = $post['byttes'] == 'byttes' ? 0 : 1;
                    $harpw = $post['passord'] == 'yes' ? 1 : 0;
                    Vaktbytte::nyttVaktBytte($sisteArg, $gisbort, $post['merknad'], $harpw, $post['passordtekst']);
                    
                    Funk::setSuccess("Vaktbyttet ble oppretta for din vakt " . $vakt->toString());

                    $this->redirect();
                
                //Ta vakt som er gitt bort, med og uten passord
                case 'ta':
                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) != null &&
                        $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId()
                        && $vaktbyttet->getGisBort()){

                        $vakta = $vaktbyttet->getVakt();
                        $vakta->fjernFraAlleBytter();

                        $vakta->setBruker($bruker->getId());
                        $vaktbyttet->slett();

                        Funk::setSuccess("Du tok vakta " . $vakta->toString());
                        $this->redirect();
                    } else {
                        Funk::setError("Noe gikk galt. Du fikk ikke tatt vakta!");
                        $this->redirect();
                    }


                    break;

                //Legge til forslag på vaktbytte
                case 'forslag':
                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) == null || ($forslag_vakt = Vakt::medId($post['forslag'])) == null){
                        Funk::setError("Det ser ut til at dette vaktbyttet eller den foreslåtte vakta ikke eksisterer!");
                        $this->redirect();
                    }

                    if($vaktbyttet->harPassord() && !$vaktbyttet->stemmerPassord($post['passord'])){
                        Funk::setError("Feil passord!");
                        $this->redirect();
                    } else {
                        $vaktbyttet->leggTilForslag($forslag_vakt->getId());
                        Funk::setSuccess("Vakta " . $forslag_vakt->toString() . " ble lagt til som forslag!");
                        $this->redirect();
                    }

                    break;
                //Godta bytteforslag
                case 'bytte':
                    
                    break;
                
                //Fjerne bytteforslag
                case 'slettbytte':
                    
                    break;
            }

        } elseif($_SERVER['REQUEST_METHOD'] == 'GET'){

            $dok = new Visning($this->cd);
            $sisteArg = $this->cd->getSisteArg();
            //Her vil requests se slik ut: ?a=vakt/bytte/<case>/<id>
            switch ($aktueltArg) {
                //Modal for egen vakt
                case 'modal_egen':

                    $vakt = Vakt::medId($sisteArg);
                    $dok->set('vakt', $vakt);
                    $dok->vis('vakt_bytte_liste_modal.php');
                    exit();

                case 'modal_slett':
                    if(($vaktbyttet = Vaktbytte::medId($sisteArg)) != null && $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId()){
                        Funk::setError("Det skjedde noe galt!");
                        header('Location: ?a=vakt/bytte');
                        exit();
                    }
                    $dok->set('vaktbyttet', $vaktbyttet);
                    $dok->set('slett', 1);
                    $dok->vis('vakt_bytte_modal_slett.php');
                    exit();

                case 'modal_bytt':
                    if(!(($vaktbyttet = Vaktbytte::medId($sisteArg)) == null || $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId())){
                        Funk::setError("Det skjedde noe galt!");
                        header('Location: ?a=vakt/bytte');
                        exit();
                    }
                    $dok->set('vaktbyttet', $vaktbyttet);
                    $dok->set('bytt', 1);

                    $dok->vis('vakt_bytte_modal_bytt.php');
                    exit();

                case 'modal_gibort':
                    if(!(($vaktbyttet = Vaktbytte::medId($sisteArg)) == null || $bruker->getId() != $vaktbyttet->getVakt()->getBrukerId())){
                        Funk::setError("Det skjedde noe galt!");
                        header('Location: ?a=vakt/bytte');
                        exit();
                    }
                    $dok->set('vaktbyttet', $vaktbyttet);
                    $dok->set('gibort', 1);

                    $dok->vis('vakt_bytte_modal_gibort.php');
                    exit();
            }
            
        }
        
        
        $egne_vakter = VaktListe::medBrukerId($bruker->getId());
        $vaktbytter = Vaktbytte::getAlleMulige();
        
        $dok = new Visning($this->cd);
        
        $dok->set('beboer', $bruker->getPerson());
        $dok->set('egne_vakter', $egne_vakter);
        $dok->set('vaktbytter', $vaktbytter);
        
        $dok->vis('vakt_bytte_liste_ny.php');
        return;
        
        
    }
}