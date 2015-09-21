<?php

namespace intern3;

set_time_limit(999999999);

/* Denne fila må på sikt flyttes slik at ikke hvem som helst kan leke med den! */
/* Denne fila tømmer databasen og importerer fra gammel internside. */

// For å koble til postgres-sql og singbasen
require_once("../ink/pg_sql_tilkobling.php");

require_once("../ink/autolast.php");

DB::getDB()->beginTransaction();

function avbryt($melding) {
	DB::getDB()->rollback();
	exit($melding . PHP_EOL);
}

function byttTegnsett($streng) {
	return iconv('windows-1252', 'utf-8', $streng);
//	return $streng;
}

$db = DB::getDB();
$db->query('TRUNCATE TABLE skole;');
$db->query('TRUNCATE TABLE studie;');
$db->query('TRUNCATE TABLE rom;');
$db->query('TRUNCATE TABLE bruker;');
$db->query('TRUNCATE TABLE beboer;');
$db->query('TRUNCATE TABLE verv;');
$db->query('TRUNCATE TABLE beboer_verv;');
$db->query('TRUNCATE TABLE vakt;');
$db->query('TRUNCATE TABLE ansatt;');
//etc

/* Migrering av skole, start */

$hentSkoler = pg_query('SELECT * FROM skole ORDER BY skole_id;');
while ($skole = pg_fetch_array($hentSkoler)) {
	$navn = byttTegnsett($skole['skole']);
	$st = $db->prepare('INSERT INTO skole(navn) VALUES(:navn);');
	$st->bindParam(':navn', $navn);
	$st->execute();
}

/* Migrering av skole, slutt */

/* Migrering av studie, start */

$hentStudier = pg_query('SELECT * FROM studie ORDER BY studie_id;');
while ($studie = pg_fetch_array($hentStudier)) {
	$navn = byttTegnsett($studie['studie']);
	$st = $db->prepare('INSERT INTO studie(navn) VALUES(:navn);');
	$st->bindParam(':navn', $navn);
	$st->execute();
}

/* Migrering av studie, slutt */

/* Migrering av rom, start */

// Fra gamle til nye romtype_id-er
$romtypeIder = array(
	1 => 1, // Bøttekott
	4 => 2, // Korrhybel
	3 => 3, // Storhybel
	6 => 4, // Liten Parhybel
	5 => 5, // Stor Parhybel
	3 => 4, // Parhybel = Liten Parhybel ??????
	7 => 1 // Vedlager => Bøttekott :)
);
$hentRom = pg_query('SELECT * FROM rom WHERE romnummer > 1 ORDER BY romnummer;');
while ($rom = pg_fetch_array($hentRom)) {
	$romtypeId = $romtypeIder[$rom['romtype_id']];
	$nummer = $rom['romnummer'];
	while (strlen($nummer) < 3) {
		$nummer = '0' . $nummer;
	}
	$st = $db->prepare('INSERT INTO rom(
	romtype_id,
	navn
) VALUES(
	:romtype_id,
	:navn
);');
	$st->bindParam(':romtype_id', $romtypeId);
	$st->bindParam(':navn', $nummer);
	$st->execute();
}

/* Migrering av rom, slutt */

// Fra gammel beboer_id til ny bruker_id
$beboerBrukerKobling = array();

/* Migrering av brukere, start */

