<?php

namespace intern3;

class Beboer implements Person
{
    private $id;
    private $brukerId;
    private $fornavn;
    private $mellomnavn;
    private $etternavn;
    private $fodselsdato;
    private $adresse;
    private $postnummer;
    private $telefon;
    private $studieId;
    private $skoleId;
    private $klassetrinn;
    private $alkoholdepositum;
    private $rolleId;
    private $epost;
    private $romhistorikk;
    private $bilde;
    private $ansiennitet;
    private $antall_kjipe;

    // Latskap
    private $studie;
    private $skole;
    private $romId;
    private $rom;
    private $romhistorikkObjekt;
    private $rolle;
    private $vervListe;
    private $utvalgVervListe;
    private $bruker;

    public static function medId($id)
    {
        $st = DB::getDB()->prepare('SELECT * FROM beboer WHERE id=:id;');
        $st->bindParam(':id', $id);
        $st->execute();
        return self::init($st);
    }

    public static function medBrukerId($brukerId)
    {
        $st = DB::getDB()->prepare('SELECT * FROM beboer WHERE bruker_id=:brukerId;');
        $st->bindParam(':brukerId', $brukerId);
        $st->execute();
        return self::init($st);
    }

    private static function init(\PDOStatement $st)
    {
        $rad = $st->fetch();
        if ($rad == null) {
            return null;
        }
        $instance = new self();
        $instance->id = $rad['id'];
        $instance->brukerId = $rad['bruker_id'];
        $instance->fornavn = $rad['fornavn'];
        $instance->mellomnavn = $rad['mellomnavn'];
        $instance->etternavn = $rad['etternavn'];
        $instance->fodselsdato = $rad['fodselsdato'];
        $instance->adresse = $rad['adresse'];
        $instance->postnummer = $rad['postnummer'];
        $instance->telefon = $rad['telefon'];
        $instance->studieId = $rad['studie_id'];
        $instance->skoleId = $rad['skole_id'];
        $instance->klassetrinn = $rad['klassetrinn'];
        $instance->alkoholdepositum = $rad['alkoholdepositum'];
        $instance->rolleId = $rad['rolle_id'];
        $instance->epost = $rad['epost'];
        $instance->romhistorikk = $rad['romhistorikk'];
        $instance->bilde = $rad['bilde'];
        $instance->ansiennitet = $rad['ansiennitet'];
        $instance->studie = null;
        $instance->skole = null;
        $instance->romId = null;
        $instance->rom = null;
        $instance->romhistorikkObjekt = null;
        $instance->bruker = null;
        $instance->antall_kjipe = -1;
        return $instance;
    }

