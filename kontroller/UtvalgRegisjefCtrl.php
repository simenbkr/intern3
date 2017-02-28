<?php

namespace intern3;

class UtvalgRegisjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
            case 'registatus':
                $dok = new Visning($this->cd);

                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    //
                    //'endreRegi=1&halv=' + halv + "&full=" + full,
                    if (isset($post['endreRegi']) && $post['endreRegi'] == 1 && isset($post['halv']) && is_numeric($post['halv'])
                        && isset($post['full']) && is_numeric($post['full'])
                    ) {
                        $halv = $post['halv'];
                        $full = $post['full'];

                        $st = DB::getDB()->prepare('UPDATE rolle SET regitimer=:halv WHERE id=1');
                        $st->bindParam(':halv', $halv);
                        $st->execute();

                        $st2 = DB::getDB()->prepare('UPDATE rolle SET regitimer=:fullregi WHERE id=3');
                        $st2->bindParam(':fullregi', $full);
                        $st2->execute();
                    }
                }
                $unix = $_SERVER['REQUEST_TIME'];
                $dok->set('tabeller', array(
                    'Har gjenværende regitimer' => BrukerListe::harRegiIgjen($unix),
                    'Har ikke gjenværende regitimer' => BrukerListe::harIkkeRegiIgjen($unix)
                ));
                $timer_brukt = Arbeid::getTimerBruktPerSemester();
                $roller = RolleListe::alle();
                $dok->set('roller', $roller);
                $dok->set('timer_brukt', $timer_brukt);
                $dok->vis('regi_registatus.php');
                return;
            case 'leggtilarbeid':
                $sisteArg = $this->cd->getSisteArg();
                $dok = new Visning($this->cd);
                if ($sisteArg != 'leggtilarbeid' && is_numeric($sisteArg) && ($beboeren = Beboer::medId($sisteArg)) != null) {
                    if (isset($_POST) && count($_POST) > 0) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        // foreach($post as $key => $val){setcookie($key,$val);}
                        if (isset($_POST['registrer'])) {
                            $feil = $this->registrerArbeid($beboeren->getBrukerId());
                            if (count($feil) == 0) {
                                header('Location: ' . $_SERVER['REQUEST_URI']);
                                exit();
                            }
                        }
                    }
                    $arbeidListe = ArbeidListe::medBrukerIdSemester($beboeren->getBrukerId());
                    $regitimer = array(0, 0);
                    foreach ($arbeidListe as $arbeid) {
                        $regitimer[$arbeid->getGodkjent() ? 1 : 0] += $arbeid->getSekunderBrukt() / 3600;
                    }
                    $dok->set('regitimer', $regitimer);
                    $dok->set('arbeidListe', $arbeidListe);
                    $dok->set('beboeren', $beboeren);
                    $dok->vis('utvalg_regisjef_leggetil_arbeid_beboer.php');
                    return;
                }
                $har_regi = BeboerListe::aktiveMedRegi();

                $dok->set('beboerliste', $har_regi);
                $dok->vis('utvalg_regisjef_leggetil_arbeid.php');
                return;
            case 'arbeid':
                $valgtCtrl = new UtvalgRegisjefArbeidCtrl($this->cd->skiftArg());
                break;
            case 'oppgave':
                $valgtCtrl = new UtvalgRegisjefOppgaveCtrl($this->cd->skiftArg());
                break;

            default:
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_regisjef.php');
                return;
        }
        $valgtCtrl->bestemHandling();
    }


    private function registrerArbeid($bruker_id)
    {
        $feil = $this->godkjennArbeid();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
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
                ':bruker_id' => $bruker_id,
                ':polymorfkategori_id' => $post['polymorfkategori_id'][$post['polymorfkategori_velger']],
                ':polymorfkategori_velger' => $this->getPolymorfkategoriVelger(),
                ':sekunder_brukt' => $this->getSekunderBrukt(),
                ':tidUtfort' => $post['tid_utfort'],
                ':kommentar' => $post['kommentar']
            );
            $aktiv_bruker = LogginnCtrl::getAktivBruker()->getId();
            $sql = 'INSERT INTO arbeid(' . implode(',', array_keys($endringer)) . ', godkjent,godkjent_bruker_id,tid_godkjent) VALUES(' . implode(',', $endringer) . ',1,' . $aktiv_bruker . ',CURRENT_TIMESTAMP);';
            setcookie('test', $sql);
            $st = DB::getDB()->prepare($sql);
            foreach ($parametre as $navn => $verdi) {
                $st->bindValue($navn, $verdi);
            }
            $st->execute();
        }
        return $feil;
    }

    private function godkjennArbeid()
    {
        $feil = array();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        //TODO bruke sikker input!
        do {
            if (!isset($_POST['polymorfkategori_velger']) || !$_POST['polymorfkategori_velger']) {
                $feil[] = 'Tilhørighet mangler.';
                break;
            }
            if ($this->getPolymorfkategoriVelger() == -1) {
                $feil[] = 'Valgt tilhørighet fins ikke.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['polymorfkategori_id']) || !isset($_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']]) || !$_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']]) {
                $feil[] = 'Kategori mangler.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['tid_utfort']) || !$_POST['tid_utfort']) {
                $feil[] = 'Utførelsesdato mangler.';
                break;
            }
            if (!Funk::erDatoGyldigFormat($_POST['tid_utfort'])) {
                $feil[] = 'Utførelsesdato må være i formatet åååå-mm-dd.';
                break;
            }
            if (!Funk::finsDato($_POST['tid_utfort'])) {
                $feil[] = 'Utførelsesdato er ugyldig, datoen fins ikke.';
                break;
            }
        } while (false);
        do {
            if (!isset($_POST['tid_brukt']) || !$_POST['tid_brukt']) {
                $feil[] = 'Tid brukt mangler.';
                break;
            }
            if (
                !preg_match('/^[0-9]+(\:[0-9]{2})?$/', $_POST['tid_brukt'])
                && !preg_match('/^[0-9]+(\,[0-9]+)?$/', $_POST['tid_brukt'])
                && !preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['tid_brukt']) &&
                !preg_match('/^-[0-9]+(\:[0-9]{2})?$/', $_POST['tid_brukt'])
                && !preg_match('/^-[0-9]+(\,[0-9]+)?$/', $_POST['tid_brukt'])
                && !preg_match('/^-[0-9]+(\.[0-9]+)?$/', $_POST['tid_brukt'])
            ) {
                $feil[] = 'Tid brukt må være på formatet timer:minutter eller timer som desimaltall.';
                break;
            }
            if ($this->getSekunderBrukt() == 0) {
                $feil[] = 'Tid brukt må være noe annet enn 0.';
                break;
            }
        } while (false);
        if (!isset($_POST['kommentar']) || !$_POST['kommentar']) {
            $feil[] = 'Kommentar mangler.';
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
            }
        } while (false);
        //return array();
        return $feil;
    }

    private function getSekunderBrukt()
    {
        $neg = ($_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']] == 13);
        if (preg_match('/^([0-9]+)$/', $_POST['tid_brukt'], $treff)) {
            return $neg ? -$treff[1] * 3600 : $treff[1] * 3600;
        }
        if (preg_match('/^([0-9]+)(\:([0-9]{2}))?$/', $_POST['tid_brukt'], $treff)) {
            return $neg ? -$treff[1] * 3600 - $treff[3] * 60 : $treff[1] * 3600 + $treff[3] * 60;
        }
        if (preg_match('/^[0-9]+(\,[0-9]+)?$/', $_POST['tid_brukt'])) {
            return $neg ? -str_replace(',', '.', $_POST['tid_brukt']) * 3600 : str_replace(',', '.', $_POST['tid_brukt']) * 3600;
        }
        if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['tid_brukt'])) {
            return $neg ? -$_POST['tid_brukt'] * 3600 : $_POST['tid_brukt'] * 3600;
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
