<?php

namespace intern3;

class UtvalgRegisjefArbeidCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        if (isset($_POST['id'])) {
            $this->godkjennArbeid($_POST['id'], @$_POST['underkjenn']);
        }
        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();

        /*if($sisteArg != $aktueltArg && ($arbeidet = Arbeid::medId($sisteArg)) != null){
            setcookie('faen','lol');
            //$this->underkjennArbeid($arbeidet, LogginnCtrl::getAktivBruker()->getId());
            $bid = LogginnCtrl::getAktivBruker()->getId();
            $st = DB::getDB()->prepare('UPDATE arbeid SET godkjent=-1,tid_godkjent=CURRENT_TIMESTAMP,godkjent_bruker_id=:bid WHERE id=:id');
            $st->bindParam(':id', $sisteArg);
            $st->bindParam(':bid', $bid);
            $st->execute();
            setcookie('hva','fæn');
        }*/

        $dok = new Visning($this->cd);
        switch ($aktueltArg) {
            case 'tilbakemelding':
                if(($arbeidet = Arbeid::medId($this->cd->getSisteArg())) != null){
                    if(isset($_POST) && count($_POST) > 0){
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if(isset($post['tilbakemelding'])){
                            $st = DB::getDB()->prepare('UPDATE arbeid SET tilbakemelding=:tb WHERE id=:id');
                            $st->bindParam(':tb', $post['tilbakemelding']);
                            $st->bindParam(':id', $arbeidet->getId());
                            $st->execute();

                            $_SESSION['success'] = 1;
                            $_SESSION['msg'] = "Du la til en tilbakemelding!";
                        } elseif(isset($post['underkjenn'])){
                            $bid = LogginnCtrl::getAktivBruker()->getId();
                            $st = DB::getDB()->prepare('UPDATE arbeid SET godkjent=-1,tid_godkjent=CURRENT_TIMESTAMP,godkjent_bruker_id=:bid WHERE id=:id');
                            $st->bindParam(':id', $sisteArg);
                            $st->bindParam(':bid', $bid);
                            $st->execute();
                            exit();
                        }
                    }
                    $dok->set('arbeidet', $arbeidet);
                    $dok->vis('utvalg_regisjef_arbeid_tilbakemelding.php');
                    break;
                }
            case 'endre':
                do {
                    if (!is_numeric($this->cd->getSisteArg())) {
                        break;
                    }
                    $arbeidId = $this->cd->getSisteArg();
                    $arbeidet = Arbeid::medId($arbeidId);
                    if ($arbeidet == null) {
                        break;
                    }
                    if(isset($_POST)){
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        if(isset($post['time']) && isset($post['endre'])){
                            $this->endreArbeid($post['endre'], $post['time']);
                            $dok->set('endret', 1);
                            break;
                        }
                    }
                    $dok->set('arbeidet', $arbeidet);
                    $dok->vis('utvalg_regisjef_arbeid_endre.php');
                    $done = 1;
                } while (false);
                if(isset($done)){
                    break;
                }
            default:
                $timer_brukt = Arbeid::getTimerBruktPerSemester();
                $sideinndeling = new SideinndelData();
                $sideinndeling->setPerSide(200);
                $sideinndeling->setSide($this->cd->getAktueltArg());
                $dok->set('arbeidListe', ArbeidListe::alle($sideinndeling));
                $dok->set('sideinndeling', $sideinndeling);
                $dok->set('timer_brukt', $timer_brukt);
                $dok->vis('utvalg_regisjef_arbeid.php');
                break;
        }
    }

    private function godkjennArbeid($id, $underkjenn = '')
    {
        $arbeid = Arbeid::medId($id);
        if ($arbeid == null) {
            http_response_code(404);
            exit('Arbeid ble ikke funnet.');
        }
        $godkjent = $underkjenn == '' || $underkjenn == '0' ? 1 : 0;
        $godkjentBrukerId = $this->cd->getAktivBruker()->getId();
        $st = DB::getDB()->prepare('UPDATE arbeid SET godkjent=:godkjent,godkjent_bruker_id=:godkjent_bruker_id,tid_godkjent=CURRENT_TIMESTAMP WHERE id=:id;');
        $st->bindParam(':godkjent', $godkjent);
        $st->bindParam(':godkjent_bruker_id', $godkjentBrukerId);
        $st->bindParam(':id', $id);
        $st->execute();
        $arbeid = Arbeid::medId($id);
        $dok = new Visning($this->cd);
        $dok->set('arbeid', $arbeid);
        $dok->vis('utvalg_regisjef_arbeid_rad.php');
        exit();
    }

    private function endreArbeid($id, $nyTid){
        $aktuelt_arbeid = Arbeid::medId($id);
        $tiden = $this->getSekunderBrukt($nyTid);
        $st = DB::getDB()->prepare('UPDATE arbeid SET sekunder_brukt=:sek_brukt WHERE id=:id');
        $st->bindParam(':id', $id);
        $st->bindParam(':sek_brukt', $tiden);
        $st->execute();
        return;
    }

    private function getSekunderBrukt($param)
    {
        if (preg_match('/^([0-9]+)$/', $param, $treff)) {
            return $treff[1] * 3600;
        }
        if (preg_match('/^([0-9]+)(\:([0-9]{2}))?$/', $param, $treff)) {
            return $treff[1] * 3600 + $treff[3] * 60;
        }
        if (preg_match('/^[0-9]+(\,[0-9]+)?$/', $param)) {
            return str_replace(',', '.', $param) * 3600;
        }
        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $param)) {
            return $param * 3600;
        }
        return 0;
    }

    private function underkjennArbeid(Arbeid $arbeid, $bruker_id){
        $st = DB::getDB()->prepare('UPDATE arbeid SET godkjent=-1,tid_godkjent=CURRENT_TIMESTAMP,godkjent_bruker_id=:bid WHERE id=:id');
        $st->bindParam(':id', $arbeid->getId());
        $st->bindParam(':bid', $bruker_id);
        $st->execute();
    }

}

?>