    public function erBeboer()
    {
        $id = $this->getId();
        $st = DB::getDB()->prepare('SELECT romhistorikk FROM beboer WHERE id=:id LIMIT 1');
        $st->bindParam(':id', $id);
        $st->execute();

        $romhistorikk = $st->fetchColumn();

        $dec = json_decode($romhistorikk, true);

        foreach ($dec as $romInfo) {
            if ($romInfo['utflyttet'] == null) {
                return true;
            }
        }
        return false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBrukerId()
    {
        return $this->brukerId;
    }

    public function setBrukerId($bruker_id){
        $this->brukerId = $bruker_id;
        $this->oppdater();
    }

    public function getFornavn()
    {
        return $this->fornavn;
    }

    public function setFornavn($fornavn){
        $this->fornavn = $fornavn;
        $this->oppdater();
    }

    public function getMellomnavn()
    {
        return $this->mellomnavn;
    }

    public function setMellomnavn($mellomnavn){
        $this->mellomnavn = $mellomnavn;
        $this->oppdater();
    }

    public function getEtternavn()
    {
        return $this->etternavn;
    }

    public function setEtternavn($etternavn){
        $this->etternavn = $etternavn;
        $this->oppdater();
    }

    public function getFulltNavn()
    {
        return trim(preg_replace('/[\s]{2,}/', ' ', $this->fornavn . ' ' . $this->mellomnavn . ' ' . $this->etternavn));
    }

    public function getFodselsdato()
    {
        return $this->fodselsdato;
    }

    public function setFodselsdato($dato){
        $this->fodselsdato = date('Y-m-d', $dato);
        $this->oppdater();
    }

    public function getBilde()
    {
        return $this->bilde;
    }

    public function setBilde($bilde){
        $this->bilde = $bilde;
        $this->bilde = $bilde;
    }

    public function getFodselsar()
    {
        return substr($this->fodselsdato, 0, 4);
    }

    public function getAlderIAr()
    {
        return date('Y') - substr($this->fodselsdato, 0, 4);
    }

    public function getAlder()
    {
        return $this->getAlderIAr() - (($_SERVER['REQUEST_TIME'] - mktime(0, 0, 0, substr($this->fodselsdato, 5, 2), substr($this->fodselsdato, 8, 2))) < 0 ? 1 : 0);
    }

    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($adresse){
        $this->adresse = $adresse;
        $this->oppdater();
    }

    public function getPostnummer()
    {
        return $this->postnummer;
    }

    public function setPostnummer($nr){
        $this->postnummer = $nr;
        $this->oppdater();
    }

    public function getAnsiennitet() : int
    {
        if(is_numeric($this->ansiennitet)){
            return $this->ansiennitet;
        }
        return 0;
    }

    public function setAnsiennitet($ans){
        $this->ansiennitet = $ans;
        $this->oppdater();
    }
    
    public function inkrementerAnsiennitet(){
        $this->ansiennitet++;
        $this->oppdater();
    }

    public function getTelefon()
    {
        if (strlen($this->telefon) > 8) {
            return substr($this->telefon, 0, strlen($this->telefon) - 8) . ' ' . substr($this->telefon, -8);
        }
        return $this->telefon;
    }

    public function setTelefon($tel){
        $this->telefon = $tel;
        $this->oppdater();
    }

    public function getKlassetrinn()
    {
        return $this->klassetrinn;
    }

    public function setKlassetrinn($trinn){
        $this->klassetrinn = $trinn;
        $this->oppdater();
    }

    public function harAlkoholdepositum()
    {
        return $this->alkoholdepositum > 0;
    }

    public function setAlko($alko){
        $this->alkoholdepositum = $alko;
        $this->oppdater();
    }

    public function getStudieId()
    {
        return $this->studieId;
    }

    public function setStudieId($id){
        $this->studieId = $id;
        $this->oppdater();
        $this->studie = Studie::medId($id);
    }

    public function getStudie()
    {
        if ($this->studie == null) {
            $this->studie = Studie::medId($this->studieId);
        }
        return $this->studie;
    }

    public function getSkoleId()
    {
        return $this->skoleId;
    }

    public function setSkoleId($id){
        $this->skoleId = $id;
        $this->oppdater();
        $this->skole = Skole::medId($id);
    }

    public function getSkole()
    {
        if ($this->skole == null) {
            $this->skole = Skole::medId($this->skoleId);
        }
        return $this->skole;
    }

    public function getEpost()
    {
        return $this->epost;
    }

    public function setEpost($epost){
        if(Funk::isValidEmail($epost)) {
            $this->epost = $epost;
            $this->oppdater();
        }
    }

    public function getRolleId()
    {
        return $this->rolleId;
    }

    public function setRolleId($id){
        $this->rolleId = $id;
        $this->oppdater();
        $this->rolle = Rolle::medId($id);
    }

    public function getRolle()
    {
        if ($this->rolle == null) {
            $this->rolle = Rolle::medId($this->rolleId);
        }
        return $this->rolle;
    }

    public function getRomId()
    {
        if ($this->romId == null) {
            $this->romId = $this->getRomhistorikk()->getAktivRomId();
        }
        return $this->romId;
    }

    public function setRomId($id){
        $this->romId = $id;
        $this->oppdater();
        $this->rom = Rom::medId($id);
    }

    public function getRom()
    {
        if ($this->rom == null) {
            $this->rom = Rom::medId($this->getRomId());
        }
        return $this->rom;
    }

    public function getRomhistorikk()
    {
        if ($this->romhistorikkObjekt == null) {
            $this->romhistorikkObjekt = Romhistorikk::fraJson($this->romhistorikk);
        }
        return $this->romhistorikkObjekt;
    }

    public function flyttUt()
    {
        /*
         * Flytter ut beboer. Setter siste romhistorikk til (ikke null).
         * Fjerner fra evt åpmandsverv.
         * Fjerner fra evt vakter. <- NEI! (Vaktsjef) - Beboeren skal sitte vakter den er satt opp på, uavhengig
         * om den flytter ut.
         * Fjerner vaktbytter på vakter tilhørende denne beboeren.
         */

        $romhistorikken = $this->romhistorikk;
        $som_array = json_decode($romhistorikken, true);
        foreach ($som_array as $key => $historikk) {
            if ($historikk['utflyttet'] == null) {
                $som_array[$key]['utflyttet'] = date('Y-m-d');
            }
        }
        $som_json_igjen = json_encode($som_array);

        $st = DB::getDB()->prepare('UPDATE beboer SET romhistorikk=:historikk WHERE id=:id');
        $id = $this->getId();
        $st->bindParam(':historikk', $som_json_igjen);
        $st->bindParam(':id', $id);
        $st->execute();

        //Slett beboer fra alle verv den har.
        $st = DB::getDB()->prepare('DELETE FROM beboer_verv WHERE beboer_id=:id');
        $st->bindParam(':id', $this->getId());
        $st->execute();
        
        //Slett aktive vaktbytter.
        
        foreach(Vaktbytte::getAlle() as $vaktbytte){
            /* @var Vaktbytte $vaktbytte */
            if($vaktbytte->getVakt()->getBruker()->getPerson()->getId() === $this->id){
                $vaktbytte->slett();
            }
        }
    
        try {
            $groupmanager = new \Group\GroupManage();
            
            $groupmanager->addToGroup($this->epost, 'MEMBER', SING_VETERAN);
    
            $groupmanager->removeFromGroup($this->epost, SING_ALLE);
            $groupmanager->removeFromGroup($this->epost, SING_SLARV);
            
        } catch(\Exception $e){}
    }

    public function flyttInn(){

        $romhistorikken = json_decode($this->romhistorikk, true);

        $siste_rom = end($romhistorikken);
        $siste_rom['innflyttet'] = date('Y-m-d');
        $siste_rom['utflyttet'] = null;

        $romhistorikken[] = $siste_rom;
        $romhistorikken = json_encode($romhistorikken);

        $st = DB::getDB()->prepare('UPDATE beboer SET romhistorikk=:historikk WHERE id=:id');
        $st->bindParam(':historikk', $romhistorikken);
        $st->bindParam(':id', $this->id);
        $st->execute();
    
        try {
            $groupmanager = new \Group\GroupManage();

            $groupmanager->addToGroup($this->epost, 'MEMBER', SING_ALLE);
            $groupmanager->addToGroup($this->epost, 'MEMBER', SING_SLARV);
            
            $groupmanager->removeFromGroup($this->epost, SING_VETERAN);
        } catch(\Exception $e){}
    }

    public function getVakterOgVakterSittet(){
        $antall_vakter = 0;
        $antall_sittet = 0;

    }

    public function harVakt()
    {
        return $this->getRolle()->getRegitimer() < 48;
    }

    public function getVakterInnenDogn()
    {
        //Henter ut alle vakter for de neste 24 timene.
        $vakterInnenDogn = array();
        if ($this->harVakt()) {
            foreach (VaktListe::medBrukerId($this->brukerId) as $vakt) {
                if (floor((strtotime($vakt->getDato()) - time()) / (60 * 60 * 24)) <= 1 && !$vakt->erFerdig()) {
                    $vakterInnenDogn[] = $vakt;
                }
            }
        }
        return $vakterInnenDogn;
    }

    public function getPrefs(){
        return Prefs::fraBeboerId($this->getId());
    }

    public function getVervListe()
    {
        if ($this->vervListe == null) {
            $this->vervListe = VervListe::medBeboerId($this->id);
        }
        return $this->vervListe;
    }

    public function erKjellerMester()
    {
        $st = DB::getDB()->prepare('SELECT * FROM beboer_verv WHERE beboer_id=:id AND verv_id=45');
        $st->bindParam(':id', $this->id);
        $st->execute();
        return ($st->rowCount() > 0 || $this->harDataVerv());
    }

    public function harDataVerv()
    {
        $id = $this->getId();

        $st = DB::getDB()->prepare('SELECT * from beboer_verv WHERE (beboer_id=:beboer_id AND (verv_id=43 OR verv_id=44))');
        $st->bindParam(':beboer_id', $id);
        $st->execute();

        return $st->rowCount() > 0;
    }

    public function harUtvalgVerv()
    {
        return count($this->getUtvalgVervListe()) > 0 || $this->harDataVerv();
    }

    public function getUtvalgVervListe()
    {
        if ($this->utvalgVervListe == null) {
            $this->utvalgVervListe = VervListe::utvalgMedBeboerId($this->id);
        }
        return $this->utvalgVervListe;
    }

    public function getMonthKryss($month = null)
    {
        return Krysseliste::getKryssByMonth($this->id, $month);
    }

    public function getEpostPref()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();

        return $st->fetchAll()[0];
    }

