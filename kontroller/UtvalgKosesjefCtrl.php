<?php

namespace intern3;

class UtvalgKosesjefCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $aktueltArg = $this->cd->getAktueltArg();
        if ($aktueltArg == 'utleie') {
            $dok = new Visning($this->cd);
            if (isset($_POST)) {
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

                if (isset($post['fjern']) && isset($post['utleieid']) && is_numeric($post['utleieid'])) {
                    $utleieId = $post['utleieid'];
                    if ($post['fjern'] == 'utleie') {
                        $st = DB::getDB()->prepare('DELETE FROM utleie WHERE id=:id');
                        $st->bindParam(':id', $utleieId);
                        $st->execute();
                        $dok->set('slettetUtleie', 1);
                    } elseif ($post['fjern'] == 'beboer' && isset($post['beboerid']) && is_numeric($post['beboerid'])
                        && isset($post['felt']) && is_numeric($post['felt'])
                    ) {
                        $beboerId = $post['beboerid'];
                        $felt = $post['felt'];
                        $uteleiet = Utleie::medId($utleieId);
                        switch ($felt) {
                            case 1:
                                if ($uteleiet->getBeboer1()->getId() == $beboerId) {
                                    $uteleiet->setBeboer1(null);
                                }
                                break;
                            case 2:
                                if ($uteleiet->getBeboer2()->getId() == $beboerId) {
                                    $uteleiet->setBeboer2(null);
                                }
                                break;
                            case 3:
                                if ($uteleiet->getBeboer3()->getId() == $beboerId) {
                                    $uteleiet->setBeboer3(null);
                                }
                                break;
                        }
                        $dok->set('slettetBeboer', 1);
                    }
                }
                if (isset($post['dato']) && isset($post['rom']) && isset($post['leietaker']) && isset($post['leggtil'])) {
                    $st = DB::getDB()->prepare('INSERT INTO utleie (dato,navn,rom) VALUES(:dato,:navn,:rom)');
                    $st->bindParam(':dato', $post['dato']);
                    $st->bindParam(':navn', $post['leietaker']);
                    $st->bindParam(':rom', $post['rom']);
                    $st->execute();
                    $dok->set('success', 1);

                    $mottakere = "";
                    foreach (BeboerListe::aktive() as $beboer) {
                        if ($beboer->vilHaUtleieVarsel()) {
                            $mottakere .= $beboer->getEpost() . ",";
                        }
                    }
                    $df = new IntlDateFormatter('nb_NO',
                        IntlDateFormatter::TRADITIONAL, IntlDateFormatter::NONE,
                        'Europe/Oslo');

                    //$mottakere = rtrim($mottakere, ','); TODO UNCOMMENT
                    $mottakere = "data@singsaker.no";
                    $tittel = "[SING-INTERN] Kosesjef har lagt til et nytt utleie";
                    $tekst = "<html>(Dette er en automatisert melding. Du kan melde deg av på <a href='https://intern.singsaker.no'>Internsida</a>." .
                        "<br/><br/>Kosesjef har registrert et nytt utleie. Utleie skjer " . $df->format(date('Y-m-d',$post['dato'])) . " i " . $post['rom'] .
                        "<br/>Meld deg på som barvakt eller vasking mot regi på <a href='https://intern.singsaker.no'>Internsida</a>." .
                        "<br/><br/>";
                    Epost::sendEpost($mottakere,$tittel,$tekst);
                }
            }
            $utleier = Utleie::getUtleierFremover();
            $dok->set('utleier', $utleier);
            $dok->vis('utvalg_kosesjef_utleie.php');
        } else {
            $dok = new Visning($this->cd);
            $dok->vis('utvalg_kosesjef.php');
        }
    }
}

?>
