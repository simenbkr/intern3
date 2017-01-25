<?php
namespace intern3;


class KjellerCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $vintypene = Vintype::getAlle();
        $vinene = Vin::getAlle();
        $tillatte_filtyper = array('jpg', 'jpeg', 'png', 'gif');
        $dok = new Visning($this->cd);
        switch ($aktueltArg) {
            case 'leggtil':
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (isset($_FILES['image']) && isset($post['navn']) && isset($post['pris']) && isset($post['antall'])
                        && isset($post['type']) && is_numeric($post['pris']) && is_numeric($post['antall'])
                    ) {

                        $file_name = $_FILES['image']['name'];
                        $file_size = $_FILES['image']['size'];
                        $tmp_file = $_FILES['image']['tmp_name'];
                        $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

                        if (in_array($file_ext, $tillatte_filtyper) && $file_size > 10 && $file_size < 1000000000) {
                            $bildets_navn = md5($file_name . "spisostdindostogfuckoff" . time()) . '.' . $file_ext;
                            move_uploaded_file($tmp_file, "vinbilder/" . $bildets_navn);
                            chmod("vinbilder/" . $bildets_navn, 644);
                            $this->insertVin(true, $bildets_navn);
                        }
                    } elseif (isset($post['navn']) && isset($post['pris']) && isset($post['antall'])
                        && isset($post['type']) && is_numeric($post['pris']) && is_numeric($post['antall'])
                    ) {
                        $this->insertVin(false, null);
                    } elseif (sizeof($post) > 0) {
                        $dok->set('error', 1);
                        $vintypene = Vintype::getAlle();
                        $dok->vis('kjeller_add.php');
                    }
                }

                $dok->set('vintyper', $vintypene);
                $dok->vis('kjeller_add.php');
                break;
            case 'admin':

                $sisteArg = $this->cd->getSisteArg();
                if ($sisteArg != 'admin' && is_numeric($sisteArg)) {
                    $vinen = Vin::medId($sisteArg);
                    if (isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        foreach($post as $key=>$val){setcookie($key,$val);}
                        if (isset($_FILES['image']) && $_FILES['image']['size'] > 100 && isset($post['navn']) && isset($post['pris']) && isset($post['antall'])
                            && isset($post['type']) && is_numeric($post['pris']) && is_numeric($post['antall'])
                        ) {
                            $file_name = $_FILES['image']['name'];
                            $file_size = $_FILES['image']['size'];
                            $tmp_file = $_FILES['image']['tmp_name'];
                            $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

                            if (in_array($file_ext, $tillatte_filtyper) && $file_size > 10 && $file_size < 1000000000) {
                                $bildets_navn = md5($file_name . "spisostdindostogfuckoff" . time()) . '.' . $file_ext;
                                move_uploaded_file($tmp_file, "vinbilder/" . $bildets_navn);
                                chmod("vinbilder/" . $bildets_navn, 644);
                                $this->updateVin(true, $bildets_navn, $vinen->getId());
                            }

                        } elseif (isset($post['navn']) && isset($post['pris']) && isset($post['antall'])
                            && isset($post['type']) && is_numeric($post['pris']) && is_numeric($post['antall'])
                        ) {
                            $this->updateVin(true, $vinen->getBilde(), $vinen->getId());
                        }

                    }
                    if ($vinen != null) {
                        $dok->set('vinen', $vinen);
                        $dok->set('vintyper', $vintypene);
                        $dok->vis('kjeller_endre_vin.php');
                        exit();
                    }
                }
                $vinene = Vin::getAlle();
                $dok->set('vinene', $vinene);
                $dok->vis('kjeller_admin.php');
                break;
            case 'regning':
                if(isset($_POST)){
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if(isset($post['beboer']) && is_numeric($post['beboer']) && isset($post['vin'])
                        && isset($post['antall']) && isset($post['dato'])){

                        $beboeren = Beboer::medId($post['beboer']);
                        $vinen = Vin::medId($post['vin']);

                        if($vinen != null && $beboeren != null){
                            $st = DB::getDB()->prepare('INSERT INTO vinkryss (antall,tiden,fakturert,vinId,beboerId) VALUES(
                            :antall,:tiden,0,:vinId,:beboerId)');
                            $st->bindParam(':antall', $post['antall']);
                            $st->bindParam(':tiden', $post['dato']);
                            $st->bindParam(':vinId', $post['vin']);
                            $st->bindParam(':beboerId', $post['beboer']);
                            $st->execute();
                        }
                    }
                }

                $beboerlista = BeboerListe::aktive();
                $dok->set('vinene', $vinene);
                $dok->set('beboerlista', $beboerlista);
                $dok->vis('kjeller_regning.php');
                break;
            case 'lister':
                $sisteArg = $this->cd->getSisteArg();
                if($sisteArg != 'lister'){
                    switch($sisteArg){
                        case 'varebeholdning_utskrift':
                            $dok->set('vinene', $vinene);
                            $dok->vis('kjeller_lister_varebeholdning_utskrift.php');
                            exit();
                        case 'rapport':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach($beboerlista as $beboer){
                                $beboer_vin = array('beboer' => $beboer,
                                    'antall' => 0,
                                    'kostnad' => 0);
                                $alle_kryss = Vinkryss::getAlleIkkeFakturertByBeboerId($beboer->getId());
                                foreach($alle_kryss as $krysset){
                                    $beboer_vin['antall'] += round($krysset->getAntall(),2);
                                    $beboer_vin['kostnad'] += round($krysset->getKostnad(),2);
                                }
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_antall_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_lister_rapport.php');
                            exit();
                        case 'beboere_vin':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach($beboerlista as $beboer){
                                $ikke_fakturert = Vinkryss::getAlleIkkeFakturertByBeboerId($beboer->getId());
                                if(count($ikke_fakturert) < 1){
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach($ikke_fakturert as $vin_kryss){
                                    if(!in_array($vin_kryss->getVinId(), $vin_array)){
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad(),2),
                                            'antall' => round($vin_kryss->getAntall(),2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad(),2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(),2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin.php');
                            exit();
                        case 'beboere_vin_utskrift':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach($beboerlista as $beboer){
                                $ikke_fakturert = Vinkryss::getAlleIkkeFakturertByBeboerId($beboer->getId());
                                if(count($ikke_fakturert) < 1){
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach($ikke_fakturert as $vin_kryss){
                                    if(!in_array($vin_kryss->getVinId(), $vin_array)){
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad(),2),
                                            'antall' => round($vin_kryss->getAntall(),2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad(),2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(),2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin_utskrift.php');
                            exit();
                        case 'beboere_vin_fakturerte':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach($beboerlista as $beboer){
                                $ikke_fakturert = Vinkryss::getAlleFakturertByBeboerId($beboer->getId());
                                if(count($ikke_fakturert) < 1){
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach($ikke_fakturert as $vin_kryss){
                                    if(!in_array($vin_kryss->getVinId(), $vin_array)){
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad(),2),
                                            'antall' => round($vin_kryss->getAntall(),2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad(),2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(),2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin_fakturert.php');
                            exit();
                        case 'beboere_vin_utskrift_fakturerte':
                            $beboerlista = BeboerListe::aktive();
                            $beboer_antall_vin = array();
                            foreach($beboerlista as $beboer){
                                $ikke_fakturert = Vinkryss::getAlleFakturertByBeboerId($beboer->getId());
                                if(count($ikke_fakturert) < 1){
                                    continue;
                                }
                                $beboer_vin = array('beboer' => $beboer,
                                    'vin' => '');
                                $vin_array = array();
                                foreach($ikke_fakturert as $vin_kryss){
                                    if(!in_array($vin_kryss->getVinId(), $vin_array)){
                                        $vin_array[$vin_kryss->getVinId()] = array('kostnad' => round($vin_kryss->getKostnad(),2),
                                            'antall' => round($vin_kryss->getAntall(),2),
                                            'aktuell_vin' => $vin_kryss->getVin());
                                    } else {
                                        $vin_array[$vin_kryss->getVinId()]['kostnad'] += round($vin_kryss->getKostnad(),2);
                                        $vin_array[$vin_kryss->getVinId()]['antall'] += round($vin_kryss->getAntall(),2);
                                    }
                                }
                                $beboer_vin['vin'] = $vin_array;
                                $beboer_antall_vin[] = $beboer_vin;
                            }
                            $dok->set('beboer_vin', $beboer_antall_vin);
                            $dok->vis('kjeller_beboere_vin_fakturert_utskrift.php');
                            exit();
                    }
                }
                $dok->vis('kjeller_lister.php');
                break;
            default:
                $dok->vis('kjeller_hoved.php');
        }
    }
    private function insertVin($medbilde = false, $bildenavn)
    {
        $bildet = $medbilde ? $bildenavn : '';
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $st = DB::getDB()->prepare('INSERT INTO vin (navn,bilde,pris,antall,typeId) VALUES(
:navn,:bilde,:pris,:antall,:typeId)');

        $st->bindParam(':navn', $post['navn']);
        $st->bindParam(':bilde', $bildet);
        $st->bindParam(':pris', $post['pris']);
        $st->bindParam(':antall', $post['antall']);
        $st->bindParam(':typeId', $post['type']);
        $st->execute();
    }

    private function updateVin($medbilde = false, $bildenavn, $id){
        $bildet = $medbilde ? $bildenavn : '';
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $st = DB::getDB()->prepare('UPDATE vin SET navn=:navn,bilde=:bilde,pris=:pris,antall=:antall,typeId=:typeId WHERE id=:id');

        $st->bindParam(':id', $id);
        $st->bindParam(':navn', $post['navn']);
        $st->bindParam(':bilde', $bildet);
        $st->bindParam(':pris', $post['pris']);
        $st->bindParam(':antall', $post['antall']);
        $st->bindParam(':typeId', $post['type']);
        $st->execute();
    }

}
?>