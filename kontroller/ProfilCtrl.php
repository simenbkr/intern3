<?php

namespace intern3;

class ProfilCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $feil = array();
        if (isset($_POST['endre'])) {
            switch ($_POST['endre']) {
                case 'generell':
                    $feil = array_merge($feil, $this->endreGenerellInfo());
                    break;
                case 'passord':
                    $feil = array_merge($feil, $this->endrePassord());
                    break;
                case 'bilde':
                    $feil = array_merge($feil, $this->endreBilde());
                    break;
                case 'varsler':
                    $feil = array_merge($feil, $this->endreVarsler());
                    break;
            }
            if (count($feil) == 0) {
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        $epostInst = LogginnCtrl::getAktivBruker()->getPerson()->getEpostPref();
        $dok = new Visning($this->cd);
        $dok->set('epostInst', $epostInst);
        $dok->set('feil', $feil);
        $dok->vis('profil.php');
    }

    private function endreBilde(){
        $tillatte_filtyper = array('jpg', 'jpeg', 'png', 'gif');
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (isset($_FILES['image'])) {

            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $tmp_file = $_FILES['image']['tmp_name'];
            $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

            if (in_array($file_ext, $tillatte_filtyper) && $file_size > 10 && $file_size < 1000000000) {
                $bildets_navn = md5($file_name . "spisostdindostogfuckoff" . time()) . '.' . $file_ext;
                move_uploaded_file($tmp_file, "profilbilder/" . $bildets_navn);
                chmod("vinbilder/" . $bildets_navn, 0644);
                $id = LogginnCtrl::getAktivBruker()->getPerson()->getId();
                $st = DB::getDB()->prepare('UPDATE beboer SET bilde=:bilde WHERE id=:id');
                $st->bindParam(':bilde', $bildets_navn);
                $st->bindParam(':id', $id);
                $st->execute();
            } else {
                return array('Det var ikke et gyldig bilde!');
            }
        } else {
            return array('Du valgte ikke et bilde!');
        }
        return null;
    }

    private function endreVarsler()
    {
        $options = array('tildeltvakt', 'vakt', 'vaktbytte', 'utleie', 'barvakt');
        $st = DB::getDB()->prepare('UPDATE epost_pref SET tildelt=:tildeltvakt, snart_vakt=:vakt, bytte=:vaktbytte, utleie=:utleie, barvakt=:barvakt');
        foreach ($options as $option) {
            $var = ':' . $option;
            if (isset($_POST[$option]) && $_POST[$option] == 1) {
                $en = 1;
                $st->bindParam($var, $en);
            } else {
                $null = 0;
                $st->bindParam($var, $null);
            }
        }
        $st->execute();
        return null;
    }


    private function endreGenerellInfo()
    {
        $feil = $this->godkjennGenerellInfo();
        if (count($feil) == 0) {
            $endringer = array();
            $parametre = array();
            foreach (array(
                         'fodselsdato',
                         'epost',
                         'telefon',
                         'adresse',
                         'postnummer',
                         'skole_id',
                         'studie_id',
                         'klassetrinn'
                     ) as $felt) {
                if (isset($_POST[$felt])) {
                    $endringer[] = $felt . '=:' . $felt;
                    $parametre[':' . $felt] = $_POST[$felt];
                }
            }
            if (count($endringer) > 0) {
                $sql = 'UPDATE beboer SET ' . implode(',', $endringer) . ' WHERE id=:id;';
                $id = $this->cd->getAktivBruker()->getPerson()->getId();
                $st = DB::getDB()->prepare($sql);
                $st->bindParam(':id', $id);
                foreach ($parametre as $navn => $verdi) {
                    $st->bindValue($navn, $verdi);
                }
                $st->execute();
            }
        }
        return $feil;
    }

    private function godkjennGenerellInfo()
    {
        $feil = array();
        do {
            if (!isset($_POST['fodselsdato']) || !$_POST['fodselsdato']) {
                $feil[] = 'Fødselsdato mangler.';
                break;
            }
            if (!Funk::erDatoGyldigFormat($_POST['fodselsdato'])) {
                $feil[] = 'Fødselsdato må være i formatet åååå-mm-dd.';
                break;
            }
            if (!Funk::finsDato($_POST['fodselsdato'])) {
                $feil[] = 'Fødselsdato er ugyldig, datoen fins ikke.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['epost']) || !$_POST['epost']) {
                $feil[] = 'Epost mangler.';
                break;
            }
            if (!filter_var($_POST['epost'], FILTER_VALIDATE_EMAIL)) {
                $feil[] = 'Epost har ugyldig format.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['telefon']) || !$_POST['telefon']) {
                $feil[] = 'Telefon mangler.';
                break;
            }
            if (!preg_match('/^(\+[0-9]{2,})?[0-9]{8}$/', str_replace(' ', '', $_POST['telefon']))) {
                $feil[] = 'Telefon må enten være 8 sifre eller ha \'+\' og retningsnummer foran.';
                break;
            }
        } while (false);
        if (!isset($_POST['adresse']) || !$_POST['adresse']) {
            $feil[] = 'Adresse mangler.';
            break;
        }
        do {
            if (!isset($_POST['postnummer']) || !$_POST['postnummer']) {
                $feil[] = 'Postnummer mangler.';
                break;
            }
            if (!preg_match('/^[0-9]{4}$/', $_POST['postnummer'])) {
                $feil[] = 'Postnummer må ha 4 sifre.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['skole_id']) || !$_POST['skole_id']) {
                $feil[] = 'Skole mangler.';
                break;
            }
            if (Skole::medId($_POST['skole_id']) == null) {
                $feil[] = 'Skole må være valgt fra lista.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['studie_id']) || !$_POST['studie_id']) {
                $feil[] = 'Studie mangler.';
                break;
            }
            if (Studie::medId($_POST['studie_id']) == null) {
                $feil[] = 'Studie må være valgt fra lista.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['klassetrinn']) || !$_POST['klassetrinn']) {
                $feil[] = 'Klassetrinn mangler.';
                break;
            }
            if (!preg_match('/^[1-6]{1}$/', $_POST['klassetrinn'])) {
                $feil[] = 'Klassetrinn må være et tall fra 1 til 6.';
                break;
            }
        } while (false);
        return $feil;
    }

    private function endrePassord()
    {
        $feil = $this->godkjennPassord();
        if (count($feil) == 0 && isset($_POST['passord1'])) {
            $st = DB::getDB()->prepare('UPDATE bruker SET passord=:passord WHERE id=:id;');
            $id = $this->cd->getAktivBruker()->getId();
            $passord = LogginnCtrl::genererHash($_POST['passord1'], $id);
            $st->bindParam(':id', $id);
            $st->bindParam(':passord', $passord);
            $st->execute();
            //setcookie('passord', $passord, $_SERVER['REQUEST_TIME'] + 31556926, NULL, NULL, NULL, TRUE);
            $_SESSION['passord'] = $passord;
            $feil = array("Endret passord");
        }
        return $feil;
    }

    private function godkjennPassord()
    {
        if ($_POST['passord1'] <> $_POST['passord2']) {
            return array('De to passordene stemmer ikke.');
        }
        if (strlen($_POST['passord1']) < 8) {
            return array('Det nye passordet må være minst 8 tegn.');
        }
        return array();
    }
}

?>