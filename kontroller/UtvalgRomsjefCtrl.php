<?php

namespace intern3;

class UtvalgRomsjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $skoleListe = SkoleListe::alle();
        $studieListe = StudieListe::alle();
        $rolleListe = RolleListe::alle();
        $romListe = RomListe::alle();
        $aktive_beboere = BeboerListe::aktive();
        $dok = new Visning($this->cd);
        $dok->set('skoleListe', $skoleListe);
        $dok->set('studieListe', $studieListe);
        $dok->set('rolleListe', $rolleListe);
        $dok->set('romListe', $romListe);

        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'beboerliste') {
            $dok = new Visning($this->cd);
            $sisteArg = $this->cd->getSisteArg();
            if ($sisteArg != $aktueltArg && is_numeric($sisteArg)) {
                //data: 'flyttut=1&beboerId=' + beboerId,
                if (isset($_POST) && isset($_POST['flyttut']) && isset($_POST['beboerId']) && is_numeric($_POST['beboerId'])) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboer = Beboer::medId($post['beboerId']);
                    if ($beboer != null) {
                        $beboer->flyttUt();
                    }
                } elseif (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboer_id = $post['beboerid'];
                    $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id');
                    $st->bindParam(':id', $beboer_id);
                    $st->execute();
                    $raden = $st->fetchColumn();
                    $romhistorikk = Romhistorikk::fraJson($raden);
                    if ($romhistorikk != null && !($romhistorikk->getAktivRomId() == $post['rom_id'])) {
                        $romhistorikk->addPeriode($post['rom_id'], date('Y-m-d'), null);
                        $raden = $romhistorikk->tilJson();
                    }
                    $st = DB::getDB()->prepare('UPDATE beboer SET fornavn=:fornavn,mellomnavn=:mellomnavn,etternavn=:etternavn,
fodselsdato=:fodselsdato,adresse=:adresse,postnummer=:postnummer,telefon=:telefon,studie_id=:studie_id,skole_id=:skole_id,
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk WHERE id=:id');

                    $st->bindParam(':id', $beboer_id);
                    $st->bindParam(':fornavn', $post['fornavn']);
                    $st->bindParam(':mellomnavn', $post['mellomnavn']);
                    $st->bindParam(':etternavn', $post['etternavn']);
                    $st->bindParam(':fodselsdato', $post['fodselsdato']);
                    $st->bindParam(':adresse', $post['adresse']);
                    $st->bindParam(':postnummer', $post['postnummer']);
                    $st->bindParam(':telefon', $post['mobil']);
                    $st->bindParam(':studie_id', $post['studie_id']);
                    $st->bindParam(':skole_id', $post['skole_id']);
                    $st->bindParam(':klassetrinn', $post['klasse']);
                    $alko = (isset($post['alkodepositum']) && $post['alkodepositum'] == 'on') ? 1 : 0;
                    $st->bindParam(':alko', $alko);
                    $st->bindParam(':rolle', $post['rolle_id']);
                    $st->bindParam(':epost', $post['epost']);
                    $st->bindParam('romhistorikk', $raden);
                    $st->execute();
                }
                $beboer = Beboer::medId($sisteArg);
                if ($beboer != null || !in_array($beboer, $aktive_beboere)) {
                    $dok->set('skoleListe', $skoleListe);
                    $dok->set('studieListe', $studieListe);
                    $dok->set('rolleListe', $rolleListe);
                    $dok->set('romListe', $romListe);
                    $dok->set('beboer', $beboer);
                    $dok->vis('utvalg_romsjef_endre_denne_beboeren.php');
                    return;
                }
            }
            $beboerListe = BeboerListe::aktive();
            $dok->set('beboerListe', $beboerListe);
            $dok->vis('utvalg_romsjef_beboerliste.php');
        } else if ($aktueltArg == 'nybeboer') {
            if (isset($_POST) && isset($_POST['fornavn'])) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $bruker_id = Funk::getLastBrukerId() + 1;
                $st = DB::getDB()->prepare('INSERT INTO beboer 
(bruker_id,fornavn,mellomnavn,etternavn,fodselsdato,adresse,postnummer,telefon,studie_id,skole_id,klassetrinn,alkoholdepositum,rolle_id,epost,romhistorikk)
VALUES(:bruker_id,:fornavn,:mellomnavn,:etternavn,:fodselsdato,:adresse,:postnummer,:telefon,:studie_id,:skole_id,:klassetrinn,:alko,:rolle_id,:epost,:romhistorikk)');
                $st->bindParam(':bruker_id', $bruker_id);
                $st->bindParam(':fornavn', $post['fornavn']);
                $st->bindParam(':mellomnavn', $post['mellomnavn']);
                $st->bindParam(':etternavn', $post['etternavn']);
                $st->bindParam(':fodselsdato', $post['fodselsdato']);
                $st->bindParam(':adresse', $post['adresse']);
                $st->bindParam(':postnummer', $post['postnummer']);
                $st->bindParam(':telefon', $post['mobil']);
                $st->bindParam(':studie_id', $post['studie_id']);
                $st->bindParam(':skole_id', $post['skole_id']);
                $st->bindParam(':klassetrinn', $post['klasse']);
                $st->bindParam(':alko', $post['alkodepositum']);
                $st->bindParam(':rolle_id', $post['rolle_id']);
                $st->bindParam(':epost', $post['epost']);
                $rom = new Romhistorikk();
                $rom->addPeriode($post['rom_id'], date('Y-m-d'), null);
                $romhistorikken = $rom->tilJson();
                $st->bindParam(':romhistorikk', $romhistorikken);
                $st->execute();

                $st = DB::getDB()->prepare('INSERT INTO bruker (id,passord,salt) VALUES(:id,:passord,:salt)');
                $st->bindParam(':id', $bruker_id);
                $passord = Funk::generatePassword();
                $saltet = Funk::generatePassword(28);
                $hashen = LogginnCtrl::genererHashMedSalt($passord,$saltet);
                $st->bindParam(':passord', $hashen);
                $st->bindParam(':salt', $saltet);
                $st->execute();

                $beskjed = "<html><body>Hei!<br/><br/>Du har fått opprettet en brukerkonto på 
<a href='https://intern.singsaker.no'>Singsaker sine internsider!</a> Velkommen skal du være.<br/>Brukernavn: $post[epost]<br/>Passord: $passord<br/><br/>Vi anbefaler deg å bytte passord. snarest
<br/><br/>Med vennlig hilsen<br/>Singsaker Internsider og Datagutta på Sing. <br/><br/>Denne melding var datagenerert. Noe galt? Vennligst si ifra til <a href='mailto:data@singsaker.no'>data@singsaker.no</a></body></html>";
                $tittel = "[SING-INTERN] Opprettelse av brukerkonto";
                $epost = new Epost($beskjed);
                $epost->addBrukerId($bruker_id);
                $epost->send($tittel);

                $beboer_id = Beboer::medBrukerId($bruker_id)->getId();
                $st_1 = DB::getDB()->prepare('INSERT INTO epost_pref (beboer_id,tildelt,snart_vakt,bytte,utleie,barvakt) VALUES(:id,1,1,1,1,1)');
                $st_1->bindParam(':id', $beboer_id);
                $st_1->execute();

                $st_2 = DB::getDB()->prepare('INSERT INTO prefs (beboerId, resepp, vinkjeller, pinboo, pinkode, vinpinboo, vinpin)
    VALUES(:id, 1, 1, 0, NULL, 0, NULL)');
                $st_2->bindParam(':id', $beboer_id);
                $st_2->execute();

            }

            $dok->vis('utvalg_romsjef_nybeboer.php');
        } else
            if ($aktueltArg == 'endrebeboer') {
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboer_id = $post['beboerid'];
                    $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id');
                    $st->bindParam(':id', $beboer_id);
                    $st->execute();
                    $raden = $st->fetchColumn();
                    $romhistorikk = Romhistorikk::fraJson($raden);
                    if ($romhistorikk != null && !($romhistorikk->getAktivRomId() == $post['rom_id'])) {
                        $romhistorikk->addPeriode($post['rom_id'], date('Y-m-d'), null);
                        $raden = $romhistorikk->tilJson();
                    }
                    $st = DB::getDB()->prepare('UPDATE beboer SET fornavn=:fornavn,mellomnavn=:mellomnavn,etternavn=:etternavn,
fodselsdato=:fodselsdato,adresse=:adresse,postnummer=:postnummer,telefon=:telefon,studie_id=:studie_id,skole_id=:skole_id,
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk WHERE id=:id');

                    $st->bindParam(':id', $beboer_id);
                    $st->bindParam(':fornavn', $post['fornavn']);
                    $st->bindParam(':mellomnavn', $post['mellomnavn']);
                    $st->bindParam(':etternavn', $post['etternavn']);
                    $st->bindParam(':fodselsdato', $post['fodselsdato']);
                    $st->bindParam(':adresse', $post['adresse']);
                    $st->bindParam(':postnummer', $post['postnummer']);
                    $st->bindParam(':telefon', $post['mobil']);
                    $st->bindParam(':studie_id', $post['studie_id']);
                    $st->bindParam(':skole_id', $post['skole_id']);
                    $st->bindParam(':klassetrinn', $post['klasse']);
                    $alko = (isset($post['alkodepositum']) && $post['alkodepositum'] == 'on') ? 1 : 0;
                    $st->bindParam(':alko', $alko);
                    $st->bindParam(':rolle', $post['rolle_id']);
                    $st->bindParam(':epost', $post['epost']);
                    $st->bindParam('romhistorikk', $raden);
                    $st->execute();
                }
                $beboerListe = BeboerListe::aktive();
                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg_romsjef_endrebeboer.php');
            } else if ($aktueltArg == 'endrebeboer_tabell') {
                $beboer = Beboer::medId($this->cd->getArg($this->cd->getAktuellArgPos() + 1));
                if ($beboer == null) {
                    exit();
                }
                $skoleListe = SkoleListe::alle();
                $studieListe = StudieListe::alle();
                $rolleListe = RolleListe::alle();
                $romListe = RomListe::alle();
                $dok = new Visning($this->cd);
                $dok->set('beboer', $beboer);
                $dok->set('skoleListe', $skoleListe);
                $dok->set('studieListe', $studieListe);
                $dok->set('rolleListe', $rolleListe);
                $dok->set('romListe', $romListe);
                $dok->vis('utvalg_romsjef_endrebeboer_tabell.php');
            } else if ($aktueltArg == 'endregammelbeboer') {
                if (isset($_POST)) {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    $beboer_id = $post['beboerid'];
                    $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id');
                    $st->bindParam(':id', $beboer_id);
                    $st->execute();
                    $raden = $st->fetchColumn();
                    $romhistorikk = Romhistorikk::fraJson($raden);
                    if ($romhistorikk != null && !($romhistorikk->getAktivRomId() == $post['rom_id'])) {
                        $romhistorikk->addPeriode($post['rom_id'], date('Y-m-d'), null);
                        $raden = $romhistorikk->tilJson();
                    }
                    $st = DB::getDB()->prepare('UPDATE beboer SET fornavn=:fornavn,mellomnavn=:mellomnavn,etternavn=:etternavn,
fodselsdato=:fodselsdato,adresse=:adresse,postnummer=:postnummer,telefon=:telefon,studie_id=:studie_id,skole_id=:skole_id,
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk WHERE id=:id');

                    $st->bindParam(':id', $beboer_id);
                    $st->bindParam(':fornavn', $post['fornavn']);
                    $st->bindParam(':mellomnavn', $post['mellomnavn']);
                    $st->bindParam(':etternavn', $post['etternavn']);
                    $st->bindParam(':fodselsdato', $post['fodselsdato']);
                    $st->bindParam(':adresse', $post['adresse']);
                    $st->bindParam(':postnummer', $post['postnummer']);
                    $st->bindParam(':telefon', $post['mobil']);
                    $st->bindParam(':studie_id', $post['studie_id']);
                    $st->bindParam(':skole_id', $post['skole_id']);
                    $st->bindParam(':klassetrinn', $post['klasse']);
                    $alko = $post['alkodepositum'] == 'on' ? 1 : 0;
                    $st->bindParam(':alko', $alko);
                    $st->bindParam(':rolle', $post['rolle_id']);
                    $st->bindParam(':epost', $post['epost']);
                    $st->bindParam('romhistorikk', $raden);
                    $st->execute();
                }
                $beboerListe = BeboerListe::ikkeAktive();
                $dok = new Visning($this->cd);
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg_romsjef_endregammelbeboer.php');
            } else if ($aktueltArg == 'endregammelbeboer_tabell') {
                $beboer = Beboer::medId($this->cd->getArg($this->cd->getAktuellArgPos() + 1));
                if ($beboer == null) {
                    exit();
                }
                $skoleListe = SkoleListe::alle();
                $studieListe = StudieListe::alle();
                $rolleListe = RolleListe::alle();
                $romListe = RomListe::alle();
                $dok = new Visning($this->cd);
                $dok->set('beboer', $beboer);
                $dok->set('skoleListe', $skoleListe);
                $dok->set('studieListe', $studieListe);
                $dok->set('rolleListe', $rolleListe);
                $dok->set('romListe', $romListe);
                $dok->vis('utvalg_romsjef_endregammelbeboer_tabell.php');
            } else if ($aktueltArg == 'gammelbeboer_tabell') {
                $dok = new Visning($this->cd);
                $sisteArg = $this->cd->getSisteArg();
                $beboerListe = BeboerListe::ikkeAktive();

                if ($sisteArg != $aktueltArg && is_numeric($sisteArg)) {
                    if (isset($_POST)) {
                        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                        $beboer_id = $post['beboerid'];
                        $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id');
                        $st->bindParam(':id', $beboer_id);
                        $st->execute();
                        $raden = $st->fetchColumn();
                        $romhistorikk = Romhistorikk::fraJson($raden);
                        if ($romhistorikk != null && !($romhistorikk->getAktivRomId() == $post['rom_id'])) {
                            $romhistorikk->addPeriode($post['rom_id'], date('Y-m-d'), null);
                            $raden = $romhistorikk->tilJson();
                        }
                        $st = DB::getDB()->prepare('UPDATE beboer SET fornavn=:fornavn,mellomnavn=:mellomnavn,etternavn=:etternavn,
fodselsdato=:fodselsdato,adresse=:adresse,postnummer=:postnummer,telefon=:telefon,studie_id=:studie_id,skole_id=:skole_id,
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk WHERE id=:id');

                        $st->bindParam(':id', $beboer_id);
                        $st->bindParam(':fornavn', $post['fornavn']);
                        $st->bindParam(':mellomnavn', $post['mellomnavn']);
                        $st->bindParam(':etternavn', $post['etternavn']);
                        $st->bindParam(':fodselsdato', $post['fodselsdato']);
                        $st->bindParam(':adresse', $post['adresse']);
                        $st->bindParam(':postnummer', $post['postnummer']);
                        $st->bindParam(':telefon', $post['mobil']);
                        $st->bindParam(':studie_id', $post['studie_id']);
                        $st->bindParam(':skole_id', $post['skole_id']);
                        $st->bindParam(':klassetrinn', $post['klasse']);
                        $alko = (isset($post['alkodepositum']) && $post['alkodepositum'] == 'on') ? 1 : 0;
                        $st->bindParam(':alko', $alko);
                        $st->bindParam(':rolle', $post['rolle_id']);
                        $st->bindParam(':epost', $post['epost']);
                        $st->bindParam('romhistorikk', $raden);
                        $st->execute();
                    }

                    $beboer = Beboer::medId($sisteArg);
                    if ($beboer != null || !in_array($beboer, $beboerListe)) {
                        $dok->set('skoleListe', $skoleListe);
                        $dok->set('studieListe', $studieListe);
                        $dok->set('rolleListe', $rolleListe);
                        $dok->set('romListe', $romListe);
                        $dok->set('beboer', $beboer);
                        $dok->vis('utvalg_romsjef_endre_denne_gamlebeboeren.php');
                        return;
                    }
                }
                $dok->set('beboerListe', $beboerListe);
                $dok->vis('utvalg_romsjef_gamlebeboer_tabell.php');
            } else if (is_numeric($aktueltArg)) {
                $beboer = Beboer::medId($aktueltArg);
                // Trenger feilhåndtering her.
                $dok = new Visning($this->cd);
                $dok->set('beboer', $beboer);
                $dok->vis('beboer_detaljer.php');
            } else {
                $dok = new Visning($this->cd);
                $dok->vis('utvalg_romsjef.php');
            }
    }
}

?>
