<?php

namespace intern3;

class UtvalgRomsjefCtrl extends AbstraktCtrl
{

    private function visEndreTabell($dok, $beboer)
    {

        $dok->set('beboer', $beboer);
        $skoleListe = SkoleListe::alle();
        $studieListe = StudieListe::alle();
        $rolleListe = RolleListe::alle();
        $romListe = RomListe::alle();
        $dok->set('skoleListe', $skoleListe);
        $dok->set('studieListe', $studieListe);
        $dok->set('rolleListe', $rolleListe);
        $dok->set('romListe', $romListe);
        $dok->vis('utvalg/romsjef/utvalg_romsjef_endre_denne_beboeren.php');
        return;
    }

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


                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    //data: 'flyttut=1&beboerId=' + beboerId,
                    if ($post['flyttut'] == 1 && ($beboer = Beboer::medId($post['beboerId'])) !== null) {
                        $beboer->flyttUt();
                        Funk::setSuccess("Du flyttet ut denne beboeren!");
                        exit();
                    } else {
                        $this->endreBeboer();
                        Funk::setSuccess("Du endret denne beboeren!");
                        header('Location: ?a=utvalg/romsjef/beboerliste/' . $post['beboerid']);
                        exit();
                    }
                }

                $beboer = Beboer::medId($sisteArg);
                if ($beboer != null || !in_array($beboer, $aktive_beboere)) {
                    $this->visEndreTabell($dok, $beboer);
                    exit();
                }


            }
            $beboerListe = BeboerListe::aktive();

            $fullvakt = 0;
            $fullregi = 0;
            $halv = 0;

            foreach ($beboerListe as $beboer) {
                /* @var Beboer $beboer */

                if ($beboer->getRolle() == null) {
                    continue;
                }

                switch ($beboer->getRolle()->getRegitimer()) {
                    case 0:
                        $fullvakt++;
                        break;
                    case 18:
                        $halv++;
                        break;
                    case 48:
                        $fullregi++;
                        break;
                }
            }

            $dok->set('beboerListe', $beboerListe);
            $dok->set('fullregi', $fullregi);
            $dok->set('fullvakt', $fullvakt);
            $dok->set('halv', $halv);

            $skoleListe = SkoleListe::alle();
            $studieListe = StudieListe::alle();
            $rolleListe = RolleListe::alle();
            $romListe = RomListe::alle();
            $dok->set('skoleListe', $skoleListe);
            $dok->set('studieListe', $studieListe);
            $dok->set('rolleListe', $rolleListe);
            $dok->set('romListe', $romListe);
            $dok->set('showTable', 1);
            $dok->vis('utvalg/romsjef/utvalg_romsjef_beboerliste.php');
            exit();

        } else if($aktueltArg == 'eksporter') {

            $beboerListe = BeboerListe::aktive();
            $csv = BeboerListe::tilCSV($beboerListe);
            header("Content-type: text/csv");
            $ts = date('Y-m-d_H_i_s');
            header("Content-Disposition: attachment; filename=beboerliste-$ts.csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $output = fopen('php://output', 'wb');
            foreach($csv as $line) {
                fputcsv($output, $line);
            }

            fclose($output);
            return;

        } else if ($aktueltArg == 'flyttinn') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (($beboer = Beboer::medId($post['id'])) != null && !in_array($beboer, BeboerListe::aktive())) {
                $beboer->flyttInn();

                $_SESSION['success'] = 1;
                $_SESSION['msg'] = "Du flyttet inn beboeren " . $beboer->getFulltNavn() . ' igjen!';
            } else {
                $_SESSION['error'] = 1;
                $_SESSION['msg'] = "Hmm.. Noe galt skjedde - kan det hende beboeren er alt innflyttet?";
            }
        } else if ($aktueltArg == 'nybeboer') {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                $st = DB::getDB()->prepare('SELECT epost FROM beboer WHERE epost=:epost');
                $st->bindParam(':epost', $post['epost']);
                $st->execute();

                if ($st->rowCount() > 0) {
                    Funk::setError("En beboer har allerede denne epost-adressen! Sjekk 'Gamle beboere' før du oppretter ny beboer!");
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }


                $values = array('fornavn' => 'fornavn', 'etternavn' => 'etternavn',
                    'fodselsdato' => 'fødselsdato', 'adresse' => 'adresse', 'postnummer' => 'postnummer',
                    'mobil' => 'mobil', 'studie_id' => 'studie', 'skole_id' => 'skole', 'klasse' => 'klasse',
                    'rolle_id' => 'rolle', 'epost' => 'e-post', 'rom_id' => 'rom');

                foreach ($values as $value => $key) {

                    if (!isset($post[$value])) {
                        Funk::setError("Oops! Det ser ut til at du mangler " . $values[$key]);
                        $dok->vis('utvalg/romsjef/utvalg_romsjef_nybeboer.php');
                        exit();
                    }
                }

                $alko = (isset($post['alkodepositum']) && $post['alkodepositum'] == 'on') ? 1 : 0;
                $beboer = Beboer::nyBeboer(
                    $post['fornavn'], $post['mellomnavn'], $post['etternavn'], $post['fodselsdato'],
                    $post['adresse'], $post['postnummer'], $post['mobil'], $post['studie_id'], $post['skole_id'],
                    $post['klasse'], $alko, $post['rolle_id'], $post['epost'], $post['rom_id'], $post['kjonn']);

                header('Location: ?a=utvalg/romsjef/beboerliste/' . $beboer->getId());
                exit();
            }

            $dok->vis('utvalg/romsjef/utvalg_romsjef_nybeboer.php');


        } else if ($aktueltArg == 'gammelbeboer_tabell') {
            $dok = new Visning($this->cd);
            $beboerListe = BeboerListe::ikkeAktive();

            $dok->set('beboerListe', $beboerListe);

            $skoleListe = SkoleListe::alle();
            $studieListe = StudieListe::alle();
            $rolleListe = RolleListe::alle();
            $romListe = RomListe::alle();
            $dok->set('skoleListe', $skoleListe);
            $dok->set('studieListe', $studieListe);
            $dok->set('rolleListe', $rolleListe);
            $dok->set('romListe', $romListe);

            $dok->vis('utvalg/romsjef/utvalg_romsjef_beboerliste.php');
        } else if ($aktueltArg == 'epost') {
            $valgtCtrl = new UtvalgRomsjefEpostCtrl($this->cd->skiftArg());
            return $valgtCtrl->bestemHandling();
        } else if ($aktueltArg == 'ansiennitet') {
            $valgtCtrl = new UtvalgRomsjefAnsiennitetCtrl($this->cd->skiftArg());
            return $valgtCtrl->bestemHandling();
        } else if ($aktueltArg == 'storhybel') {
            $valgtCtrl = new UtvalgRomsjefStorhybelCtrl($this->cd->skiftArg());
            return $valgtCtrl->bestemHandling();
        } else if ($aktueltArg == 'soknad') {
            $valgtCtrl = new UtvalgRomsjefSoknadCtrl($this->cd->skiftArg());
            return $valgtCtrl->bestemHandling();
        } else if (is_numeric($aktueltArg)) {
            $beboer = Beboer::medId($aktueltArg);
            // Trenger feilhåndtering her.
            $dok = new Visning($this->cd);
            $dok->set('beboer', $beboer);

            $dok->vis('beboer/beboer_detaljer.php');
        } else {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg/romsjef/utvalg_romsjef.php');
        }
    }

    private function endreBeboer()
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $beboer_id = $post['beboerid'];
        $beboer = Beboer::medId($beboer_id);

        $st = DB::getDB()->prepare('SELECT count(*) as cnt FROM beboer WHERE (epost=:epost AND id != :id)');
        $st->execute(['epost' => $post['epost'], 'id' => $beboer_id]);

        if ($st->fetch()['cnt'] > 0) {
            Funk::setError("En beboer har allerede denne epost-adressen! Sjekk 'Gamle beboere' før du oppretter ny beboer!");
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }


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
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk,kjonn=:kjonn WHERE id=:id');

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
        $st->bindParam(':kjonn', $post['kjonn']);
        $st->bindParam(':romhistorikk', $raden);

        if($beboer->getKjonn() != ($kjonn = Beboer::$MULIGE_KJONN[$post['kjonn']])) {
            $beboer->updateLists('', false, true);
        }

        if($beboer->getEpost() != $post['epost']) {
            $beboer->updateLists($post['epost'], true);
        }

        $st->execute();

    }

}