$hentBrukere = pg_query('SELECT * FROM
	brukerdata as br,
	beboer as be
WHERE \'b_\' || be.beboer_id = br.chain
ORDER BY user_id;');
while ($bruker = pg_fetch_array($hentBrukere)) {
	$beboerId = intval(substr($bruker['chain'], 2));
	$brukerId = $db->lastInsertId();
	$beboerBrukerKobling[$beboerId] = $brukerId;
	$st = $db->prepare('INSERT INTO bruker(passord) VALUES(:passord);');
	$st->bindParam(':passord', $bruker['passord']);
	$st->execute();
}

/* Migrering av brukere, slutt */

$beboerIdFornyelse = array();

/* Migrering av beboere, start */

// Fra gamle oppgave_id til nye rolle_id
$rolleIder = array(
	1 => 2, // Halv vakt/regi
	2 => 1, // Full vakt
	3 => 3, // Full regi
	4 => 3,
	5 => 3,
	6 => 1,
	7 => 1
);

$hentBeboere = pg_query('SELECT * FROM
	beboer AS be,
	skole AS sk,
	studie AS st
WHERE
	be.skole_id = sk.skole_id AND
	be.studie_id = st.studie_id AND
	beboer_id > 0
ORDER BY be.beboer_id;');
if (pg_num_rows($hentBeboere) == 0) {
	avbryt('Ingen beboere? Ikke bra.');
}
while ($beboer = pg_fetch_array($hentBeboere)) {
	$brukerId = isset($beboerBrukerKobling[$beboer['beboer_id']]) ? $beboerBrukerKobling[$beboer['beboer_id']] : null;
	// Innføring av begrepet mellomnavn (alt mellom fornavn og etternavn)
	$mellomnavn = $beboer['fornavn'] . ' ' . $beboer['etternavn'];
	$mellomnavn = byttTegnsett($mellomnavn);
	$mellomnavn = trim(preg_replace('/[\s]{2,}/', ' ', $mellomnavn));
	$mellomnavn = explode(' ', $mellomnavn);
	$etternavn = array_pop($mellomnavn);
	$fornavn = array_shift($mellomnavn);
	$mellomnavn = implode(' ', $mellomnavn);
	/* Migrering av romhistorikk, start */
	$hentRomhistorikk = pg_query('SELECT * FROM romhistorikk WHERE beboer_id=' . $beboer['beboer_id'] . ' ORDER BY innflyttet;');
	$romhistorikk = new Romhistorikk();
	while ($rom = pg_fetch_array($hentRomhistorikk)) {
		//Flytting av romhistorikk til json-dokument
		$nummer = $rom['romnummer'];
		if ($nummer == 1) {
			continue;
		}
		while (strlen($nummer) < 3) {
			$nummer = '0' . $nummer;
		}
		$romhistorikk->addPeriode(Rom::medNavn($nummer)->getId(), $rom['innflyttet'], $rom['utflyttet']);
	}
	$romhistorikkJson = $romhistorikk->tilJson();
	/* Migrering av romhistorikk, slutt */
	$fodselsdato = $beboer['fodselsdato'] == null ? ' ' : $beboer['fodselsdato'];
	$adresse = $beboer['adresse'] == null ? null : byttTegnsett($beboer['adresse']);
	$postnummer = $beboer['postnummer'] == null ? null : $beboer['postnummer'];
	$telefon = $beboer['mobil'] == null ? ' ' : str_replace(' ', '', $beboer['mobil']);
	$rolleId = $rolleIder[$beboer['oppgave_id']];
	$epost = $beboer['epost'] == null ? null : strtolower($beboer['epost']);
	$studieId = intval(Studie::medNavn(byttTegnsett($beboer['studie']))->getId());
	$skoleId = intval(Skole::medNavn(byttTegnsett($beboer['skole']))->getId());
	// Innsetting
	$st = $db->prepare('INSERT INTO beboer(
	bruker_id,
	fornavn,
	mellomnavn,
	etternavn,
	fodselsdato,
	adresse,
	postnummer,
	telefon,
	studie_id,
	skole_id,
	klassetrinn,
	alkoholdepositum,
	rolle_id,
	epost,
	romhistorikk)
VALUES(
	:bruker_id,
	:fornavn,
	:mellomnavn,
	:etternavn,
	:fodselsdato,
	:adresse,
	:postnummer,
	:telefon,
	:studie_id,
	:skole_id,
	:klassetrinn,
	:alkoholdepositum,
	:rolle_id,
	:epost,
	:romhistorikk
);');
	$st->bindParam(':bruker_id', $brukerId);
	$st->bindParam(':fornavn', $fornavn);
	$st->bindParam(':mellomnavn', $mellomnavn);
	$st->bindParam(':etternavn', $etternavn);
	$st->bindParam(':fodselsdato', $fodselsdato);
	$st->bindParam(':adresse', $adresse);
	$st->bindParam(':postnummer', $postnummer);
	$st->bindParam(':telefon', $telefon);
	$st->bindParam(':studie_id', $studieId);
	$st->bindParam(':skole_id', $skoleId);
	$st->bindParam(':klassetrinn', $beboer['klasse']);
	$st->bindParam(':alkoholdepositum', $beboer['alkodepositum']);
	$st->bindParam(':rolle_id', $rolleId);
	$st->bindParam(':epost', $epost);
	$st->bindParam(':romhistorikk', $romhistorikkJson);
	// Merk at bruker_id her ennå ikke er satt.
	$st->execute();
	$beboerIdFornyelse[$beboer['beboer_id']] = $db->lastInsertId();
}

foreach (BeboerListe::aktive() as $beboer) {
	if ($beboer->getBrukerId()==0) {
		$st = $db->query('INSERT INTO bruker(passord) VALUES(\'testetest\');');
		$brukerId = $db->lastInsertId();
		$st = $db->query('UPDATE beboer SET bruker_id='.$brukerId.' WHERE id='.$beboer->getId().';');
		$gammelIndex = array_search($beboer->getId(), $beboerIdFornyelse);
		$beboerBrukerKobling[$gammelIndex] = $brukerId;
	}
}

/* Migrering av beboere, slutt */

// Nye passord
$st = $db->query('INSERT INTO bruker(passord) VALUES(\'testetest\');');
$st = $db->query('INSERT INTO ansatt(bruker_id, fornavn, etternavn, epost) VALUES('.$db->lastInsertId().', \'Torild\', \'Fivë\', \'torild@singsaker.no\');');
$st = $db->query('UPDATE bruker SET passord=\'' . LogginnCtrl::genererHash('testetest') . '\';');

/* Migrering av verv, start */

$innsatteVerv = array();

$hentVerv = pg_query('SELECT * FROM apmandsverv ORDER BY apmandsverv_id;');
while ($verv = pg_fetch_array($hentVerv)) {
	$navn = byttTegnsett($verv['apmandsverv']);
	$utvalg = $verv['utvalgsverv'] == 't' ? 1 : 0;
	if (!isset($innsatteVerv[$navn])) {
		$st = $db->prepare('INSERT INTO verv(
	navn,utvalg,epost
) VALUES(
	:navn,:utvalg,:epost
);');
		$st->bindParam(':navn', $navn);
		$st->bindParam(':utvalg', $utvalg);
		$st->bindParam(':epost', $verv['epost']);
		$st->execute();
		$innsatteVerv[$navn] = $db->lastInsertId();
	}
	if ($verv['beboer_id'] == 0) {
		continue;
	}
	$st = $db->prepare('INSERT INTO beboer_verv(beboer_id,verv_id) VALUES(:beboerId,:vervId);');
	$st->bindParam(':beboerId', $beboerIdFornyelse[$verv['beboer_id']]);
	$st->bindParam(':vervId', $innsatteVerv[$navn]);
	$st->execute();
}

/* Migrering av verv, slutt */

$vaktIdFornyelse = array();

/* Migrering av vakter, start */

$hentVakter = pg_query('SELECT * FROM vaktliste ORDER BY vakt_id;');
while ($vakt = pg_fetch_array($hentVakter)) {
	$brukerId = $vakt['beboer_id'] <> 0 && isset($beboerBrukerKobling[$vakt['beboer_id']]) ? $beboerBrukerKobling[$vakt['beboer_id']] : 0;
	$vakttype = $vakt['vakt'] + 1;
	$bekreftet = $vakt['bekreftet'] == 't';
	$autogenerert = $vakt['manual'] != 't';
	$st = $db->prepare('INSERT INTO vakt(
	bruker_id,vakttype,dato,bekreftet,autogenerert
) VALUES(
	:brukerId,:vakttype,:dato,:bekreftet,:autogenerert
);');
	$st->bindParam(':brukerId', $brukerId);
	$st->bindParam(':vakttype', $vakttype);
	$st->bindParam(':dato', $vakt['dato']);
	$st->bindParam(':bekreftet', $bekreftet);
	$st->bindParam(':autogenerert', $autogenerert);
	$st->execute();
	$vaktIdFornyelse[$vakt['vakt_id']] = $db->lastInsertId();
}

/* Migrering av vakter, slutt */

/* Migrering av vakterbytter ja, start */

$hentVaktbytter = pg_query('SELECT * FROM pb_ledigevakter ORDER BY id;');
while ($vaktbytte = pg_fetch_array($hentVaktbytter)) {
	$vaktId = $vaktIdFornyelse[$vaktbytte['vakt_id']];
	$gisBort = !$vaktbytte['modus'];
	// $forslag = $vaktbytte['forslag'];
	$merknad = $vaktbytte['comment'];
	$st = $db->prepare('INSERT INTO vaktbytte(
	vakt_id,gisbort,merknad
) VALUES(
	:vaktId,:gisBort,:merknad
)');
	$st->bindParam(':vaktId', $vaktId);
	$st->bindParam(':gisBort', $gisBort);
	// $st->bindParam(':forslag', $forslag);
	$st->bindParam(':merknad', $merknad);
	$st->execute();
}

/* Migrering av vakterbytter ja, slutt */

ferdig(); // Alt heretter går veldig sakte.
$db->query('TRUNCATE TABLE krysseliste;');

/* Migrering av krysseliste, start */

// Fra gamle til nye drikke_id-er
$drikkeIder = array(
	-1 => 1, // Pant (hadde egen kolonne tidligere)
	0 => 2, // Øl
	1 => 2, // Øl
	2 => 3, // Carlsberg
	3 => 4, // Cider
	4 => 5 // Rikdom
);

$hentKryss = pg_query('SELECT * FROM krysseliste_beer ORDER BY dato;');
while ($kryss = pg_fetch_array($hentKryss)) {
	if ($kryss['beboer_id'] == 0) {
		continue;
	}
	$drikkeId = $kryss['pant'] == 'TRUE' ? $drikkeIder[-1] : $drikkeIder[$kryss['type']];
	$krysseliste = Krysseliste::medBeboerDrikkeId($beboerIdFornyelse[$kryss['beboer_id']], $drikkeId);
	$krysseliste->addKryss($kryss['antall'], strtotime($kryss['dato']), $kryss['fakturert']);
	$krysseliste->oppdater();
}

/* Migrering av krysseliste, slutt */

ferdig();

function ferdig() {
	DB::getDB()->commit();
	exit();
}

?>
