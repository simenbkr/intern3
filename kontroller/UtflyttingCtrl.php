<?php

namespace intern3;

class UtflyttingCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $dok = new Visning($this->cd);
        $bruker = LogginnCtrl::getAktivBruker();
        if(isset($_POST)) {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if(isset($post['tekst']) && isset($post['passord']) && $bruker->passordErGyldig(LogginnCtrl::genererHash($post['passord'], LogginnCtrl::getAktivBruker()->getId()))){
                $beboer = Beboer::medBrukerId(LogginnCtrl::getAktivBruker()->getId());
                $fulltNavn = $beboer->getFulltNavn();
                $teksten = "<html>" . $post['tekst'] . "<br/><br/>Dette er en automatisert melding. Vennligst ta kontakt med data@singsaker.no dersom denne ble sendt feil.</html>";
                $mottaker = "romsjef@singsaker.no";
                $tittel = "[SING-INTERN] Melding om utflytting fra " . $fulltNavn;
                Epost::sendEpost($mottaker,$tittel,$teksten);
                $dok->set('success', 1);
            }
            else {
                if(isset($post)){
                    $dok->set("error",1);
                }
            }
        }
        $dok->vis('utflytting.php');
    }
}
?>
