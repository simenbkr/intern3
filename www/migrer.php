<?php

namespace intern3;

set_time_limit(999999999);

/* Denne fila må på sikt flyttes slik at ikke hvem som helst kan leke med den! */
/* Denne fila tømmer databasen og importerer fra gammel internside. */

// For å koble til postgres-sql og singbasen
pg_connect('host=dev.singsaker.no dbname=singbasen user=utvalget password=***REMOVED***');

// For å koble til mysql og regiportalen
$regi = new \PDO('mysql:host=localhost;dbname=regiportal', 'regiportal', 'regiportal',
    array(
        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    )
);

require_once("../ink/autolast.php");

DB::getDB()->beginTransaction();

function avbryt($melding)
{
    DB::getDB()->rollback();
    exit($melding . PHP_EOL);
}

function byttTegnsett($streng)
{
    //return iconv(mb_detect_encoding($streng), 'utf-8', $streng);
    return iconv('windows-1252', 'utf-8', $streng);
//	return $streng;
}

$db = DB::getDB();
$db->query('SET NAMES utf8');
$db->query('CREATE TABLE IF NOT EXISTS `ansatt`(
`id` int(10) unsigned NOT NULL auto_increment,
`bruker_id` int(10) unsigned default NULL,
`fornavn` varchar(20) collate utf8_unicode_ci NOT NULL,
`mellomnavn` varchar(40) collate utf8_unicode_ci default NULL COMMENT \'alt mellom fornavn og etternavn\',
`etternavn` varchar(20) collate utf8_unicode_ci NOT NULL,
`epost` varchar(40) collate utf8_unicode_ci NOT NULL,
PRIMARY KEY  (`id`))');

