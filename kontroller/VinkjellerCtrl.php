<?php

namespace intern3;


class VinkjellerCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {

        $aktivBruker = LogginnCtrl::getAktivBruker();

        if ($aktivBruker != null &&
            (
                $aktivBruker->getPerson()->erKjellerMester() ||
                $aktivBruker->getPerson()->harUtvalgVerv())
        ) {
            //Time to set token yo
            session_destroy();
            session_start();
            $token = Token::createToken('vinkjeller', 15768000);
            $_SESSION['token'] = $token->getToken();
            Funk::setSuccess("Du har blitt logget ut av egen bruker, og inn på vinkjelleren.");
        } elseif (
            !isset($_SESSION['token'])
            || ($token = Token::byToken($_SESSION['token'])) == null
            || !$token->isValidToken('vinkjeller')
        ) {
            session_destroy();
            header('Location: ?a=diverse');
            exit();
        }


        $aktueltarg = $this->cd->getAktueltArg();

        $dok = new Visning($this->cd);
        $dok->set('skjulMeny', 1);
        switch ($aktueltarg) {
            case 'pinkode':
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    $ansvarlig = Beboer::medId($post['ansvarlig']);
                    $resten = array();
                    foreach (json_decode($post['beboerene']) as $id) {
                        $resten[] = Beboer::medId($id);
                    }

                    $this->pinkode($ansvarlig, $resten, $post['vinid']);
                    break;
                }
            case 'kryssing':
                if (($sisteArg = $this->cd->getSisteArg()) != $aktueltarg) {
                    $this->handleKryssing($dok);
                    break;
                }
                $dok->vis('vinkjeller_kryssing.php');
                break;

