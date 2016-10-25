<?php

namespace intern3;

class HelgaCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();

        switch ($aktueltArg) {
            case 'general':
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if(isset($post['start']) && isset($post['aar'])){
                        $st = DB::getDB()->prepare('UPDATE helga SET start_dato=:start, tema=:tema WHERE aar=:aar');
                        $st->bindParam(':start', $post['start']);
                        $st->bindParam(':tema', $post['tema']);
                        $st->bindParam(':aar', $post['aar']);
                        $st->execute();
                    }
                }
                $denne_helga = Helga::getLatestHelga();
                $alle_helga = Helga::getAlleHelga();
                $dok = new Visning($this->cd);
                $dok->set('alle_helga', $alle_helga);
                $dok->set('helga', $denne_helga);
                $dok->vis('helga_general.php');
                break;
            case 'helga':
            default:
                $dok = new Visning($this->cd);
                $dok->vis('helga.php');
                exit();
        }
    }
}

?>
