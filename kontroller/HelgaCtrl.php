<?php

namespace intern3;

class HelgaCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();

        switch ($aktueltArg) {
            case 'general':
                $dok = new Visning($this->cd);
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if(isset($post['start']) && isset($post['aar'])){
                        $klar = $post['klar'] == 'on' ? 1 : 0;
                        $st = DB::getDB()->prepare('UPDATE helga SET start_dato=:start, tema=:tema, klar=:klar WHERE aar=:aar');
                        $st->bindParam(':start', $post['start']);
                        $st->bindParam(':tema', $post['tema']);
                        $st->bindParam(':aar', $post['aar']);
                        $st->bindParam(':klar', $klar);
                        $st->execute();
                        $dok->set('oppdatert', 1);
                    }
                }
                $denne_helga = Helga::getLatestHelga();
                $alle_helga = Helga::getAlleHelga();

                $dok->set('alle_helga', $alle_helga);
                $dok->set('helga', $denne_helga);
                $dok->vis('helga_general.php');
                break;
            case 'helga':
            default:
                $beboer_id = LogginnCtrl::getAktivBruker()->getPerson()->getId();
                $aar = date('Y');
                $beboers_gjester = HelgaGjesteListe::getGjesteListeByBeboerAar($beboer_id, $aar);
                $gjeste_count = HelgaGjesteListe::getGjesteCountBeboer($beboer_id, $aar);
                $dok = new Visning($this->cd);
                $dok->set('beboers_gjester', $beboers_gjester);
                $dok->set('gjeste_count', $gjeste_count);
                $dok->vis('helga.php');
                exit();
        }
    }
}

?>
