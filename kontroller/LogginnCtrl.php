<?php

namespace intern3;

class LogginnCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        if ($this->cd->getAktueltArg() == 'loggut') {
            $this->loggUt();
        } /*else if ($this->cd->getAktueltArg() == 'passord') {
            $this->glemtPassord();
            exit();
        } */
        else if($this->cd->getAktueltArg() == 'passord'){

            if('passord' == $this->cd->getSisteArg()) {

                $this->glemtPassord();
                exit();
            } else {
                $this->resettPassord();
            }
        }
        else if (isset($_POST['brukernavn']) && isset($_POST['passord'])) {
            $this->loggInn();
        }
        $this->visSkjema();
    }

    private static function loggUt()
    {
        header('Location: ' . $_GET['ref']);
        session_destroy();
        exit();
    }

    private static function loggInn()
    {
        //setcookie('brukernavn', $_POST['brukernavn'], $_SERVER['REQUEST_TIME'] + 31556926, NULL, NULL, NULL, TRUE);
        //setcookie('passord', self::genererHash($_POST['passord']), $_SERVER['REQUEST_TIME'] + 31556926, NULL, NULL, NULL, TRUE);
        session_destroy();
        session_start();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $brukeren = Bruker::medEpost($post['brukernavn']);
        if ($brukeren != null) {
            $_SESSION['brukernavn'] = $post['brukernavn'];
            $_SESSION['passord'] = self::genererHash($post['passord'], $brukeren->getId());
        }
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

    private function visSkjema()
    {
        $dok = new Visning($this->cd);
        $dok->set('skjulMeny', 1);
        $dok->vis('logginn.php');
    }

    /*
    private function glemtPassord()
    {ALTER TABLE `bruker` ADD `glemt_token` VARCHAR(512) NOT NULL AFTER `salt`;"
        $dok = new Visning($this->cd);
        if (isset($_POST) && isset($_POST['brukernavn'])) {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $epost = $post['brukernavn'];
            $aktuellBruker = Bruker::medEpost($epost);

            if ($aktuellBruker != null) {
                $bruker_id = $aktuellBruker->getId();
                $nyttPassord = Funk::generatePassword();
                $hash = self::genererHash($nyttPassord, $bruker_id);

                $st = DB::getDB()->prepare('UPDATE bruker SET passord=:passord WHERE id=:id');
                $st->bindParam(':passord', $hash);
                $st->bindParam(':id', $bruker_id);
                $st->execute();

                $beskjed =
                    "<html><body>Hei<br/><br/>Du, eller noen som later som de er deg har forsøkt å resette ditt passord på 
<a href='https://intern.singsaker.no'>Internsidene</a><br/><br/>Ditt nye passord er : $nyttPassord<br/>
Vi anbefaler deg om å logge inn og bytte passord så fort som mulig. Hvis du lurer på noe, ta kontakt med oss på epost: 
<a href='mailto:data@singsaker.no'>data@singsaker.no</a> eller ta turen innom.<br/><br/>Med vennlig hilsen,
<br/>Singsaker Studenterhjem<br/><br/>(Dette var en automagisk beskjed. Feil? Ta kontakt med datagutta!)</body></html>";

                $tittel = "[SING-INTERN] Ditt passord har blitt resatt.";
                Epost::sendEpost($aktuellBruker->getPerson()->getEpost(), $tittel, $beskjed);
                //$sendEpost = new Epost($beskjed);
                //$sendEpost->addBrukerId($bruker_id);
                //$sendEpost->send($tittel);
                $dok->set('epostSendt', 1);
            }
        }
        $dok->set('skjulMeny', 1);
        $dok->vis('glemtpassord.php');
    } */

    private function okTid($tid){
        //Hvis tiden er mindre enn 24t siden.
        $dogn = 86400; //24*60*60
        return (time() - $tid) < $dogn;

    }

    private function resettPassord(){

        $token = $this->cd->getSisteArg();
        $bruker = Bruker::byGlemtToken($token);

        if($bruker === null){
            session_destroy();
            session_start();
            Funk::setError("Ser ut som du gjorde noe galt! Prøv gjerne på nytt.");
            header('Location: ?a=logginn');
            exit();
        }

        $tiden = $bruker->getResettTid();
        if($bruker != null && $this->okTid($tiden)){
            $dok = new Visning($this->cd);

            if(isset($_POST) && isset($_POST['passord1']) && isset($_POST['passord2'])){

                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $passord1 = $post['passord1'];
                $passord2 = $post['passord2'];

                if($passord1 === $passord2) {

                    $hash = LogginnCtrl::genererHash($passord1, $bruker->getId());
                    $bruker->endrePassord($hash);

                    //Fjerne gyldighet av link.
                    $st = DB::getDB()->prepare('UPDATE bruker SET dato=0 WHERE id=:id');
                    $st->bindParam(':id', $bruker->getId());
                    $st->execute();

                    Funk::setSuccess("Ditt passord ble endret!");

                    header('Location: ?a=diverse');
                    exit();

                } else {
                    $_SESSION['error'] = 1;
                    $_SESSION['msg'] = "Passordene matchet ikke. Prøv på nytt!";
                }
            }

            $dok->set('skjulMeny', 1);
            $dok->vis('glemt_passord_resett.php');
            exit();
        }

    }

    private function glemtPassord(){

        $dok = new Visning($this->cd);

        if(isset($_POST) && isset($_POST['brukernavn'])){

            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $epost = $post['brukernavn'];
            $aktuellBruker = Bruker::medEpost($epost);

            if($aktuellBruker != null){
                //Generer random string
                $token = Token::generateToken();
                $token = md5($token . $aktuellBruker->getPerson()->getFulltNavn());

                $brukerId = $aktuellBruker->getId();
                $dato = time();

                $st = DB::getDB()->prepare('UPDATE bruker SET glemt_token=:token,dato=:dato WHERE id=:id');
                $st->bindParam(':token', $token);
                $st->bindParam(':id', $brukerId);
                $st->bindParam(':dato', $dato);
                $st->execute();

                $link = '<a href="http://' . $_SERVER['SERVER_NAME'] . '/?a=logginn/passord/' . $token . '">denne siden</a>';
                $beskjed = "<html><body>Hei,<br/><br/>Ditt passord har blitt forespurt resatt. Hvis du ønsker å resette det
vennligst besøk $link. Dersom du ikke ønsker å resette det, se bort fra denne e-posten. Lenken er gyldig i 24t.<br/><br/>Med vennlig hilsen<br/>Internsida</body></html>";
                $tittel = "[SING-INTERN] Forespørsel om resatt passord.";

                Epost::sendEpost($epost, $tittel, $beskjed);

            }

            $_SESSION['success'] = 1;
            $_SESSION['msg'] = "Hvis e-posten eksisterer, har en e-post med videre instruksjoner blitt sendt.";
        }

        $dok->set('skjulMeny', 1);
        $dok->vis('glemt_passord.php');
    }

    public static function getAktivBruker()
    {
        if (!isset($_SESSION['brukernavn']) || !isset($_SESSION['passord'])) {
            return null;
        }
        $bruker = Bruker::medEpost($_SESSION['brukernavn']);
        if ($bruker == null) {
            return null;
        }
        if (!$bruker->passordErGyldig($_SESSION['passord'])) {
            return null;
        }
        return $bruker;
    }

    public static function genererHash($passord, $brukerid)
    {
        $saltet = (Bruker::medId($brukerid) != null) ? Bruker::medId($brukerid)->getSalt() : exit(1);
        if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH) {
            //$salt = '$2y$11$' . substr(md5($passord . 'V@Q?0q%FCB5?iIB'), 0, 27);
            //return crypt('Z\'3s+uc(WDk<,7Q' . crypt($passord, $salt), '$6$rounds=5000$VM5wn6AvwUOAdUO24oLzGQ$');
            return crypt($passord, '$6$rounds=5000$' . $saltet . '$');
        }
        throw new \Exception('Sugefisk?');
    }

    public static function genererHashMedSalt($passord, $salt)
    {
        if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH) {
            //$salt = '$2y$11$' . substr(md5($passord . 'V@Q?0q%FCB5?iIB'), 0, 27);
            //return crypt('Z\'3s+uc(WDk<,7Q' . crypt($passord, $salt), '$6$rounds=5000$VM5wn6AvwUOAdUO24oLzGQ$');
            return crypt($passord, '$6$rounds=5000$' . $salt . '$');
        }
        throw new \Exception('Sugefisk?');
    }
}

?>