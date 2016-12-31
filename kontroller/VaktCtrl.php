<?php

namespace intern3;

class VaktCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $vaktbytteListe = VaktbytteListe::etterVakttype();
        if ($aktueltArg == 'bytte') {
            $dok = new Visning($this->cd);
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                //data: 'vaktbytte=1&id=' + id +'&action=' + action + "&passord=" + passord + "&passordet=" + passordet + "&merknad=" + merknad,
                //var action = 0; //1 = gisbort, 0 = byttes
                if (isset($post['vaktbytte']) && $post['vaktbytte'] == 1 && isset($post['action']) && isset($post['id']) && is_numeric($post['id'])) {
                    $action = $post['action'];
                    $id = $post['id'];
                    $merknad = $post['merknad'];
                    $passord = $post['passord'];
                    $passordet = $post['passordet'];

                    $vaktInstans = Vakt::medId($id);
                    if ($vaktInstans != null && ($vaktInstans->getBruker() == LogginnCtrl::getAktivBruker()) || in_array($action, array(0, 1, '0', '1'))) {
                        Vaktbytte::nyttVaktBytte($id, $action, $merknad, $passord, $passordet);
                        $st = DB::getDB()->prepare('UPDATE vakt SET bytte=1 WHERE id=:id');
                        $st->bindParam(':id', $id);
                        $st->execute();
                    }
                }
                //data: 'vaktbytte=2&id=' + id +'&vaktId=' + vaktId,
                elseif(isset($post['vaktbytte']) && $post['vaktbytte'] == 2 && isset($post['id']) && isset($post['vaktId'])){
                    $id = $post['id'];
                    $vaktId = $post['vaktId'];
                    $vaktInstans = Vakt::medId($vaktId);
                    if($vaktInstans != null && $vaktInstans->getBruker() == LogginnCtrl::getAktivBruker()){
                        Vaktbytte::slettEgetVaktBytte($id,$vaktId);
                        $st = DB::getDB()->prepare('UPDATE vakt SET bytte=0 WHERE id=:id');
                        $st->bindParam(':id', $vaktId);
                        $st->execute();

                    }
                }
                //data: 'vaktbytte=3&id=' + id + '&vaktId=' + vaktId,
                elseif(isset($post['vaktbytte']) && $post['vaktbytte'] == 3 && isset($post['id']) && isset($post['vaktId'])){
                    $id = $post['id'];
                    $vaktId = $post['vaktId'];
                    $vaktInstans = Vakt::medId($vaktId);
                    $vaktByttet = Vaktbytte::medId($id);
                    if($vaktInstans != null && $vaktInstans->getBytte() == 1 && $vaktByttet != null && $vaktByttet->getVaktId() == $vaktId){
                        $brukerId = LogginnCtrl::getAktivBruker()->getId();
                        Vaktbytte::taVakt($vaktId, $brukerId);
                        $st = DB::getDB()->prepare('DELETE FROM vaktbytte WHERE id=:id');
                        $st->bindParam(':id', $id);
                        $st->execute();
                    }
                }
            }
            $dok->set('vaktbytteListe', $vaktbytteListe);
            $dok->vis('vakt_bytte_liste.php');
        } else {
            $dok = new Visning($this->cd);
            $dok->set('denneUka', @date('W'));
            $dok->set('detteAret', @date('Y'));
            $dok->vis('vakt_vaktliste.php');
        }
    }
}

?>
