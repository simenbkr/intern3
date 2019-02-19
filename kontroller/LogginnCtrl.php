<?php

namespace intern3;

class LogginnCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        if ($this->cd->getAktueltArg() == 'loggut') {
            $this->loggUt();
        } else if ($this->cd->getAktueltArg() == 'passord') {

            if ('passord' == $this->cd->getSisteArg()) {

                $this->glemtPassord();
                exit();
            } else {
                $this->resettPassord();
            }
        } else if (isset($_POST['brukernavn']) && isset($_POST['passord'])) {
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

    private function loggInn()
    {
        session_destroy();
        session_start();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $brukeren = Bruker::medEpost($post['brukernavn']);
        if ($brukeren != null
            && $brukeren->passordErGyldig(self::genererHash($post['passord'], $brukeren->getId()))
            && $brukeren->getPerson()->erAktiv()) {

            $_SESSION['brukernavn'] = $post['brukernavn'];
            $this->cd->setAktivBruker($brukeren);
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

    private function okTid($tid)
    {
        //Hvis tiden er mindre enn 24t siden.
        $dogn = 86400; //24*60*60
        return (time() - $tid) < $dogn;

    }

    private function resettPassord()
    {

        $token = $this->cd->getSisteArg();
        $bruker = Bruker::byGlemtToken($token);

        if ($bruker === null) {
            session_destroy();
            session_start();
            Funk::setError("Ser ut som du gjorde noe galt! Prøv gjerne på nytt.");
            header('Location: ?a=logginn');
            exit();
        }

        $tiden = $bruker->getResettTid();
        if ($bruker != null && $this->okTid($tiden)) {
            $dok = new Visning($this->cd);

            if (isset($_POST) && isset($_POST['passord1']) && isset($_POST['passord2'])) {

                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $passord1 = $post['passord1'];
                $passord2 = $post['passord2'];

                if ($passord1 === $passord2) {

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

    private function glemtPassord()
    {

        $dok = new Visning($this->cd);

        if (isset($_POST) && isset($_POST['brukernavn'])) {

            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $epost = $post['brukernavn'];
            $aktuellBruker = Bruker::medEpost($epost);

            if ($aktuellBruker != null) {
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
        if (!isset($_SESSION['brukernavn'])) {
            return null;
        }
        $bruker = Bruker::medEpost($_SESSION['brukernavn']);
        if ($bruker == null) {
            return null;
        }
        return $bruker;
    }

    public static function genererHash($passord, $brukerid)
    {
        $saltet = (Bruker::medId($brukerid) != null) ? Bruker::medId($brukerid)->getSalt() : exit(1);
        if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH) {
            return crypt($passord, '$6$rounds=5000$' . $saltet . '$');
        }
        throw new \Exception('Sugefisk?');
    }

    public static function genererHashMedSalt($passord, $salt)
    {
        if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH) {
            return crypt($passord, '$6$rounds=5000$' . $salt . '$');
        }
        throw new \Exception('Sugefisk?');
    }
}

?>