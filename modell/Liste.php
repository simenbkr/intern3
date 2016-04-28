<?php

namespace intern3;

abstract class Liste {
	protected static function listeFraSql($ListeFn , $sql , $param = array() , SideinndelData $Sideinndeling = null) {
		do {
			if ($Sideinndeling == null) {
			break; // Ingen sideinndeling
			}
			if (!$Sideinndeling->getPerSide()) {
			break; // Ingen sideinndeling
			}
		/* Her følges $Sideinndeling */
		preg_match("/[\s\n]*select[\s\n]+([\(\)\w\s\n\,\.]+)[\s\n]+from/i" , $sql , $Arg);
		$Sekv = $Arg[0];
		$Arg0 = $Arg[1];
		$Arg = $Arg0;
			if ($Arg0{0} == '(' && substr($Arg0 , -1) == ')') {
			$Arg0 = substr($Arg0 , 1 , -2);
				if (strpos($Arg0 , ',')) {
				$Arg0 = explode(',' , $Arg0);
				$Arg0 = $Arg0[0];
				}
			$Arg0 = trim($Arg0);
			} // $Arg er nå argumentene i returlista i spørringa, der $Arg0 er det første.
		$sql_ant = str_replace($Sekv , 'SELECT COUNT(' . $Arg0 . ') FROM' , $sql); // For telling av antall rader
		$db = DB::getDB();
		$st = $db->prepare($sql_ant);
			foreach ($param as $nok => $ver) {
			$st->bindParam($nok , $ver);
			}
		$st->execute();
		$rl = $st->fetch();
		$Sideinndeling->setAntall($rl[0]); // samme som $rl['COUNT($Arg0)']
		/* $Sideinndeling kan regne ut nåværende side */
		$sql = rtrim($sql , ';'); // Klart for innsetting av LIMIT
		$sql .= ' LIMIT ' . $Sideinndeling->getStart() . ',' . $Sideinndeling->getPerSide() . ';';
		} while (false);
	/* Med eller uten sideinndeling herfra. Med sideinndeling er $sql endra. */
		if ($ListeFn == 'Arbeid::medId') { // Dette er VELDIG stygt, men nødvendig når PDO har drekki maling
			foreach ($param as $nok => $ver) {
			//var_dump($nok , $ver);
				if (!strpos($ver , '\'')) {
				$sql = str_replace($nok , '\'' . $ver . '\'' , $sql);
				unset($param[$nok]);
				}
			}
		}
	$db = DB::getDB();
	$st = $db->prepare($sql);
		foreach ($param as $nok => $ver) {
		$st->bindParam($nok , $ver);
		}
	//echo $sql;
	return self::listeFraPdo($ListeFn , $st);
	}
	protected static function listeFraPdo ($ListeFn , $st) {
	$ListeFn = __NAMESPACE__ . '\\' . $ListeFn;
	$st->execute();
	$liste = array();
		if (strpos($ListeFn , '::')) { // hvis funksjonen er i en klasse
		$ListeFn = explode('::' , $ListeFn); // indeks 0 er klassenavn, og 1 er funksjonsnavn
		$Objekt = new $ListeFn[0];
			while($rl = $st->fetch()) {
			$liste[] = call_user_func(array(&$Objekt , $ListeFn[1]) , $rl[0]); // anta at indeks 0 er 'id'
			}
		}
		else {
			while($rl = $st->fetch()) {
			$liste[] = $ListeFn($rl[0]); // anta at indeks 0 er 'id'
			}
		}
	return $liste;
	}
}

?>