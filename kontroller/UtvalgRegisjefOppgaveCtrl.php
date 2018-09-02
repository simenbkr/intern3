<?php

namespace intern3;

class UtvalgRegisjefOppgaveCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        $dok = new Visning($this->cd);
        
        $aktueltArg = $this->cd->getAktueltArg();
        $sisteArg = $this->cd->getSisteArg();
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        
        if ($aktueltArg != $sisteArg || (empty($aktueltArg) && empty($sisteArg))) {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                if (isset($post['godkjenn']) && ($oppgaven = Oppgave::medId($post['godkjenn'])) !== null) {
                    Oppgave::endreGodkjent($post['godkjenn'], 1);

                    $tittel = "[SING-INTERN] Din Oppgave ble godkjent!";
                    $beskjed = "<html><body>Hei<br/><br/>Din oppgave med tittel " . $oppgaven->getNavn();
                    $beskjed .= "har blitt godkjent. Husk å føre timene på Internsiden.<br/><br/>Med vennlig hilsen";
                    $beskjed .= "<br/>Singsaker Internside</body></html>";

                    foreach($oppgaven->getPameldteBeboere() as $beboer){
                        /* @var \intern3\Beboer $beboer */
                        if($beboer->vilHaVaktVarsler()){
                            Epost::sendEpost($beboer->getEpost(), $tittel, $beskjed);
                        }
                    }


                } elseif (isset($post['fjern'])) {
                    Oppgave::endreGodkjent($post['fjern'], 0);
                } elseif (isset($post['frys'])) {
                    Oppgave::medId($post['frys'])->setFryst();
                } elseif (isset($post['afrys'])) {
                    Oppgave::medId($post['afrys'])->unFrys();
                } elseif (isset($post['registrer'])) {
                    if (isset($post['navn'])  && isset($post['timer']) && isset($post['personer']) && isset($post['beskrivelse'])
                        && $post['navn'] != null && $post['timer'] != null && $post['personer'] != null && $post['beskrivelse'] != null
                    ) {
                        $navn = $post['navn'];
                        $pri = 1;
                        $anslagtid = $post['timer'];
                        $anslagpers = $post['personer'];
                        $beskrivelse = $post['beskrivelse'];
                        $tildelte = $post['tildelte'];
                        
                        
                        if (empty($post['dato'])) {
                            Oppgave::AddOppgave($navn, $pri, $anslagtid, $anslagpers, $beskrivelse);
                        } else {
                            Oppgave::AddOppgave($navn, $pri, $anslagtid, $anslagpers, $beskrivelse, $post['dato']);
                        }
                        
                        $aktuellOppgave = Oppgave::getSiste();
                        $aktuellOppgave->settLag($tildelte);
                        
                        if ($post['epost'] !== 1) {
                            $tittel = "[SING-INTERN] Du er satt opp på en ny regi-oppgave!";
                            $beskjed = "<html><body>Hei!<br/><br/>Du er satt opp på en ny regi-oppgave. Beskrivelse følger:<br/>";
                            $beskjed .= "<h3>$navn</h3><br/>$beskrivelse";
                            
                            if(!empty($post['dato'])){
                                $beskjed .= "<br/>Oppgaven skal utføres " . $post['dato'] . ".";
                            }
                            
                            $beskjed .= "<br/><br/>Med vennlig hilsen <br/>Singsaker Internside";
                            $beskjed .= "<br/><br/>Hvis denne e-posten er sendt feil, vennligst ta kontakt med data@singsaker.no</body></html>";
                            
                            foreach ($tildelte as $beboer_id) {
                                $beboer = Beboer::medId($beboer_id);
                                Epost::sendEpost($beboer->getEpost(), $tittel, $beskjed);
                            }
                        }
                        
                        header('Location:' . $_SERVER['REQUEST_URI']);
                        exit();
                    } else {
                        $dok->set('feilSubmit', 1);
                    }
                    
                } //data: 'slett=' + id,
                elseif (isset($post['slett']) && is_numeric($post['slett'])) {
                    $id = $post['slett'];
                    $oppgaven = Oppgave::medId($id);
                    $st = DB::getDB()->prepare('DELETE FROM oppgave WHERE id=:id');
                    $st->bindParam(':id', $id);
                    $st->execute();
                    $_SESSION['success'] = 1; //('slettet', 1);
                    $_SESSION['msg'] = "Du slettet oppgaven med navn" . $oppgaven->getNavn();
                    header('Location:' . $_SERVER['REQUEST_URI']);
                    exit();
                } elseif (isset($post['fjernFraOppgave']) && is_numeric($post['fjernFraOppgave']) &&
                    ($beboeren = Beboer::medId($post['fjernFraOppgave'])) != null && isset($post['oppgaveId'])
                    && ($oppgaven = Oppgave::medId($post['oppgaveId'])) != null
                ) {
                    
                    $oppgaven->fjernPerson($beboeren->getId());
                }
            }
            $regilister = Regiliste::getAlleLister();
            $oppgaveListe = OppgaveListe::alle();
            $beboerListe = BeboerListe::aktiveMedRegi();
            $dok->set('regilister', $regilister);
            $dok->set('beboerListe', $beboerListe);
            $dok->set('oppgaveListe', $oppgaveListe);
            $dok->vis('utvalg/regisjef/utvalg_regisjef_oppgave.php');
            $dok->set('feilSubmit', null);
        } elseif($sisteArg === 'forslag' && $_SERVER['REQUEST_METHOD'] === 'POST'){
        
            $antall = $post['antall'];
            $kandidater = BeboerListe::aktiveMedRegiTilDisp();

            if(($regilisten = Regiliste::medId($post['regiliste_id'])) !== null){
                $kandidater = $regilisten->getDisponibelBeboerliste();
            }
            
            if(empty($antall) || $antall === null || !is_numeric($antall) || $antall < 1){
                $antall = 1;
            }

            if($antall >= count($kandidater)){
                $antall = count($kandidater);
            }
            
            $keys = array_rand($kandidater, $antall);
            
            if ($antall == 1){
                $keys = array($keys);
            }
            
            $forslag = array();
            foreach($keys as $key){
                $beboer = $kandidater[$key];
                
                $forslag[] = array(
                    'id' => $beboer->getId(),
                    'navn' => $beboer->getFulltNavn()
                );
            };
            
            print json_encode($forslag, true);
        
        }
        elseif (is_numeric($sisteArg) && ($oppgaven = Oppgave::medId($sisteArg)) !== null) {
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                if (($beboer = Beboer::medId($post['beboer'])) !== null) {
                    $oppgaven->meldPa($post['beboer']);
                    Funk::setSuccess("La til " . $beboer->getFulltNavn() . " til oppgaven.");
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    exit();
                } elseif( isset($post['navn']) && isset($post['beskrivelse']) && isset($post['dato'])
                    && isset($post['anslag_timer']) && isset($post['anslag_personer'])){
                    
                    $oppgaven->setNavn($post['navn']);
                    $oppgaven->setTidutfore($post['dato']);
                    $oppgaven->setAnslagTimer($post['anslag_timer']);
                    $oppgaven->setAnslagPersoner($post['anslag_personer']);
                    
                    if(strlen($post['beskrivelse']) > 1){
                        $oppgaven->setBeskrivelse($post['beskrivelse']);
                    }
                    
                    Funk::setSuccess("Endra oppgaven!");
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                    
                } elseif( isset($post['send_epost']) && $post['send_epost'] === 1){

                    $navn = $oppgaven->getNavn();
                    $beskrivelse = $oppgaven->getBeskrivelse();
                    $dato = $oppgaven->getTidUtfore();

                    $tittel = "[SING-INTERN] Du er satt opp på en ny regi-oppgave!";
                    $beskjed = "<html><body>Hei!<br/><br/>Du er satt opp på en ny regi-oppgave. Beskrivelse følger:<br/>";
                    $beskjed .= "<h3>$navn</h3><br/>$beskrivelse";

                    if(!empty($dato) && $dato !== null){
                        $beskjed .= "<br/>Oppgaven skal utføres " . $dato . ".";
                    }

                    $beskjed .= "<br/><br/>Med vennlig hilsen <br/>Singsaker Internside";
                    $beskjed .= "<br/><br/>Hvis denne e-posten er sendt feil, vennligst ta kontakt med data@singsaker.no</body></html>";

                    foreach ($oppgaven->getPameldteBeboere() as $beboer) {
                        Epost::sendEpost($beboer->getEpost(), $tittel, $beskjed);
                    }


                }
                
            }
            
            $dok->set('oppgaven', $oppgaven);
            $dok->vis('utvalg/regisjef/utvalg_regisjef_oppgave_detaljer.php');
        }
        
    }
}

?>