$db->query('CREATE TABLE IF NOT EXISTS `beboer` (
`id` int(10) unsigned NOT NULL auto_increment,
  `bruker_id` int(10) unsigned DEFAULT NULL,
  `fornavn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mellomnavn` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT \'alt mellom fornavn og etternavn\',
  `etternavn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fodselsdato` date DEFAULT NULL,
  `adresse` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postnummer` smallint(5) unsigned DEFAULT \'0\',
  `telefon` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `studie_id` int(10) unsigned NOT NULL,
  `skole_id` int(10) unsigned NOT NULL,
  `klassetrinn` tinyint(3) unsigned NOT NULL,
  `alkoholdepositum` tinyint(1) DEFAULT NULL,
  `rolle_id` int(10) DEFAULT NULL,
  `epost` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `romhistorikk` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `beboer_verv` (
  `beboer_id` int(10) unsigned NOT NULL,
  `verv_id` int(10) unsigned NOT NULL
)');

$db->query('CREATE TABLE IF NOT EXISTS `bruker` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `passord` varchar(120) collate utf8_unicode_ci NOT NULL,
  `salt` varchar(64) collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `drikke` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(20) collate utf8_unicode_ci NOT NULL,
  `pris` double unsigned NOT NULL,
  `vin` tinyint(1) NOT NULL COMMENT \'Krysset i vinkjeller?\',
  PRIMARY KEY  (`id`)
)');

$db->query('INSERT INTO `drikke` (`id`, `navn`, `pris`, `vin`) VALUES
(1, \'Pant\', 1, 0),
(2, \'Øl\', 20, 0),
(3, \'Cider\', 29.5, 0),
(4, \'Carlsberg\', 26.5, 0),
(5, \'Rikdom\', 37, 0);');

$db->query('CREATE TABLE IF NOT EXISTS `kryss` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `beboer_id` int(10) unsigned NOT NULL,
  `drikke_id` int(10) unsigned NOT NULL,
  `krysseliste` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `pafyll` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `dato` date NOT NULL,
  `vakttype` varchar(1) collate utf8_unicode_ci NOT NULL,
  `transaksjon` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `rom` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `romtype_id` int(10) unsigned NOT NULL,
  `navn` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `romtype` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('INSERT INTO `romtype` (`id`, `navn`) VALUES
(1, \'Bøttekott\'),
(2, \'Korrhybel\'),
(3, \'Storhybel\'),
(4, \'Liten Parhybel\'),
(5, \'Stor Parhybel\')');

$db->query('CREATE TABLE IF NOT EXISTS `skole` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `straffevakt` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `bruker_id` int(10) unsigned NOT NULL,
  `vakt_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `studie` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `vakt` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `bruker_id` int(10) unsigned NOT NULL,
  `vakttype` varchar(1) collate utf8_unicode_ci NOT NULL,
  `dato` date NOT NULL,
  `bekreftet` tinyint(1) default NULL,
  `bytte` tinyint(1),
  `autogenerert` tinyint(1) default \'1\',
  `dobbelvakt` tinyint(1) default NULL,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `vaktbytte` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vakt_id` int(10) unsigned NOT NULL,
  `gisbort` tinyint(1) default \'0\',
  `har_passord` tinyint(1) default \'0\',
  `passord` varchar(512),
  `forslag` text collate utf8_unicode_ci COMMENT \'Hva man kan få i bytte\',
  `merknad` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `verv` (
`id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `regitimer` tinyint(3) unsigned DEFAULT \'0\',
  `utvalg` tinyint(1) DEFAULT \'0\',
  `epost` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `feil` (
`id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(128) collate utf8_unicode_ci NOT NULL,
  `beskrivelse` text collate utf8_unicode_ci,
  `feilkategori_id` int(10) unsigned NOT NULL,
  `prioritet_id` int(10) unsigned NOT NULL,
  `tid_oppretta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `feilkategori` (
`id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(16) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `beskrivelse` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `prioritet_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('INSERT INTO `feilkategori` (`id`, `navn`, `beskrivelse`, `prioritet_id`) VALUES
(1, \'Elektrisk\', \'\', 2),
(2, \'Bad\', \'\', 2),
(3, \'Rom\', \'\', 2),
(4, \'Dør\', \'\', 2),
(5, \'Vindu\', \'\', 2),
(6, \'Brannsikkerhet\', \'\', 2)');

$db->query('CREATE TABLE IF NOT EXISTS `oppgave` (
`id` int(1) NOT NULL auto_increment,
  `tid_oppretta` date DEFAULT NULL,
  `anslag_timer` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `ansvarsomrade_id` int(1) DEFAULT NULL,
  `anslag_personer` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `paameldte` varchar(1024) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `prioritet_id` int(1) NOT NULL,
  `navn` varchar(128) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `beskrivelse` text CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `godkjent` tinyint(1) NOT NULL,
  `tid_godkjent` date DEFAULT NULL,
  `godkjent_bruker_id` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `prioritet` (
`id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(16) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `nummer` tinyint(4) NOT NULL COMMENT \'Relativt nummer for å skille viktighet\',
  `farge` varchar(7) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('INSERT INTO `prioritet` (`id`, `navn`, `nummer`, `farge`) VALUES
(1, \'Lav\', 0, \'#00B233\'),
(2, \'Middels\', 1, \'#1485CC\'),
(3, \'Høy\', 3, \'#FF2626\')');

$db->query('CREATE TABLE IF NOT EXISTS `rapport` (
`id` int(12) unsigned NOT NULL auto_increment,
  `kvittering_id` int(8) unsigned DEFAULT NULL,
  `feil_id` int(8) unsigned DEFAULT NULL,
  `oppgave_id` int(8) unsigned DEFAULT NULL COMMENT \'For framtidig implementasjon av regioppgaver\',
  `prioritet_id` int(4) unsigned NOT NULL,
  `godkjent` tinyint(1) NOT NULL,
  `tid_endret` datetime DEFAULT NULL,
  `merknad` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `tid_godkjent` datetime NOT NULL,
  `godkjent_bruker_id` int(8) NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `kvittering` (
`id` int(8) unsigned NOT NULL auto_increment,
  `bruker_id` int(8) unsigned COMMENT \'Opprettet av\',
  `rom_id` int(8) unsigned NOT NULL,
  `tid_oppretta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `ansvarsomrade` (
`id` int(4) NOT NULL auto_increment,
  `navn` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `beskrivelse` text CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `ansvarsomrade_feil` (
  `ansvarsomrade_id` int(4) unsigned NOT NULL,
  `feil_id` int(4) unsigned NOT NULL
)');

$db->query('CREATE TABLE IF NOT EXISTS `bruker_ansvarsomrade` (
  `bruker_id` int(8) unsigned NOT NULL,
  `ansvarsomrade_id` int(4) unsigned NOT NULL
)');

$db->query('CREATE TABLE IF NOT EXISTS `arbeidskategori` (
`id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `beskrivelse` text CHARACTER SET utf8 COLLATE utf8_swedish_ci,
  `tid_oppretta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `arbeid` (
`id` int(10) unsigned NOT NULL auto_increment,
  `bruker_id` int(1) unsigned NOT NULL,
  `polymorfkategori_id` int(1) unsigned NOT NULL COMMENT \'union{oppgave, feil, kategori}_id\',
  `polymorfkategori_velger` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
  `tid_registrert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sekunder_brukt` int(10) NOT NULL DEFAULT \'0\',
  `tid_utfort` date NOT NULL,
  `kommentar` text COLLATE utf8_unicode_ci,
  `godkjent` tinyint(1) NOT NULL DEFAULT \'0\',
  `tid_godkjent` datetime NOT NULL,
  `godkjent_bruker_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `oppgave` (
`id` int(10) NOT NULL auto_increment,
  `tid_oppretta` date DEFAULT NULL,
  `anslag_timer` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `ansvarsomrade_id` int(1) DEFAULT NULL,
  `anslag_personer` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `prioritet_id` int(1) NOT NULL,
  `navn` varchar(128) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `beskrivelse` text CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `godkjent` tinyint(1) NOT NULL,
  `tid_godkjent` date DEFAULT NULL,
  `godkjent_bruker_id` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `journal` (
`kryss_id` int(6) NOT NULL auto_increment,
  `beboer_id` int(5) DEFAULT NULL,
  `vakt` int(1) DEFAULT NULL,
  `ol_mottatt` int(6) DEFAULT NULL,
  `ol_pafyll` int(6) DEFAULT NULL,
  `ol_avlevert` int(6) DEFAULT NULL,
  `ol_utavskap` int(6) DEFAULT NULL,
  `cid_mottatt` int(6) DEFAULT NULL,
  `cid_pafyll` int(6) DEFAULT NULL,
  `cid_avlevert` int(6) DEFAULT NULL,
  `cid_utavskap` int(6) DEFAULT NULL,
  `carls_mottatt` int(6) DEFAULT NULL,
  `carls_pafyll` int(6) DEFAULT NULL,
  `carls_avlevert` int(6) DEFAULT NULL,
  `carls_utavskap` int(6) DEFAULT NULL,
  `dato` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rikdom_mottatt` int(6) DEFAULT NULL,
  `rikdom_pafyll` int(6) DEFAULT NULL,
  `rikdom_avlevert` int(6) DEFAULT NULL,
  `rikdom_utavskap` int(6) DEFAULT NULL,
  PRIMARY KEY (`kryss_id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `krysseliste` (
`id` int(10) unsigned NOT NULL auto_increment,
  `beboer_id` int(10) unsigned NOT NULL,
  `drikke_id` int(10) unsigned NOT NULL,
  `krysseliste` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
)');

$db->query('CREATE TABLE IF NOT EXISTS `bruker_oppgave` (
  `bruker_id` int(8) unsigned NOT NULL,
  `oppgave_id` int(4) unsigned NOT NULL
)');

$db->query('CREATE TABLE IF NOT EXISTS `rolle` (
`id` int(10) unsigned NOT NULL auto_increment,
  `navn` varchar(20) CHARACTER SET latin1 NOT NULL,
  `regitimer` tinyint(3) unsigned DEFAULT \'0\',
  `vakter_h` tinyint(3) unsigned NOT NULL DEFAULT \'0\',
  `vakter_v` tinyint(3) unsigned NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`)
)');

$db->query('INSERT INTO `rolle` (`id`, `navn`, `regitimer`, `vakter_h`, `vakter_v`) VALUES
(1, \'Halv vakt/regi\', 18, 5, 6),
(2, \'Full vakt\', 0, 8, 9),
(3, \'Full regi\', 48, 0, 0)');

$db->query('TRUNCATE TABLE skole;');
$db->query('TRUNCATE TABLE studie;');
$db->query('TRUNCATE TABLE rom;');
$db->query('TRUNCATE TABLE bruker;');
$db->query('TRUNCATE TABLE beboer;');
$db->query('TRUNCATE TABLE verv;');
$db->query('TRUNCATE TABLE beboer_verv;');
$db->query('TRUNCATE TABLE vakt;');
$db->query('TRUNCATE TABLE vaktbytte;');
$db->query('TRUNCATE TABLE ansatt;');
$db->query('TRUNCATE TABLE prioritet;');
$db->query('TRUNCATE TABLE feilkategori;');
$db->query('TRUNCATE TABLE feil;');
$db->query('TRUNCATE TABLE kvittering;');
$db->query('TRUNCATE TABLE rapport;');
$db->query('TRUNCATE TABLE ansvarsomrade;');
$db->query('TRUNCATE TABLE ansvarsomrade_feil;');
$db->query('TRUNCATE TABLE bruker_ansvarsomrade;');
$db->query('TRUNCATE TABLE arbeidskategori;');
$db->query('TRUNCATE TABLE arbeid;');
$db->query('TRUNCATE TABLE oppgave;');
$db->query('TRUNCATE TABLE journal');
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

//Gammel => Ny
$mapBeboerIder = array();
$indeks = 1;
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

function byttTegnsettBeboer($streng)
{
    //return iconv(mb_detect_encoding($streng), 'utf-8', $streng);
    return iconv('windows-1252', 'utf-8', $streng);
//	return $streng;
}

while ($beboer = pg_fetch_array($hentBeboere)) {
    $brukerId = isset($beboerBrukerKobling[$beboer['beboer_id']]) ? $beboerBrukerKobling[$beboer['beboer_id']] : null;
    // Innføring av begrepet mellomnavn (alt mellom fornavn og etternavn)
    $mellomnavn = $beboer['fornavn'] . ' ' . $beboer['etternavn'];
    $mellomnavn = byttTegnsettBeboer($mellomnavn);
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
    $alkoholdepositum = $beboer['alkodepositum'] == 't' ? 1 : 0;
    $rolleId = $rolleIder[$beboer['oppgave_id']];
    $epost = $beboer['epost'] == null ? null : strtolower($beboer['epost']);
    $epost = iconv(mb_detect_encoding($epost), 'utf-8', $epost);
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
    $st->bindParam(':alkoholdepositum', $alkoholdepositum);
    $st->bindParam(':rolle_id', $rolleId);
    $st->bindParam(':epost', $epost);
    $st->bindParam(':romhistorikk', $romhistorikkJson);
    // Merk at bruker_id her ennå ikke er satt.
    $st->execute();
    $beboerIdFornyelse[$beboer['beboer_id']] = $db->lastInsertId();

    $mapBeboerIder[$beboer['beboer_id']] = $indeks;
    $indeks += 1;
}

foreach (BeboerListe::aktive() as $beboer) {
    if ($beboer->getBrukerId() == 0) {
        $st = $db->query('INSERT INTO bruker(passord) VALUES(\'testetest\');');
        $brukerId = $db->lastInsertId();
        $st = $db->query('UPDATE beboer SET bruker_id=' . $brukerId . ' WHERE id=' . $beboer->getId() . ';');
        $gammelIndex = array_search($beboer->getId(), $beboerIdFornyelse);
        $beboerBrukerKobling[$gammelIndex] = $brukerId;
    }
}

/* Migrering av beboere, slutt */

// Nye passord
$st = $db->query('INSERT INTO bruker(passord) VALUES(\'testetest\');');
$st = $db->query('INSERT INTO ansatt(bruker_id, fornavn, etternavn, epost) VALUES(' . $db->lastInsertId() . ', \'Torild\', \'Fivë\', \'torild@singsaker.no\');');
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

/* Migrering av vaktbytter, start */

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

/* Migrering av vaktbytter, slutt */

/* Fornyelse fra regiportalens brukerId, start */

$brukerIdFornyelse = array();
$hent = $regi->prepare('SELECT id,brukernavn,fornavn,etternavn FROM bruker;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('SELECT bruker_id FROM beboer WHERE epost LIKE :epost OR (fornavn LIKE :fornavn AND etternavn LIKE :etternavn) OR (LEFT(fornavn,LOCATE(\' \',fornavn) - 1) LIKE :fornavn AND etternavn LIKE :etternavn);');
    $epost = $rad['brukernavn'] . '@%';
    $fornavn = $rad['fornavn'] . '%';
    $etternavn = $rad['etternavn'] . '%';
    $st->bindParam(':epost', $epost);
    $st->bindParam(':fornavn', $fornavn);
    $st->bindParam(':etternavn', $etternavn);
    $st->execute();
    $ny = $st->fetch();
    $brukerIdFornyelse[$rad['id']] = $ny['bruker_id'];
}
// spesialtilfeller...
$brukerIdFornyelse[247] = 222;
$brukerIdFornyelse[389] = 357;

/* Fornyelse fra regiportalens brukerId, slutt */

/* Fra regiportalens stedId til romId, start */

$stedIdFornyelse = array();
$hent = $regi->prepare('SELECT id,navn FROM sted;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $stedIdFornyelse[$rad['id']] = Rom::medNavn($rad['navn'])->getId();
}

/* Fra regiportalens stedId til romId, slutt */

/* Migrering av prioritet, start */

$hent = $regi->prepare('SELECT * FROM prioritet;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO prioritet(
	navn,nummer,farge
) VALUES(
	:navn,:nummer,:farge
);');
    $st->bindParam(':navn', $rad['navn']);
    $st->bindParam(':nummer', $rad['nummer']);
    $st->bindParam(':farge', $rad['farge']);
    $st->execute();
}

/* Migrering av prioritet, slutt */

/* Migrering av feilkategori, start */

$hent = $regi->prepare('SELECT * FROM feilkategori;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO feilkategori(
	navn,beskrivelse,prioritet_id
) VALUES(
	:navn,:beskrivelse,:prioritet_id
);');
    $st->bindParam(':navn', $rad['navn']);
    $st->bindParam(':beskrivelse', $rad['beskrivelse']);
    $st->bindParam(':prioritet_id', $rad['prioritet_id']);
    $st->execute();
}

/* Migrering av feilkategori, slutt */

/* Migrering av feil, start */

$hent = $regi->prepare('SELECT * FROM feil;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO feil(
	navn,beskrivelse,feilkategori_id,prioritet_id,tid_oppretta
) VALUES(
	:navn,:beskrivelse,:feilkategori_id,:prioritet_id,:tid_oppretta
);');
    $st->bindParam(':navn', $rad['navn']);
    $st->bindParam(':beskrivelse', $rad['beskrivelse']);
    $st->bindParam(':feilkategori_id', $rad['feilkategori_id']);
    $st->bindParam(':prioritet_id', $rad['prioritet_id']);
    $st->bindParam(':tid_oppretta', $rad['tid_oppretta']);
    $st->execute();
}

/* Migrering av feil, slutt */

/* Migrering av kvittering, start */

$kvitteringIdFornyelse = array();
$hent = $regi->prepare('SELECT * FROM kvittering;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO kvittering(
	bruker_id,rom_id,tid_oppretta
) VALUES(
	:bruker_id,:rom_id,:tid_oppretta
);');
    $st->bindParam(':bruker_id', $brukerIdFornyelse[$rad['bruker_id']]);
    $st->bindParam(':rom_id', $stedIdFornyelse[$rad['sted_id']]);
    $st->bindParam(':tid_oppretta', $rad['tid_oppretta']);
    $st->execute();
    $kvitteringIdFornyelse[$rad['id']] = $db->lastInsertId();
}

/* Migrering av kvittering, slutt */

/* Migrering av rapport, start */

$rapportIdFornyelse = array();
$hent = $regi->prepare('SELECT * FROM rapport;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO rapport(
	kvittering_id,feil_id,oppgave_id,prioritet_id,godkjent,tid_endret,merknad,tid_godkjent,godkjent_bruker_id
) VALUES(
	:kvittering_id,:feil_id,:oppgave_id,:prioritet_id,:godkjent,:tid_endret,:merknad,:tid_godkjent,:godkjent_bruker_id
);');
    $st->bindParam(':kvittering_id', $kvitteringIdFornyelse[$rad['kvittering_id']]);
    $st->bindParam(':feil_id', $rad['feil_id']);
    $st->bindParam(':oppgave_id', $rad['oppgave_id']);
    $st->bindParam(':prioritet_id', $rad['prioritet_id']);
    $st->bindParam(':godkjent', $rad['godkjent']);
    $st->bindParam(':tid_endret', $rad['tid_endret']);
    $st->bindParam(':merknad', $rad['merknad']);
    $st->bindParam(':tid_godkjent', $rad['tid_godkjent']);
    $st->bindParam(':godkjent_bruker_id', $rad['godkjent_bruker_id']);
    $st->execute();
    $rapportIdFornyelse[$rad['id']] = $db->lastInsertId();
}

/* Migrering av rapport, slutt */

/* Migrering av ansvarsområde, start */

$hent = $regi->prepare('SELECT * FROM ansvarsomrade;');
$hent->execute();
while ($rad = $hent->fetch()) {
    if ($rad['navn'] == 'Admin') {
        continue;
    }
    $st = $db->prepare('INSERT INTO ansvarsomrade(
	navn,beskrivelse
) VALUES(
	:navn,:beskrivelse
);');
    $st->bindParam(':navn', $rad['navn']);
    $st->bindParam(':beskrivelse', $rad['beskrivelse']);
    $st->execute();
}

$hent = $regi->prepare('SELECT * FROM ansvarsomrade_feil;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO ansvarsomrade_feil(
	ansvarsomrade_id,feil_id
) VALUES(
	:ansvarsomrade_id,:feil_id
);');
    $st->bindParam(':ansvarsomrade_id', $rad['ansvarsomrade_id']);
    $st->bindParam(':feil_id', $rad['feil_id']);
    $st->execute();
}

$hent = $regi->prepare('SELECT * FROM bruker_ansvarsomrade;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO bruker_ansvarsomrade(
	bruker_id,ansvarsomrade_id
) VALUES(
	:bruker_id,:ansvarsomrade_id
);');
    $st->bindParam(':bruker_id', $brukerIdFornyelse[$rad['bruker_id']]);
    $st->bindParam(':ansvarsomrade_id', $rad['ansvarsomrade_id']);
    $st->execute();
}

/* Migrering av ansvarsområde, slutt */

/* Migrering av arbeidskategori, start */

$hent = $regi->prepare('SELECT * FROM arbeidskategori;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO arbeidskategori(
	navn,beskrivelse,tid_oppretta
) VALUES(
	:navn,:beskrivelse,:tid_oppretta
);');
    $st->bindParam(':navn', $rad['navn']);
    $st->bindParam(':beskrivelse', $rad['beskrivelse']);
    $st->bindParam(':tid_oppretta', $rad['tid_oppretta']);
    $st->execute();
}

/* Migrering av arbeidskategori, slutt */

/* Migrering av arbeid, start */
$hent = $regi->prepare('SELECT * FROM arbeid;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO arbeid(
	bruker_id,polymorfkategori_id,polymorfkategori_velger,tid_registrert,sekunder_brukt,tid_utfort,kommentar,godkjent,tid_godkjent,godkjent_bruker_id
) VALUES(
	:bruker_id,:polymorfkategori_id,:polymorfkategori_velger,:tid_registrert,:sekunder_brukt,:tid_utfort,:kommentar,:godkjent,:tid_godkjent,:godkjent_bruker_id
);');
    $st->bindParam(':bruker_id', $brukerIdFornyelse[$rad['bruker_id']]);
    $st->bindParam(':polymorfkategori_id', $rad['polymorfkategori_id']);
    $st->bindParam(':polymorfkategori_velger', $rad['polymorfkategori_velger']);
    $st->bindParam(':tid_registrert', $rad['tid_registrert']);
    $st->bindParam(':sekunder_brukt', $rad['sekunder_brukt']);
    $st->bindParam(':tid_utfort', $rad['tid_utfort']);
    $st->bindParam(':kommentar', $rad['kommentar']);
    $st->bindParam(':godkjent', $rad['godkjent']);
    $st->bindParam(':tid_godkjent', $rad['tid_godkjent']);
    $st->bindParam(':godkjent_bruker_id', $brukerIdFornyelse[$rad['godkjent_bruker_id']]);
    $st->execute();
}

/* Migrering av arbeid, slutt */

/* Migrering av oppgave, start */

$hent = $regi->prepare('SELECT * FROM oppgave;');
$hent->execute();
while ($rad = $hent->fetch()) {
    $st = $db->prepare('INSERT INTO oppgave(
	tid_oppretta,anslag_timer,ansvarsomrade_id,anslag_personer,prioritet_id,navn,beskrivelse,godkjent,tid_godkjent,godkjent_bruker_id
) VALUES(
	:tid_oppretta,:anslag_timer,:ansvarsomrade_id,:anslag_personer,:prioritet_id,:navn,:beskrivelse,:godkjent,:tid_godkjent,:godkjent_bruker_id
);');
    $st->bindParam(':tid_oppretta', $rad['tid_oppretta']);
    $st->bindParam(':anslag_timer', $rad['anslag_timer']);
    $st->bindParam(':ansvarsomrade_id', $rad['ansvarsomrade_id']);
    $st->bindParam(':anslag_personer', $rad['anslag_personer']);
    $st->bindParam(':prioritet_id', $rad['prioritet_id']);
    $st->bindParam(':navn', $rad['navn']);
    $st->bindParam(':beskrivelse', $rad['beskrivelse']);
    $st->bindParam(':godkjent', $rad['godkjent']);
    $st->bindParam(':tid_godkjent', $rad['tid_godkjent']);
    $st->bindParam(':godkjent_bruker_id', $rad['godkjent_bruker_id']);
    $st->execute();
}

/* Migrering av oppgave, slutt */

/* Lager Helga-tabell, start */
$st = DB::getDB()->prepare("CREATE TABLE `helga` 
( `aar` INT(4) NOT NULL , 
`start_dato` VARCHAR(10) NOT NULL , 
`slutt_dato` VARCHAR(10) NOT NULL , 
`generaler` VARCHAR(512) NOT NULL , 
`tema` VARCHAR(128) NOT NULL , 
`epost_text` TEXT NOT NULL ,
`klar` TINYINT(1) NOT NULL,
`max_gjest` INT(10) NOT NULL,
PRIMARY KEY (`aar`))");
$st->execute();

/* Lager Helga-tabell, slutt */

/* Lager helgagjest-tabell, start */

$st = DB::getDB()->prepare("CREATE TABLE `helgagjest` 
( `id` INT NOT NULL AUTO_INCREMENT ,
`navn` VARCHAR(512) NOT NULL ,
`epost` VARCHAR(128) NOT NULL ,
`vert` INT(10) NOT NULL ,
`dag` INT(10) NOT NULL ,
`sendt_epost` TINYINT(1) NOT NULL ,
`inne` TINYINT(1) NOT NULL ,
`aar` INT(4) NOT NULL ,
`klar` TINYINT(1) NOT NULL,
 PRIMARY KEY (`id`))");

$st->execute();
/* Lager helgagjest-tabell, slutt */


/* Oppsett av epost_preferanser start */

$beboerliste = BeboerListe::alle();

$st = DB::getDB()->prepare('CREATE TABLE epost_pref (
beboer_id INT(10) UNSIGNED NOT NULL,
tildelt TINYINT(1) NOT NULL DEFAULT 1,
snart_vakt TINYINT(1) NOT NULL DEFAULT 1,
bytte TINYINT(1) NOT NULL DEFAULT 1,
utleie TINYINT(1) NOT NULL DEFAULT 1,
barvakt TINYINT(1) NOT NULL DEFAULT 1
)');
$st->execute();

foreach ($beboerliste as $beboer) {
    $iden = $beboer->getId();
    $st = DB::getDB()->prepare('INSERT INTO epost_pref(beboer_id,tildelt,snart_vakt,bytte,utleie,barvakt
	) VALUES(
	:beboer_id,1,1,1,1,1)');
    $st->bindParam(':beboer_id', $iden);
    $st->execute();
}

/* Oppsett av epost_pref slutt */

/* Utleie-tabell, start */

$st = DB::getDB()->prepare('CREATE TABLE `utleie` (
`id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY , 
`dato` VARCHAR(512) NOT NULL,
`navn` VARCHAR(512) NOT NULL,
`beboer1_id` INT(10) NOT NULL,
`beboer2_id` INT(10) NOT NULL,
`beboer3_id` INT(10) NOT NULL,
`rom` VARCHAR(512) NOT NULL)');

$st->execute();

/* Utleie-tabell, slutt */


/* Lager fakturert-tabell for å holde oversikt over når øl blir fakturert. */

$st = DB::getDB()->prepare('CREATE TABLE fakturert (
id INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
dato VARCHAR(1024) NOT NULL
)');
$st->execute();

/* Slutt */

/* Migrering av kryssejournal start */
$hentKrysseliste = pg_query('SELECT * FROM krysseliste ORDER BY dato');

while ($krysset = pg_fetch_array($hentKrysseliste)) {
    if ($krysset['beboer_id'] == 0 || $krysset['beboer_id'] == 0) {
        continue;
    }
    $st = $db->prepare('INSERT INTO journal(kryss_id,beboer_id,vakt,ol_mottatt,ol_pafyll,ol_avlevert,ol_utavskap,
cid_mottatt,cid_pafyll,cid_avlevert,cid_utavskap,carls_mottatt,carls_pafyll,carls_avlevert,carls_utavskap,dato,
rikdom_mottatt,rikdom_pafyll,rikdom_avlevert,rikdom_utavskap) VALUES(:kryss_id,:beboer_id,:vakt,:ol_mottatt,:ol_pafyll,:ol_avlevert,:ol_utavskap,
:cid_mottatt,:cid_pafyll,:cid_avlevert,:cid_utavskap,:carls_mottatt,:carls_pafyll,:carls_avlevert,:carls_utavskap,:dato,
:rikdom_mottatt,:rikdom_pafyll,:rikdom_avlevert,:rikdom_utavskap)');

    $st->bindParam(':kryss_id', $krysset['kryss_id']);
    $st->bindParam(':beboer_id', $mapBeboerIder[$krysset['beboer_id']]);
    $st->bindParam(':vakt', $krysset['vakt']);

    $st->bindParam(':ol_mottatt', $krysset['ol_mottatt']);
    $st->bindParam(':ol_pafyll', $krysset['ol_pafyll']);
    $st->bindParam(':ol_avlevert', $krysset['ol_avlevert']);
    $st->bindParam(':ol_utavskap', $krysset['ol_utavskap']);

    $st->bindParam(':cid_mottatt', $krysset['cid_mottatt']);
    $st->bindParam(':cid_pafyll', $krysset['cid_pafyll']);
    $st->bindParam(':cid_avlevert', $krysset['cid_avlevert']);
    $st->bindParam(':cid_utavskap', $krysset['cid_utavskap']);

    $st->bindParam(':carls_mottatt', $krysset['carls_mottatt']);
    $st->bindParam(':carls_pafyll', $krysset['carls_pafyll']);
    $st->bindParam(':carls_avlevert', $krysset['carls_avlevert']);
    $st->bindParam(':carls_utavskap', $krysset['carls_utavskap']);

    $st->bindParam(':dato', $krysset['dato']);

    $st->bindParam(':rikdom_mottatt', $krysset['rikdom_mottatt']);
    $st->bindParam(':rikdom_pafyll', $krysset['rikdom_pafyll']);
    $st->bindParam(':rikdom_avlevert', $krysset['rikdom_avlevert']);
    $st->bindParam(':rikdom_utavskap', $krysset['rikdom_utavskap']);

    $st->execute();

}
/*Migrering av kryssejournal slutt*/
ferdig();
// Alt heretter går veldig sakte. TODO Endre ferdig() til senere før "ekte" migrering!
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

function ferdig()
{
    DB::getDB()->commit();
    exit();
}
echo "FIN";
?>