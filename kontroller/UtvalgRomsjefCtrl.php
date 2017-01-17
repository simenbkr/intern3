<?php

namespace intern3;

class UtvalgRomsjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        //TODO Gjør om dette til Switch, i stedet for if-else?
        if ($aktueltArg == 'beboerliste') {
            $beboerListe = BeboerListe::aktive();
            $dok = new Visning($this->cd);
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

                $st = DB::getDB()->prepare('INSERT INTO bruker (id,passord) VALUES(:id,:passord)');
                $st->bindParam(':id', $bruker_id);
                $passord = Funk::generatePassword();

                $hashen = LogginnCtrl::genererHash($passord);
                $st->bindParam(':passord', $hashen);
                $st->execute();

                $beskjed = "<html><body>Hei!<br/><br/>Du har fått opprettet en brukerkonto på 
<a href='https://intern.singsaker.no'>Singsaker sine internsider!</a> Velkommen skal du være.<br/>Brukernavn: $post[epost]<br/>Passord: $passord<br/><br/>Vi anbefaler deg å bytte passord. snarest
<br/><br/>Med vennlig hilsen<br/>Singsaker Internsider og Datagutta på Sing. <br/><br/>Denne melding var datagenerert. Noe galt? Vennligst si ifra til <a href='mailto:data@singsaker.no'>data@singsaker.no</a></body></html>";
                $tittel = "[SING-INTERN] Opprettelse av brukerkonto";
                $epost = new Epost($beskjed);
                $epost->addBrukerId($bruker_id);
                $epost->send($tittel);

            }
            $skoleListe = SkoleListe::alle();
            $studieListe = StudieListe::alle();
            $rolleListe = RolleListe::alle();
            $romListe = RomListe::alle();
            $dok = new Visning($this->cd);
            $dok->set('skoleListe', $skoleListe);
            $dok->set('studieListe', $studieListe);
            $dok->set('rolleListe', $rolleListe);
            $dok->set('romListe', $romListe);
            $dok->vis('utvalg_romsjef_nybeboer.php');
        } else if ($aktueltArg == 'endrebeboer') {
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