            case 'kryss_vin':
                $this->handleKryss($dok);
                break;
            case 'regler':
                $dok->vis('vinkjeller_regler.php');
                break;
            case '':
            default:
                $dok->vis('vinkjeller_hoved.php');
                break;
        }

    }

    private function handleKryssing(Visning $dok)
    {
        /*
         * Hva om..
         * URL-bygges slik: vinkjeller/kryssing/[navn|type]/<vin_id>
         * Også hukes de som ønsker å krysse seg av på denne siden,
         * Dette postes til f.eks vinkjeller/kryss og føres inn i databasen.
         */
        $alleArgs = $this->cd->getAllArgs();
        $lastArg = end($alleArgs);

        if (count($alleArgs) < 1) {
            /* @var $dok \intern3\Visning */
            $dok->vis('vinkjeller_kryssing.php');
            return;
        }

        switch ($lastArg) {
            case 'navn':
                $vinListe = Vin::getAktiveOrderedByNavn();
                $dok->set('vinListe', $vinListe);
                $dok->vis('vinkjeller_kryssing_navn.php');
                return;
            case 'type':
                $typeListe = Vintype::getAlle();
                $dok->set('typeListe', $typeListe);
                $dok->vis('vinkjeller_kryssing_type.php');
                return;
            case 'land':
                $land = Vin::getAlleLand();
                $dok->set('land', $land);
                $dok->vis('vinkjeller_kryssing_land.php');
                return;
            case 'kryss':
                $this->handleKryss($dok);
                return;
            case '':
                header('Location:' . rtrim($_SERVER['REQUEST_URI'], '/'));
                exit();
        }

        if (in_array('type', $alleArgs)) {
            /* Type er i argumentene, men er IKKE siste argument. */
            if (($typen = Vintype::medId($lastArg)) != null) {
                $vinListe = Vin::getAktiveAvType($typen);
                $dok->set('vinListe', $vinListe);
                $dok->vis('vinkjeller_kryssing_navn.php');
                return;
            } else {
                $dok->vis('vinkjeller_hoved.php');
                return;
            }
        } elseif (in_array('land', $alleArgs) && in_array($lastArg, Vin::getAlleLand())) {
            $vinListe = Vin::getByLand($lastArg);
            $dok->set('vinListe', $vinListe);
            $dok->vis('vinkjeller_kryssing_navn.php');
            return;
        }


        if (count($this->cd->getAllArgs()) < 4) {
            $aktuell_vin = Vin::medId($lastArg);
            /* Forventet URL: vinkjeller/kryssing/<id> */
            $beboerListe = BeboerListe::vinkjellerListe();
            $dok->set('beboerListe', $beboerListe);
            $dok->set('vinen', $aktuell_vin);
            $dok->vis('vinkjeller_kryssing_vinen.php');
            return;
        } else {
            /* ?a=vinkjeller/kryssing/<vinID>/<beboerID-varargs> */
            $args = $this->cd->getAllArgs();

            $ansvarlig_beboer = Beboer::medId($args[3]);
            $beboerene = [$ansvarlig_beboer];
            for ($i = 4; $i < count($args); $i++) {
                $beboerene[] = Beboer::medId($args[$i]);
            }

            if (!isset($_SESSION[md5($ansvarlig_beboer->getFulltNavn())])) {
                $this->pinkode($ansvarlig_beboer, $beboerene, $args[2]);
                exit();
            }

            $back = '?a=vinkjeller/kryssing/' . $args[2];
            $aktuell_vin = Vin::medId($args[2]);
            $dok->set('back', $back);
            $dok->set('beboerene', $beboerene);
            $dok->set('vinen', $aktuell_vin);
            $dok->vis('vinkjeller_kryss.php');
            exit();
        }

    }

    private function pinkode(Beboer $ansvarlig_beboer, $beboerene, $vinid)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            if ($post['pinkode'] == $ansvarlig_beboer->getPrefs()->getVinPinkode()) {
                $_SESSION[md5($ansvarlig_beboer->getFulltNavn())] = 1;
                $loc = "?a=vinkjeller/kryssing/$vinid/";
                foreach ($beboerene as $beboer) {
                    $loc .= $beboer->getId() . '/';
                    $loc = rtrim($loc, '/');
                }
                header('Location: ' . $loc);
                exit();
            }
        }

        $dok = new Visning($this->cd);
        $dok->set('beboer', $ansvarlig_beboer);
        $idene = array();
        foreach ($beboerene as $beboer) {
            $idene[] = $beboer->getId();
        }
        $dok->set('vinid', $vinid);
        $dok->set('idene', json_encode($idene));
        $dok->vis('vinkjeller_pinkode.php');
        exit();
    }

    private function isInt($num)
    {
        return $num - floor($num) == 0;
    }


    private function handleKryss($dok)
    {

        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            //AKA fuck off
            $dok->vis('vinkjeller_hoved.php');
            return;
        }
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $antall = $post['antall'];

        $beboerIDs = explode(',', $post['beboerId']);
        $ansvarlig = $beboerIDs[0];
        $vinId = $post['vinid'];

        //Dette gjøres fordi JavaScript er å anse som svart magi. Det fungerer kun basert på empiri.
        $fordeling = array();
        foreach (explode(',', $post['fordeling']) as $key => $val) {
            //Pls kill me now.
            if ($key && $val) {
                $fordeling[$key] = $val;
            }
        }

        $beboerene = array();
        foreach ($beboerIDs as $id) {
            //Gotta do them docs yo.
            /* @var Beboer $beboer */
            if (($beboer = Beboer::medId($id)) == null) {
                exit();
            }
            $beboerene[] = $beboer;
        }

        /*if($antall < 1 || $antall > $vinen->getAntall() || !is_int($antall)) {
            setcookie('woopi',"doopi");
            exit();
        }*/

        if (($vinen = Vin::medId($vinId)) == null) {
            Funk::setError("Denne vinen eksisterer ikke!");
            exit();
        }
        if ($antall < 1) {
            Funk::setError("Du må krysse minst én vin!");
            exit();
        }
        if ($antall > $vinen->getAntall()) {
            Funk::setError("Du kan ikke krysse flere vin enn det er igjen!");
            exit();
        }
        if (!$this->isInt($antall)) {
            Funk::setError("Du kan bare krysse heltalls antall viner!");
            exit();
        }

        foreach ($post as $key => $val) {
            setcookie($key, $val);
        }

        //Akseptabel avstand til sannheten. 0.000001 for den dyreste vinen tilsvarer 0.0003kr.
        $delta = 0.00000001;
        $sum = array_sum($fordeling);
        $sum_analytisk = 100.0;

        if (abs($sum - $sum_analytisk) > $delta) {
            Funk::setError("Dere må betale 100%!");
            exit();
        }


        //Ait, we gucci.
        $msg = "Du krysset " . $antall . "stk " . $vinen->getNavn() . " til " . $vinen->getPris() * $vinen->getAvanse()
            . "kr per stk på ";
        foreach ($beboerene as $beboer) {

            if (count($beboerene) == 1) {
                $antallet = $antall;
            } else {
                $antallet = round($fordeling[$beboer->getId()] / 100 * $antall, 3);
            }
            $pris = $antallet * $vinen->getPris() * $vinen->getAvanse();

            $st = DB::getDB()->prepare('INSERT INTO vinkryss (antall, tiden, fakturert, vinId, beboerId, prisen, ansvarlig)
                                       VALUES(               :antall, NOW(), 0, :vinId, :beboerId, :prisen, :ansvarlig)');

            $st->bindParam(':antall', $antallet);
            $st->bindParam(':prisen', $pris);
            $st->bindParam(':vinId', $vinen->getId());
            $st->bindParam(':beboerId', $beboer->getId());
            $st->bindParam(':ansvarlig', $ansvarlig);

            $st->execute();

            $msg .= $beboer->getFulltNavn() . ', ';
        }

        $st = DB::getDB()->prepare('UPDATE vin SET antall=:antall WHERE id=:id');
        $nytt_antall = $vinen->getAntall() - $antall;
        $st->bindParam(':antall', $nytt_antall);
        $st->bindParam(':id', $vinen->getId());
        $st->execute();

        $_SESSION['success'] = 1;
        $_SESSION['msg'] = rtrim($msg, ', ') . '. Drikk (u)ansvarlig!';

        unset($_SESSION[md5(Beboer::medId($ansvarlig)->getFulltNavn())]);


    }


}