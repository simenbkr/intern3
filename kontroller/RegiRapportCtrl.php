<?php

namespace intern3;

class RegiRapportCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$sortering = 'rapport.godkjent ASC,prioritet.nummer DESC,kvittering.tid_oppretta ASC,rapport.id ASC';
		$sideinndeling = null;
		$ekstraTittel = array();
		$type = $this->cd->getAktueltArg();
		$id = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
		$rapportListe = array();
		if ($id <> null) {
			switch ($type) {
				case 'ansvarsomrade':
					$ekstraTittel[] = Ansvarsomrade::medId($id)->getNavn();
					$rapportListe = RapportListe::medAnsvarsomradeId($id, $sortering, $sideinndeling);
					break;
				case 'feilkategori':
					$ekstraTittel[] = Feilkategori::medId($id)->getNavn();
					$rapportListe = RapportListe::medFeilkategoriId($id, $sortering, $sideinndeling);
					break;
				case 'feil':
					$feil = Feil::medId($id);
					$ekstraTittel[] = $feil->getFeilkategori()->getNavn();
					$ekstraTittel[] = $feil->getNavn();
					$rapportListe = RapportListe::medFeilId($id, $sortering, $sideinndeling);
					break;
				case 'rom':
					$ekstraTittel[] = Rom::medId($id)->getNavn();
					$rapportListe = RapportListe::medRomId($id, $sortering, $sideinndeling);
					break;
				case 'prioritet':
					$ekstraTittel[] = Prioritet::medId($id)->getNavn();
					$rapportListe = RapportListe::medPrioritetId($id, $sortering, $sideinndeling);
					break;
			}
		}
		$dok = new Visning($this->cd);
		$dok->set('ekstraTittel', $ekstraTittel);
		$dok->set('rapportListe', $rapportListe);
		$dok->vis('regi_rapport.php');
	}
}

?>
