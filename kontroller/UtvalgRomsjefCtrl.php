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
        switch ($aktueltArg) {
            case 'endrebilde':
                if($_SERVER['REQUEST_METHOD'] === 'POST' && ($beboer = Beboer::medId($this->cd->getSisteArg()))) {
                    $this->endreBilde($beboer);
                    header('Location: ?a=utvalg/romsjef/beboerliste/' . $beboer->getId());
                    exit();
                }
                header('Location: ?a=utvalg/romsjef/beboerliste');
            break;
            case 'beboerliste':
                $dok = new Visning($this->cd);
                $sisteArg = $this->cd->getSisteArg();

                if(isset($_POST['studienavn']) && !(Studie::finnesStudie($_POST['studienavn']))){
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                    Studie::nyttStudie($post['studienavn']);
                    Funk::setSuccess('Nytt studie ble lagt til');
                } else if (isset($_POST['studienavn']) && Studie::finnesStudie($_POST['studienavn'])){
                    Funk::setError('Studiet finnes allerede');
                }

                if ($_POST['slett'] == 'Slett') {
                    if ($_POST['studie_id'] == '23' || $_POST['studie_id'] == '0') {
                        Funk::setError('Denne kan ikke slettes!');
                    } else if (!(Studie::brukesStudie($_POST['studie_id']))) {
                        $studienavn = Studie::medId($_POST['studie_id'])->getNavn();
                        Studie::slettStudie($_POST['studie_id']);
                        Funk::setSuccess($studienavn . ' ble slettet');

                    } else {
                        Funk::setError('Studie er i bruk og kan ikke slettes');

                    }
                } else if ($_POST['endre'] == 'Endre navn') {
                    if ($_POST['nyttStudienavn'] !== "" && $_POST['studie_id'] !== "23" && $_POST['studie_id'] !== "0") {
                        $gammeltNavn = Studie::medId($_POST['studie_id'])->getNavn();
                        Studie::endreStudie($_POST['studie_id'], $_POST['nyttStudienavn']);
                        Funk::setSuccess('"' . $gammeltNavn . '" endret navn til "' . $_POST['nyttStudienavn'] . '"');

                    } else {
                        Funk::setError('Ugyldig input eller studie');

                    }
                }

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
                break;
            case 'eksporter':
                $beboerListe = BeboerListe::aktive();
                $csv = BeboerListe::tilCSV($beboerListe);
                header("Content-type: text/csv");
                $ts = date('Y-m-d_H_i_s');
                header("Content-Disposition: attachment; filename=beboerliste-$ts.csv");
                header("Pragma: no-cache");
                header("Expires: 0");

                $output = fopen('php://output', 'wb');
                foreach ($csv as $line) {
                    fputcsv($output, $line);
                }

                fclose($output);
                break;
            case 'flyttinn':
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                if (($beboer = Beboer::medId($post['id'])) != null && !in_array($beboer, BeboerListe::aktive())) {
                    $beboer->flyttInn();

                    $_SESSION['success'] = 1;
                    $_SESSION['msg'] = "Du flyttet inn beboeren " . $beboer->getFulltNavn() . ' igjen!';
                } else {
                    $_SESSION['error'] = 1;
                    $_SESSION['msg'] = "Hmm.. Noe galt skjedde - kan det hende beboeren er alt innflyttet?";
                }
                break;
            case 'nybeboer':
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

                    $values = array(
                        'fornavn' => 'fornavn',
                        'etternavn' => 'etternavn',
                        'fodselsdato' => 'fødselsdato',
                        'adresse' => 'adresse',
                        'postnummer' => 'postnummer',
                        'mobil' => 'mobil',
                        'studie_id' => 'studie',
                        'skole_id' => 'skole',
                        'klasse' => 'klasse',
                        'rolle_id' => 'rolle',
                        'epost' => 'e-post',
                        'rom_id' => 'rom'
                    );

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
                break;
            case 'gammelbeboer_tabell':
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
                break;
            case 'epost':
                $valgtCtrl = new UtvalgRomsjefEpostCtrl($this->cd->skiftArg());
                return $valgtCtrl->bestemHandling();
            case 'veteran':
                $valgtCtrl = new UtvalgRomsjefVeteranCtrl($this->cd->skiftArg());
                return $valgtCtrl->bestemHandling();
            case 'ansiennitet':
                $valgtCtrl = new UtvalgRomsjefAnsiennitetCtrl($this->cd->skiftArg());
                return $valgtCtrl->bestemHandling();
            case 'storhybel':
                $valgtCtrl = new UtvalgRomsjefStorhybelCtrl($this->cd->skiftArg());
                return $valgtCtrl->bestemHandling();
            case 'soknad':
                $valgtCtrl = new UtvalgRomsjefSoknadCtrl($this->cd->skiftArg());
                return $valgtCtrl->bestemHandling();
            case is_numeric($aktueltArg):
                $beboer = Beboer::medId($aktueltArg);
                // Trenger feilhåndtering her.
                $dok = new Visning($this->cd);
                $dok->set('beboer', $beboer);

                $dok->vis('beboer/beboer_detaljer.php');
                break;
            default:
                $dok = new Visning($this->cd);
                $dok->vis('utvalg/romsjef/utvalg_romsjef.php');
                break;

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

        if ($beboer->getKjonn() != ($kjonn = Beboer::$MULIGE_KJONN[$post['kjonn']])) {
            $beboer->updateLists('', false, true);
        }

        if ($beboer->getEpost() != $post['epost']) {
            $beboer->updateLists($post['epost'], true);
        }

        $st->execute();
    }

    private function endreBilde(Beboer $beboer)
    {
        $tillatte_filtyper = array('jpg', 'jpeg', 'png', 'gif');
        if (isset($_FILES['image'])) {

            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            $tmp_file = $_FILES['image']['tmp_name'];
            $file_ext = strtolower(end(explode('.', $_FILES['image']['name'])));

            if (in_array($file_ext, $tillatte_filtyper) && $file_size > 10 && $file_size < 1000000000) {
                $bildets_navn = md5($file_name . Funk::generatePassword(15) . time()) . '.' . $file_ext;
                move_uploaded_file($tmp_file, "profilbilder/" . $bildets_navn);
                chmod("profilbilder/" . $bildets_navn, 0644);
                $id = $beboer->getId();
                $st = DB::getDB()->prepare('UPDATE beboer SET bilde=:bilde WHERE id=:id');
                $st->execute(['bilde' => $bildets_navn, 'id' => $id]);
            } else {
                Funk::setError("Det var ikke et gyldig bilde!");
            }
        } else {
            Funk::setError("Du valgte ikke et bilde!");
        }
        return;
    }

}

