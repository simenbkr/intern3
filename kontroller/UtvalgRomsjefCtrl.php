<?php

namespace intern3;

class UtvalgRomsjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'beboerliste') {
            $beboerListe = BeboerListe::aktive();
            $dok = new Visning($this->cd);
            $dok->set('beboerListe', $beboerListe);
            $dok->vis('utvalg_romsjef_beboerliste.php');
        } else if ($aktueltArg == 'nybeboer') {
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
            if(isset($_POST)){
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                $beboer_id = $post['beboerid'];
                $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id');
                $st->bindParam(':id',$beboer_id);
                $st->execute();
                $raden = $st->fetchColumn();
                $romhistorikk = Romhistorikk::fraJson($raden);
                if($romhistorikk != null && !($romhistorikk->getAktivRomId() == $post['rom_id'])){
                    $romhistorikk->addPeriode($post['rom_id'],date('Y-m-d'),null);
                    $raden = $romhistorikk->tilJson();
                }
                $st = DB::getDB()->prepare('UPDATE beboer SET fornavn=:fornavn,mellomnavn=:mellomnavn,etternavn=:etternavn,
fodselsdato=:fodselsdato,adresse=:adresse,postnummer=:postnummer,telefon=:telefon,studie_id=:studie_id,skole_id=:skole_id,
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk WHERE id=:id');

                $st->bindParam(':id',$beboer_id);
                $st->bindParam(':fornavn',$post['fornavn']);
                $st->bindParam(':mellomnavn',$post['mellomnavn']);
                $st->bindParam(':etternavn',$post['etternavn']);
                $st->bindParam(':fodselsdato',$post['fodselsdato']);
                $st->bindParam(':adresse',$post['adresse']);
                $st->bindParam(':postnummer',$post['postnummer']);
                $st->bindParam(':telefon',$post['mobil']);
                $st->bindParam(':studie_id',$post['studie_id']);
                $st->bindParam(':skole_id',$post['skole_id']);
                $st->bindParam(':klassetrinn',$post['klassetrinn']);
                $alko = $post['alkodepositum'] == 'on' ? 1 : 0;
                $st->bindParam(':alko',$alko);
                $st->bindParam(':rolle',$post['rolle_id']);
                $st->bindParam(':epost',$post['epost']);
                $st->bindParam('romhistorikk',$raden);
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
