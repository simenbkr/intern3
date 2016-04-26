<?php

namespace intern3;

class RapportListe extends Liste {
	const sorteringskriterier = 'rapport,feil,rom,prioritet,kvittering';
	const databaseontologi    = 'rapport.feil_id=feil.id AND rapport.kvittering_id=kvittering.id AND kvittering.rom_id=rom.id AND rapport.prioritet_id=prioritet.id';

	public static function medId($ider=array())
	{
		$liste=array();
		foreach ($ider as $id) $liste[]=Rapport::medId($id);
		return $liste;
	}
	public static function medFeilId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND rapport.feil_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}
	public static function medFeilkategoriId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND feil.feilkategori_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}
	public static function medAnsvarsomradeId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.',ansvarsomrade_feil WHERE '.self::databaseontologi.' AND rapport.feil_id=ansvarsomrade_feil.feil_id AND ansvarsomrade_feil.ansvarsomrade_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}
	/*
	public static function medStedkategoriId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND sted.stedkategori_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}
	*/
	public static function medRomId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND rom.id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}
	public static function medPrioritetId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND prioritet.id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}
	public static function medOppgaveId($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND rapport.oppgave_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}

	public static function medBrukerId_brukerensEgne($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.' WHERE '.self::databaseontologi.' AND kvittering.bruker_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}

	public static function medBrukerId_brukerensAnsvarsomrade($id, $sortering='id' , $Sideinndeling = null)
	{
		$sql='SELECT rapport.id FROM '.self::sorteringskriterier.',ansvarsomrade_feil,bruker_ansvarsomrade WHERE '.self::databaseontologi.' AND rapport.feil_id=ansvarsomrade_feil.feil_id AND bruker_ansvarsomrade.ansvarsomrade_id=ansvarsomrade_feil.ansvarsomrade_id AND bruker_ansvarsomrade.bruker_id=:id';
		return self::LagListe($sql , $id , $sortering , $Sideinndeling);
	}

	private static function LagListe ($sql , $id , $sortering , $Sideinndeling = null) {
		$sql .= ' ORDER BY '.$sortering;
		unset($sortering);
		return self::listeFraSql('Rapport::medId' , $sql , array(':id' => $id) , $Sideinndeling);
	}
}

?>
