<?php

namespace intern3;

class OppgaveListe extends Liste {

	public static function alle ($Sideinndeling = null) {
	return self::listeFraSql('Oppgave::medId' , 'SELECT id FROM oppgave ORDER BY godkjent ASC,tid_oppretta DESC,id DESC' . /*$Grense .*/ ';' , array() , $Sideinndeling);
	}
	public static function alleSammen ($Start = false , $Lengde = false) { // Denne er teit
		if (is_int($Lengde)) {
			if (!is_numeric($Start)) {
			$Start = 0;
			}
		$Grense = ' limit ' . $Start . ',' . $Lengde;
		}
		else {
		$Grense = '';
		}
	return self::listeFraSql('Oppgave::medId' , 'SELECT id FROM oppgave order by godkjent,tid_oppretta,id' . $Grense . ';');
	}

	public static function ikkeGodkjente($Sideinndeling = null) {
		return self::listeFraSql('Oppgave::medId' , 'SELECT id FROM oppgave WHERE godkjent=0 ORDER BY prioritet_id DESC;');
	}

	public static function medBrukerId($bruker_id , $Sideinndeling = null) {
		$sql = 'SELECT oppgave.id FROM oppgave, oppgave_bruker WHERE oppgave.id=oppgave_bruker.oppgave_id AND oppgave_bruker.bruker_id=:brk_id ORDER BY godkjent ASC,tid_oppretta DESC,id DESC;';
		return self::listeFraSql('Oppgave::medId' , $sql , array(':brk_id' => $bruker_id) , $Sideinndeling);
	}

	public static function Antall () { // Denne er like teit som alleSammen
	$sql = 'select count(id) from oppgave;';
	$db = new DB();
	$st = $db->prepare($sql);
	$st->execute();
	$rl = $st->fetch();
	return $rl['count(id)'];
	}
}

?>
