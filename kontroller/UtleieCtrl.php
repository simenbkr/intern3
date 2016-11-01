<?php

namespace intern3;

class UtleieCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $dok = new Visning($this->cd);
        $denne_beboeren = LogginnCtrl::getAktivBruker()->getPerson();
        if (isset($_POST)) {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (isset($post['meldpa']) && isset($post['utleieid']) &&
                is_numeric($post['utleieid']) && isset($post['felt']) && is_numeric($post['felt'])
            ) {
                $utleiet = Utleie::medId($post['utleieid']);
                if ($denne_beboeren == null || $utleiet == null) {
                    exit();
                }
                switch ($post['felt']) {
                    case 1:
                        if ($utleiet->barvakt1Ledig()) {
                            $utleiet->setBeboer1($denne_beboeren);
                            $dok->set('success', 1);
                        }
                        break;
                    case 2:
                        if ($utleiet->barvakt2Ledig()) {
                            $utleiet->setBeboer2($denne_beboeren);
                            $dok->set('success', 1);
                        }
                        break;
                    case 3:
                        if ($utleiet->vaskevaktLedig()) {
                            $utleiet->setBeboer3($denne_beboeren);
                            $dok->set('success', 1);
                        }
                        break;
                }
            } elseif (isset($post['meldpa']) && $post['meldpa'] == 0 && is_numeric($post['utleieid'])) {
                $utleiet = Utleie::medId($post['utleieid']);
                if($utleiet == null){
                    exit();
                }
                if ($utleiet->getBeboer1() != null && $utleiet->getBeboer1()->getId() == $denne_beboeren->getId()) {;
                    $utleiet->setBeboer1(null);
                    $dok->set('avmeldt', 1);
                } elseif ($utleiet->getBeboer2() != null && $utleiet->getBeboer2()->getId() == $denne_beboeren->getId()) {
                    $utleiet->setBeboer2(null);
                    $dok->set('avmeldt', 1);
                } elseif ($utleiet->getBeboer3() != null && $utleiet->getBeboer3()->getId() == $denne_beboeren->getId()) {
                    $utleiet->setBeboer3(null);
                    $dok->set('avmeldt', 1);
                }
            }
        }
        $utleier = Utleie::getUtleierFremover();
        $dok->set('aktuell_beboer', $denne_beboeren);
        $dok->set('utleier', $utleier);
        $dok->vis('utleie_liste.php');
    }
}
?>
