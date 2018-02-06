<?php

namespace intern3;


class VaktbytteCtrl extends AbstraktCtrl
{
    
    public function bestemHandling()
    {
        
        $aktueltArg = $this->cd->getAktueltArg();
        $bruker = LogginnCtrl::getAktivBruker();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            switch ($aktueltArg) {
                
                //Slette eget vaktbytte
                case 'slett':
                    
                    break;
                
                //Legge til eget vaktbytte
                case 'leggtil':
                    //byttes=>[byttes|gisbort], passord=>[yes|no], passordtekst => passordtekst, merknad => merknad
                    
                    $sisteArg = $this->cd->getSisteArg();
                    if(!(($vakt = Vakt::medId($sisteArg)) != null || $vakt->getBrukerId() === $bruker->getId())){
                        Funk::setError("Noe gikk galt der, beklager!");
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
                    break;
                
                //Ta vakt som er gitt bort, med og uten passord
                case 'ta':
                    
                    break;
                
                //Legge til forslag på vaktbytte
                case 'forslag':
                    
                    break;
                
                //Godta bytteforslag
                case 'bytte':
                    
                    break;
                
                //Fjerne bytteforslag
                case 'slettbytte':
                    
                    break;
            }
        } elseif($_SERVER['REQUEST_METHOD'] == 'GET'){
            //Her vil requests se slik ut: ?a=vakt/bytte/<case>/<id>
            switch ($aktueltArg) {
                //Modal for egen vakt
                case 'modal_egen':
                    $sisteArg = $this->cd->getSisteArg();
                    $vakt = Vakt::medId($sisteArg);
                    $dok = new Visning($this->cd);
                    $dok->set('vakt', $vakt);
                    $dok->vis('vakt_bytte_liste_modal.php');
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