    public function vilHaVaktVarsler()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];

        return $epost_preferanser['snart_vakt'] == 1;
    }

    public function vilHaTildeltVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['tildelt'] == 1;
    }

    public function vilHaBytteGiVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['bytte'] == 1;
    }

    public function vilHaUtleieVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['utleie'] == 1;
    }

    public function vilHaBarvaktVarsel()
    {
        $st = DB::getDB()->prepare('SELECT * from epost_pref WHERE beboer_id=:beboer_id');
        $st->bindParam(':beboer_id', $this->getId());
        $st->execute();
        $epost_preferanser = $st->fetchAll()[0];
        return $epost_preferanser['barvakt'] == 1;
    }

    public function getBruker()
    {
        if ($this->bruker == null && $this->brukerId != 0) {
            $this->bruker = Bruker::medId($this->brukerId);
        }
        return $this->bruker;
    }

    public function erHelgaGeneral()
    {
        return (Helga::getLatestHelga()) != null ? Helga::getLatestHelga()->erHelgaGeneral($this->id) : false;
    }

    public function toString(){
        return $this->getFulltNavn();
    }

    public function harHelgaTilgang(){
        if($this->harDataVerv() || $this->erHelgaGeneral() || $this->harUtvalgVerv()){
            return true;
        }
        $st = DB::getDB()->prepare('SELECT * FROM helgaverv AS hv WHERE hv.id IN 
                                  (SELECT hvb.id FROM helgaverv_beboer AS hvb WHERE hvb.beboer_id=:id)');
        $st->bindParam(':id', $this->id);
        $st->execute();
        $helgaverv = Helgaverv::medId($st->fetch()['id']);
        /* @var \intern3\Helgaverv $helgaverv */
        if($helgaverv != null){
            return $helgaverv->harInngangTilgang();
        }
        return false;
    }

    public function getSemesterlist(){
        $innflyttet = $this->getRomhistorikk()->getPerioder()[0]->innflyttet;
        $lista = array(Funk::generateSemesterString(date('Y-m-d', strtotime($innflyttet))));
        //$innflytta_aar = date('Y', strtotime($innflyttet));
        for($i = date('Y', strtotime($innflyttet)); $i <= date('Y'); $i++){
            $lista[] = Funk::generateSemesterString(date('Y-m-d', strtotime("$i-01-01")));
            if($i == date('Y')){
                break;
            }
            $lista[] = Funk::generateSemesterString(date('Y-m-d', strtotime("$i-09-01")));
        }
        $current = Funk::generateSemesterString(date('Y-m-d'));
        if(!in_array($current, $lista)){
            $lista[] = $current;
        }
        
        return array_reverse(array_unique($lista));
    }

    public static function nyBeboer ($fornavn, $mellomnavn, $etternavn, $fodselsdato, $adresse, $postnr, $mobilnr,
                                     $studie_id, $skole_id, $klasse, $alko, $rolle_id, $epost, $rom_id) : Beboer {

        //Opprett beboer

        $bruker_id = Funk::getLastBrukerId() + 1;
        $st = DB::getDB()->prepare('INSERT INTO beboer
(bruker_id,fornavn,mellomnavn,etternavn,fodselsdato,adresse,postnummer,telefon,studie_id,skole_id,klassetrinn,alkoholdepositum,rolle_id,epost,romhistorikk)
VALUES(:bruker_id,:fornavn,:mellomnavn,:etternavn,:fodselsdato,:adresse,:postnummer,:telefon,:studie_id,:skole_id,:klassetrinn,:alko,:rolle_id,:epost,:romhistorikk)');

        $st->bindParam(':bruker_id', $bruker_id);
        $st->bindParam(':fornavn', $fornavn);
        $st->bindParam(':mellomnavn', $mellomnavn);
        $st->bindParam(':etternavn', $etternavn);
        $st->bindParam(':fodselsdato', $fodselsdato);
        $st->bindParam(':adresse', $adresse);
        $st->bindParam(':postnummer', $postnr);
        $st->bindParam(':telefon', $mobilnr);
        $st->bindParam(':studie_id', $studie_id);
        $st->bindParam(':skole_id', $skole_id);
        $st->bindParam(':klassetrinn', $klasse);
        $st->bindParam(':alko', $alko);
        $st->bindParam(':rolle_id', $rolle_id);
        $st->bindParam(':epost', $epost);
        $rom = new Romhistorikk();
        $rom->addPeriode($rom_id, date('Y-m-d'), null);
        $romhistorikken = $rom->tilJson();
        $st->bindParam(':romhistorikk', $romhistorikken);
        $st->execute();


        //Opprett bruker

        $st = DB::getDB()->prepare('INSERT INTO bruker (id,passord,salt) VALUES(:id,:passord,:salt)');
        $st->bindParam(':id', $bruker_id);
        $passord = Funk::generatePassword();
        $saltet = Funk::generatePassword(28);
        $hashen = LogginnCtrl::genererHashMedSalt($passord, $saltet);
        $st->bindParam(':passord', $hashen);
        $st->bindParam(':salt', $saltet);
        $st->execute();


        $beboer = Beboer::medBrukerId($bruker_id);

        //Opprett epost-prefs
        $beboer_id = $beboer->getId();
        $st = DB::getDB()->prepare('INSERT INTO epost_pref (beboer_id,tildelt,snart_vakt,bytte,utleie,barvakt) VALUES(:id,1,1,1,1,1)');
        $st->bindParam(':id', $beboer_id);
        $st->execute();

        //Opprett prefs
        $st = DB::getDB()->prepare('INSERT INTO prefs (beboerId, resepp, vinkjeller, pinboo, pinkode, vinpinboo, vinpin)
    VALUES(:id, 1, 1, 0, NULL, 0, NULL)');
        $st->bindParam(':id', $beboer_id);
        $st->execute();

        Funk::setSuccess("Du la til " . $beboer->getFulltNavn() . " til Internsida!");

        //Legg til på epostlister
        try {
            $groupmanager = new \Group\GroupManage();
            $groupmanager->addToGroup($beboer->getEpost(), 'MEMBER', SING_ALLE);
            $groupmanager->addToGroup($beboer->getEpost(), 'MEMBER', SING_SLARV);
            Funk::setSuccess("Du la til " . $beboer->getFulltNavn() . " til Internsida, SING-ALLE og SING-SLARV!");
        } catch(\Exception $e){
            Epost::sendEpost("data@singsaker.no", "[SING-BOTS] Ble ikke lagt inn i epostlister",
                "Beboeren " . $beboer->getFulltNavn() . " med e-post " . $beboer->getEpost() . " ble ikke
                       lagt til epostgruppene. Errormelding:<br/>\n" . $e->getMessage());
        }

        //Send e-post til den nye beboeren

        $beskjed = "<html><body>Hei!<br/><br/>Du har fått opprettet en brukerkonto på
<a href='https://intern.singsaker.no'>Singsaker Studenterhjem sine internsider!</a> Velkommen skal du være.<br/>Brukernavn: $post[epost]<br/>Passord kan du sette selv, ved å benytte 'glemt-passord'-funksjonaliteten.<br/><br/>
<br/><br/>Med vennlig hilsen<br/>Internsida.<br/><br/></body></html>";
        $tittel = "[SING-INTERN] Opprettelse av brukerkonto";
        Epost::sendEpost($beboer->getEpost(), $tittel, $beskjed);

        return $beboer;
    }

    private function oppdater(){

        $st = DB::getDB()->prepare('UPDATE beboer SET fornavn=:fornavn,mellomnavn=:mellomnavn,etternavn=:etternavn,
fodselsdato=:fodselsdato,adresse=:adresse,postnummer=:postnummer,telefon=:telefon,studie_id=:studie_id,skole_id=:skole_id,
klassetrinn=:klassetrinn,alkoholdepositum=:alko,rolle_id=:rolle,epost=:epost,romhistorikk=:romhistorikk,ansiennitet=:ans WHERE id=:id');

        $st->bindParam(':id', $this->id);
        $st->bindParam(':fornavn', $this->fornavn);
        $st->bindParam(':mellomnavn', $this->mellomnavn);
        $st->bindParam(':etternavn', $this->etternavn);
        $st->bindParam(':fodselsdato', $this->fodselsdato);
        $st->bindParam(':adresse', $this->adresse);
        $st->bindParam(':postnummer', $this->postnummer);
        $st->bindParam(':telefon', $this->telefon);
        $st->bindParam(':studie_id', $this->studieId);
        $st->bindParam(':skole_id', $this->skoleId);
        $st->bindParam(':klassetrinn', $this->klassetrinn);
        $alko = $this->alkoholdepositum > 0 ? 1 : 0;
        $st->bindParam(':alko', $alko);
        $st->bindParam(':rolle', $this->rolleId);
        $st->bindParam(':epost', $this->epost);
        $st->bindParam(':romhistorikk', $this->romhistorikk);
        $st->bindParam(':ans', $this->ansiennitet);
        $st->execute();
    }

    public function erAktiv(){
        return $this->getRomhistorikk()->romHistorikk[count($this->getRomhistorikk()->romHistorikk) -1]->utflyttet === null;
    }

    public function harVaktDato($dato){

        $st = DB::getDB()->prepare('SELECT * FROM vakt WHERE (bruker_id=:brukerid AND dato=:dato)');
        $st->bindParam(':brukerid', $this->brukerId);
        $st->bindParam(':dato', $dato);
        $st->execute();

        return $st->rowCount() > 0;

    }

    /*
     * Returnerer førstevakter + 3.-4. vakt fredag og 2.,3.,4. vakt lørdag og 2.,3. vakt søndag
     */
    public function antallKjipeVakter() : int {

        if($this->antall_kjipe != -1){
            return $this->antall_kjipe;
        }

        $st = DB::getDB()->prepare('SELECT count(id) AS sum FROM vakt WHERE 
                                        (bruker_id=:brukerid  
                                        AND(
                                          (DAYOFWEEK(dato) = 6 AND vakttype IN (3, 4) ) 
                                          OR (DAYOFWEEK(dato) = 7 AND vakttype IN (2,3,4) ) 
                                          OR (DAYOFWEEK(dato) = 1 AND vakttype IN (2,3))
                                          OR vakttype = 1)
                                      )');

        $st->bindParam(':brukerid', $this->brukerId);

        $st->execute();

        $this->antall_kjipe = $st->fetch()["sum"];

        return $this->antall_kjipe;

    }

}

?>