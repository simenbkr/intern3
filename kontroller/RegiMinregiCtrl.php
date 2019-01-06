<?php

namespace intern3;


class RegiMinregiCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        
        if(is_numeric($aktueltArg) &&
            ($arbeid = Arbeid::medId($this->cd->getSisteArg())) != null){
            
            if($this->cd->getAktivBruker() === $arbeid->getBruker() ||
                LogginnCtrl::getAktivBruker()->getPerson()->harUtvalgVerv()) {

                // Legge til (flere) bilder, dersom arbeidet er i "Ubehandla" tilstand
                if($_SERVER['REQUEST_METHOD'] === 'POST' && $arbeid->getIntStatus() == 0) {
                    $this->lastOppBilder($aktueltArg);
                    exit();
                }
                elseif($_SERVER['REQUEST_METHOD'] === 'GET') {

                    $dok = new Visning($this->cd);
                    $dok->set('arbeidet', $arbeid);
                    $dok->vis('regi/regi_minregi_detaljert.php');
                }
            } else {
                Funk::setError("Du har ikke tilgang til dette objektet!");
                header('Location: ?a=regi/minregi');
            }
            exit();
        }
        
        $feil = array();
        if (isset($_POST['registrer'])) {
            $feil = $this->registrerArbeid();
            if (count($feil) == 0) {
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
        if (isset($_POST['slett']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if ($post['slett'] == 1 && isset($post['arbeid']) && is_numeric($post['arbeid'])) {
                $arbeidId = $post['arbeid'];
                $brukerId = LogginnCtrl::getAktivBruker()->getId();
                $aktuell_arbeid = Arbeid::medId($arbeidId);
                if (($aktuell_arbeid->getBrukerId() == $brukerId || Beboer::medBrukerId($brukerId)->harUtvalgVerv())
                    && $aktuell_arbeid->getGodkjent() == 0 && $aktuell_arbeid->inCurrentSem()) {
                    $st = DB::getDB()->prepare('DELETE FROM arbeid WHERE id=:id');
                    $st->bindParam(':id', $arbeidId);
                    $st->execute();
                    
                    foreach(ArbeidBilde::medArbeidId($arbeidId) as $arbeidbilde){
                        $arbeidbilde->slett();
                    }
                    
                }
                
                if (!$aktuell_arbeid->inCurrentSem())
                    Funk::setError("Du kan ikke slette ført regi for tidligere semestere!");
            }
        }
        if (isset($_POST['semester']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $_SESSION['regisemester'] = $post['semester'];
            header('Location: ?a=regi/minregi');
            exit();
        }
        
        if (isset($_SESSION['regisemester']) && is_int(($unix = Funk::semStrToUnix($_SESSION['regisemester']))) && $unix > 0) {
            $arbeidListe = ArbeidListe::medBrukerIdSemester($this->cd->getAktivBruker()->getId(), $unix);
        } else {
            $arbeidListe = ArbeidListe::medBrukerIdSemester($this->cd->getAktivBruker()->getId());
        }
        
        $regitimer = array(
            '0' => 0,
            '1' => 0,
            '-1' => 0
        );
        foreach ($arbeidListe as $arbeid) {
            $regitimer[$arbeid->getIntStatus()] += $arbeid->getSekunderBrukt() / 3600;
        }
        $semesterList = LogginnCtrl::getAktivBruker()->getPerson()->getSemesterlist();
        $mapping = array();
        foreach ($semesterList as $sem) {
            $mapping[$sem] = Funk::semStrToReadable($sem);
        }
        
        $dok = new Visning($this->cd);
        $dok->set('feil', $feil);
        $dok->set('mapping', $mapping);
        $dok->set('regitimer', $regitimer);
        $dok->set('arbeidListe', $arbeidListe);
        $dok->vis('regi/regi_minregi.php');
    }
    
    private function registrerArbeid()
    {
        $feil = $this->godkjennArbeid();
        if (count($feil) == 0) {
            $endringer = array(
                'bruker_id' => ':bruker_id',
                'polymorfkategori_id' => ':polymorfkategori_id',
                'polymorfkategori_velger' => ':polymorfkategori_velger',
                'sekunder_brukt' => ':sekunder_brukt',
                'tid_utfort' => ':tidUtfort',
                'kommentar' => ':kommentar'
            );
            $parametre = array(
                ':bruker_id' => $this->cd->getAktivBruker()->getId(),
                ':polymorfkategori_id' => $_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']],
                ':polymorfkategori_velger' => $this->getPolymorfkategoriVelger(),
                ':sekunder_brukt' => $this->getSekunderBrukt(),
                ':tidUtfort' => $_POST['tid_utfort'],
                ':kommentar' => $_POST['kommentar']
            );
            $sql = 'INSERT INTO arbeid(' . implode(',', array_keys($endringer)) . ') VALUES(' . implode(',', $endringer) . ');';
            $st = DB::getDB()->prepare($sql);
            foreach ($parametre as $navn => $verdi) {
                $st->bindValue($navn, $verdi);
            }
            $st->execute();
            
            $st = DB::getDB()->prepare('SELECT * FROM arbeid ORDER BY ID DESC LIMIT 1');
            $st->execute();
            $rad = $st->fetch();
            $id = $rad['id'];
            
            $this->lastOppBilder($id);
            
        }
        return $feil;
    }
    
    private function lastOppBilder($id)
    {
        $gyldige_extensions = array("jpeg", "jpg", "png", "gif");
        $regibilder_path = dirname(__DIR__) . '/www/regibilder/';
        
        $antall = count($_FILES['file']['name']);
        
        for($key = 0; $key < $antall; $key++){
            
            $file_ext = strtolower(end(explode('.', $_FILES['file']['name'][$key])));
            
            if(!in_array($file_ext, $gyldige_extensions)){
                continue;
            }
            
            $filnavn = md5($_FILES['file']['name'] . Funk::generatePassword()) . '.' . $file_ext;
            $bildesti = $regibilder_path . $filnavn;
            if(!move_uploaded_file($_FILES['file']['tmp_name'][$key], $bildesti)){
                continue;
            }
            
            chmod($bildesti, 0644);
            ArbeidBilde::opprett("$filnavn", $id);
        }
        
        
    }
    
    private function godkjennArbeid()
    {
        $feil = array();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        do {
            if (!isset($post['polymorfkategori_velger']) || !$post['polymorfkategori_velger']) {
                $feil[] = 'Tilhørighet mangler.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Tilhørighet mangler.";
                break;
            }
            if ($this->getPolymorfkategoriVelger() == -1) {
                $feil[] = 'Valgt tilhørighet fins ikke.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Valgt tilhørighet fins ikke.";
                break;
            }
        } while (false);
        do {
            if (!isset($post['polymorfkategori_id']) || !isset($post['polymorfkategori_id'][$post['polymorfkategori_velger']]) || !$post['polymorfkategori_id'][$post['polymorfkategori_velger']]) {
                $feil[] = 'Kategori mangler.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Kategori mangler.";
                break;
            }
        } while (false);
        do {
            if (!isset($post['tid_utfort']) || !$post['tid_utfort']) {
                $feil[] = 'Utførelsesdato mangler.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Utførelsesdato mangler.";
                break;
            }
            if (!Funk::erDatoGyldigFormat($post['tid_utfort'])) {
                $feil[] = 'Utførelsesdato må være i formatet åååå-mm-dd.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Utførelsesdato må være i formatet åååå-mm-dd.";
                break;
            }
            if (!Funk::finsDato($post['tid_utfort'])) {
                $feil[] = 'Utførelsesdato er ugyldig, datoen fins ikke.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Utførelsesdato er ugyldig, datoen fins ikke.";
                break;
            }
        } while (false);
        do {
            if (!isset($post['tid_brukt']) || !$post['tid_brukt']) {
                $feil[] = 'Tid brukt mangler.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Tid brukt mangler.";
                break;
            }
            if (
                !preg_match('/^[0-9]+(\:[0-9]{2})?$/', $post['tid_brukt'])
                && !preg_match('/^[0-9]+(\,[0-9]+)?$/', $post['tid_brukt'])
                && !preg_match('/^[0-9]+(\.[0-9]+)?$/', $post['tid_brukt'])
            ) {
                $feil[] = 'Tid brukt må være på formatet timer:minutter eller timer som desimaltall.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Tid brukt må være på formatet timer:minutter eller timer som desimaltall.";
                break;
            }
            if ($this->getSekunderBrukt() == 0) {
                $feil[] = 'Tid brukt må være noe annet enn 0.';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Tid brukt må være noe annet enn 0.";
                break;
            }
        } while (false);
        if (!isset($post['kommentar']) || !$post['kommentar']) {
            $feil[] = 'Kommentar mangler.';
            $_SESSION['error'] = 1;
            $_SESSION['msg'] = "Kommentar mangler.";
        }
        do {
            $tiden = date('Y-m-d', strtotime($post['tid_utfort']));
            $aar_utfort = date('Y', strtotime($tiden));
            $dagens_dato = date('Y-m-d');
            $dagens_aar = date('Y');
            $semester_start = null;
            $semester_slutt = null;
            
            if ($tiden > $dagens_dato) {
                $feil[] = "Du kan ikke registrere regi for fremtiden!";
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Du kan ikke registrere regi for fremtiden!";
            }
            
            if (strtotime($tiden) > strtotime("$dagens_aar-01-01") && strtotime($tiden) < strtotime("$dagens_aar-07-01")) {
                //Vår semester
                $semester_start = date('Y-m-d', strtotime("$dagens_aar-01-01"));
                $semester_slutt = date('Y-m-d', strtotime("$dagens_aar-07-01"));
            } else {
                //Høst semester
                $semester_start = date('Y-m-d', strtotime("$dagens_aar-07-01"));
                $semester_slutt = date('Y-m-d', strtotime("$dagens_aar-12-31"));
            }
            
            if ($tiden > $semester_slutt || $tiden < $semester_start) {
                $feil[] = 'Du kan ikke registrere regi for et annet semester!';
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Du kan ikke registrere regi for et annet semester!";
            }
        } while (false);
        return $feil;
    }
    
    private function getSekunderBrukt()
    {
        if (preg_match('/^([0-9]+)$/', $_POST['tid_brukt'], $treff)) {
            return $treff[1] * 3600;
        }
        if (preg_match('/^([0-9]+)(\:([0-9]{2}))?$/', $_POST['tid_brukt'], $treff)) {
            return $treff[1] * 3600 + $treff[3] * 60;
        }
        if (preg_match('/^[0-9]+(\,[0-9]+)?$/', $_POST['tid_brukt'])) {
            return str_replace(',', '.', $_POST['tid_brukt']) * 3600;
        }
        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['tid_brukt'])) {
            return $_POST['tid_brukt'] * 3600;
        }
        return 0;
    }
    
    private function getPolymorfkategoriVelger()
    {
        $polymorf = -1;
        if (!isset($_POST['polymorfkategori_velger'])) {
            return $polymorf;
        }
        switch ($_POST['polymorfkategori_velger']) {
            case 'ymse':
                $polymorf = ArbeidPolymorfkategori::YMSE;
                break;
            case 'feil':
                $polymorf = ArbeidPolymorfkategori::FEIL;
                break;
            case 'rapp':
                $polymorf = ArbeidPolymorfkategori::RAPP;
                break;
            case 'oppg':
                $polymorf = ArbeidPolymorfkategori::OPPG;
                break;
            default:
                $polymorf = -1;
                break;
        }
        return $polymorf;
    }
}

?>
