<?php

namespace intern3;

/*
 * Dette er en enum for arbeid.polymorfkategori_velger, som
 * bestemmer tolkningen av arbeid.polymorfkategori_id i SQL,
 * nemlig hva slags type tabell arbeid skal peke på.
 *
 * Også brukt i API-et til Arbeid.php.
 */

class ArbeidPolymorfkategori{
	//Endrer du disse, må du endre det som ligger i SQL-tabellene også:
	const YMSE = 0; //generelt arbeid, aka arbeidskategori
	const FEIL = 1; //generell feil
	const RAPP = 2; //spesifikk rapport
	const OPPG = 3; //spesifikk oppgave

	//siste element skal bare være høyest:
	const ANTALL = 4; //max + 1
}
?>