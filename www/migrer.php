<?php

namespace intern3;

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
}

$db = DB::getDB();
$db->query('TRUNCATE skole;');
$db->query('TRUNCATE studie;');
$db->query('TRUNCATE rom;');
$db->query('TRUNCATE beboer;');
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

/* Migrering av beboere, start */

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
	$telefon = substr($telefon, 0, strlen($telefon) - 8) . ' ' . substr($telefon, -8);
	$epost = $beboer['epost'] == null ? null : strtolower($beboer['epost']);
	$studieId = intval(Studie::medNavn(byttTegnsett($beboer['studie']))->getId());
	$skoleId = intval(Skole::medNavn(byttTegnsett($beboer['skole']))->getId());
	// Innsetting
	$st = $db->prepare('INSERT INTO beboer(
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
	$st->bindParam(':rolle_id', $beboer['oppgave_id']);
	$st->bindParam(':epost', $epost);
	$st->bindParam(':romhistorikk', $romhistorikkJson);
	// Merk at bruker_id her ennå ikke er satt.
	$st->execute();
}

/* Migrering av beboere, slutt */

// og mye mer må gjøres her...

DB::getDB()->commit();

?>