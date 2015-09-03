<?php

namespace intern3;

/* Denne fila må på sikt flyttes slik at ikke hvem som helst kan leke med den! */
/* Denne fila tømmer databasen og importerer fra gammel internside. */

// For å koble til postgres-sql og singbasen
require_once("../ink/pg_sql_tilkobling.php");

require_once("../ink/autolast.php");

DB::getDB()->beginTransaction();

function avbryt() {
	DB::getDB()->rollback();
	exit();
}

$db = DB::getDB();
$db->query('TRUNCATE beboer;');
//etc

/* Migrering av beboere, start */

$hentBeboere = pg_query('SELECT * FROM beboer WHERE beboer_id > 0 ORDER BY beboer_id;');
if (pg_num_rows($hentBeboere) == 0) {
	die('Ingen beboere? Ikke bra.');
}
while ($beboer = pg_fetch_array($hentBeboere)) {
	// Innføring av begrepet mellomnavn (alt mellom fornavn og etternavn)
	$mellomnavn = explode(' ', $beboer['fornavn'] . ' ' . $beboer['etternavn']);
	$etternavn = array_pop($mellomnavn);
	$fornavn = array_shift($mellomnavn);
	$mellomnavn = implode(' ', $mellomnavn);
	/* Migrering av romhistorikk, start */
	$hentRomhistorikk = pg_query('SELECT * FROM romhistorikk WHERE beboer_id=' . $beboer['beboer_id'] . ';');
	$romhistorikk = new Romhistorikk();
	while ($rom = pg_fetch_array($hentRomhistorikk)) {
		//Flytting av romhistorikk til json-dokument
		$romhistorikk->addPeriode($rom['romnummer']/*skal være rom_id!*/, $rom['innflyttet'], $rom['utflyttet']);
	}
	$romhistorikkJson = $romhistorikk->toJson();
	/* Migrering av romhistorikk, slutt */
	// Felter som foreløpig ikke kan være null
	$fodselsdato = $beboer['fodselsdato'] == null ? ' ' : $beboer['fodselsdato'];
	$adresse = $beboer['adresse'] == null ? ' ' : $beboer['adresse'];
	$postnummer = $beboer['postnummer'] == null ? ' ' : $beboer['postnummer'];
	$telefon = $beboer['mobil'] == null ? ' ' : $beboer['mobil'];
	$epost = $beboer['epost'] == null ? ' ' : $beboer['epost'];
	// Innsetting
	$st = $db->prepare('INSERT INTO beboer (
	id,
	fornavn,
	mellomnavn,
	etternavn,
	fodselsdato,
	adresse,
	postnummer,
	telefon,
	studie_id,
	klassetrinn,
	alkoholdepositum,
	rolle_id,
	epost,
	romhistorikk)
VALUES(
	:id,
	:fornavn,
	:mellomnavn,
	:etternavn,
	:fodselsdato,
	:adresse,
	:postnummer,
	:telefon,
	:studie_id,
	:klassetrinn,
	:alkoholdepositum,
	:rolle_id,
	:epost,
	:romhistorikk
);');
	$st->bindParam(':id', $beboer['beboer_id']);
	$st->bindParam(':fornavn', $fornavn);
	$st->bindParam(':mellomnavn', $mellomnavn);
	$st->bindParam(':etternavn', $etternavn);
	$st->bindParam(':fodselsdato', $fodselsdato);
	$st->bindParam(':adresse', $adresse);
	$st->bindParam(':postnummer', $postnummer);
	$st->bindParam(':telefon', $telefon);
	$st->bindParam(':studie_id', $beboer['studie_id']);
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