<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		if ($aktueltArg == 'vaktsjef') {
			$dok = new Visning($this->cd);
			$dok->vis('utvalg_vaktsjef.php');
		}
		else if ($aktueltArg == 'vaktoversikt') {
			$beboerListe = BeboerListe::harVakt();
			$antallVakter = Vakt::antallVakter();
			$antallUfordelte = Vakt::antallUfordelte();
			$antallUbekreftet = Vakt::antallUbekreftet();
			$dok = new Visning($this->cd);
			$dok->set('beboerListe', $beboerListe);
			$dok->set('antallVakter', $antallVakter);
			$dok->set('antallUfordelte', $antallUfordelte);
			$dok->set('antallUbekreftet', $antallUbekreftet);
			$dok->vis('utvalg_vaktsjef_vaktoversikt.php');
		}
		else if ($aktueltArg == 'vaktstyring') {
			$dok = new Visning($this->cd);
			$dok->set('denneUka', @date('W'));
			$dok->set('detteAret', @date('Y'));
			$dok->vis('utvalg_vaktsjef_vaktstyring.php');
		}
		else if ($aktueltArg == 'generer') {
			list($feilVarighet, $feilEnkelt, $feilPeriode) = array(array(), array(), array());
			do {
				if (!isset($_POST['generer'])) {
					break;
				}
				list($feilVarighet, $feilEnkelt, $feilPeriode) = $this->genererVaktliste();
				if (count($feilVarighet) > 0 || count($feilEnkelt) > 0 || count($feilPeriode) > 0) {
					break;
				}
				Header('Location: ?a=utvalg/vaktsjef/vaktstyring');
				exit();
			} while(false);
			$dok = new Visning($this->cd);
			$dok->set('feilVarighet', $feilVarighet);
			$dok->set('feilEnkelt', $feilEnkelt);
			$dok->set('feilPeriode', $feilPeriode);
			$dok->vis('utvalg_vaktsjef_generer.php');
		}
		else {
			$dok = new Visning($this->cd);
			$dok->vis('utvalg_vaktsjef.php');
		}
	}
	private function genererVaktliste() {
		$feilVarighet = $this->godkjennVaktlisteVarighet();
		$feilEnkelt = $this->godkjennVaktlisteEnkelt();
		$feilPeriode = $this->godkjennVaktlistePeriode();
		if (count($feilVarighet) == 0 && count($feilEnkelt) == 0 && count($feilPeriode) == 0) {
		}
		return array($feilVarighet, $feilEnkelt, $feilPeriode);
	}
	private function godkjennVaktlisteVarighet() {
		$feilVarighet = array();
		do {
			if (!isset($_POST['varighet_dato_start']) || !$_POST['varighet_dato_start']) {
				$feilVarighet[] = 'Startdato mangler.';
				break;
			}
			if (!Funk::erDatoGyldigFormat($_POST['varighet_dato_start'])) {
				$feilVarighet[] = 'Startdato må være i formatet åååå-mm-dd.';
				break;
			}
			if (!Funk::finsDato($_POST['varighet_dato_start'])) {
				$feilVarighet[] = 'Startdato er ugyldig, datoen fins ikke.';
				break;
			}
		} while(false);
		do {
			if (!isset($_POST['varighet_dato_slutt']) || !$_POST['varighet_dato_slutt']) {
				$feilVarighet[] = 'Sluttdato mangler.';
				break;
			}
			if (!Funk::erDatoGyldigFormat($_POST['varighet_dato_start'])) {
				$feilVarighet[] = 'Sluttdato må være i formatet åååå-mm-dd.';
				break;
			}
			if (!Funk::finsDato($_POST['varighet_dato_start'])) {
				$feilVarighet[] = 'Sluttdato er ugyldig, datoen fins ikke.';
				break;
			}
		} while(false);
		if (
				!isset($_POST['varighet_type_start']) || !$_POST['varighet_type_start']
				|| !isset($_POST['varighet_type_slutt']) || !$_POST['varighet_type_slutt']
				|| !in_array($_POST['varighet_type_start'], array("1", "2", "3", "4"))
				|| !in_array($_POST['varighet_type_slutt'], array("1", "2", "3", "4"))
		) {
			$feilVarighet[] = 'Velg mellom 1., 2., 3. eller 4. vakt.';
		}
		do {
			if (!isset($_POST['varighet_sikkerhetsmargin']) || !$_POST['varighet_sikkerhetsmargin']) {
				$feilVarighet[] = 'Sikkerhetsmargin mangler.';
				break;
			}
			if (!preg_match("/^[\d]+$/", $_POST['varighet_sikkerhetsmargin']) || $_POST['varighet_sikkerhetsmargin'] < 0) {
				$feilVarighet[] = 'Sikkerhetsmargin må være et positivt heltall eller 0.';
				break;
			}
		} while(false);
		return $feilVarighet;
	}
	private function godkjennVaktlisteEnkelt() {
		$feilEnkelt = array();
		$len = isset($_POST['enkeltvakt_type']) && isset($_POST['enkeltvakt_dato'])
						&& is_array($_POST['enkeltvakt_type']) && is_array($_POST['enkeltvakt_dato'])
				? max(count($_POST['enkeltvakt_type']), count($_POST['enkeltvakt_dato']))
				: 0;
		for ($i = 0; $i < $len; $i++) {
			if (!isset($_POST['enkeltvakt_type'][$i]) || !isset($_POST['enkeltvakt_dato'][$i]) || !$_POST['enkeltvakt_dato'][$i]) {
				unset($_POST['enkeltvakt_type'][$i], $_POST['enkeltvakt_dato'][$i]);
				continue;
			}
			do {
				if (!Funk::erDatoGyldigFormat($_POST['enkeltvakt_dato'][$i])) {
					$feilEnkelt['Dato må være i formatet åååå-mm-dd.'] = 1;
					break;
				}
				if (!Funk::finsDato($_POST['enkeltvakt_dato'][$i])) {
					$feilEnkelt['En dato er ugyldig, datoen fins ikke.'] = 1;
					break;
				}
			} while(false);
			if (
					!isset($_POST['enkeltvakt_type'][$i]) || !$_POST['enkeltvakt_type'][$i]
					|| !in_array($_POST['enkeltvakt_type'][$i], array("1", "2", "3", "4"))
			) {
				$feilEnkelt['Velg mellom 1., 2., 3. eller 4. vakt.'] = 1;
			}
		}
		$_POST['enkeltvakt_type'] = array_values($_POST['enkeltvakt_type']);
		$_POST['enkeltvakt_dato'] = array_values($_POST['enkeltvakt_dato']);
		return array_keys($feilEnkelt);
	}
	private function godkjennVaktlistePeriode() {
		$feilPeriode = array();
		$len = isset($_POST['vaktperiode_type_start']) && isset($_POST['vaktperiode_dato_start'])
						&& isset($_POST['vaktperiode_type_slutt']) && isset($_POST['vaktperiode_dato_slutt'])
						&& is_array($_POST['vaktperiode_type_start']) && is_array($_POST['vaktperiode_dato_start'])
						&& is_array($_POST['vaktperiode_type_slutt']) && is_array($_POST['vaktperiode_dato_slutt'])
				? max(
						count($_POST['vaktperiode_type_start']), count($_POST['vaktperiode_dato_start']),
						count($_POST['vaktperiode_type_slutt']), count($_POST['vaktperiode_dato_slutt'])
				)
				: 0;
		for ($i = 0; $i < $len; $i++) {
			if (!isset($_POST['vaktperiode_type_start'][$i]) || !isset($_POST['vaktperiode_dato_start'][$i]) || !$_POST['vaktperiode_dato_start'][$i] || !isset($_POST['vaktperiode_type_slutt'][$i]) || !isset($_POST['vaktperiode_dato_slutt'][$i]) || !$_POST['vaktperiode_dato_slutt'][$i]) {
				unset($_POST['vaktperiode_type_start'][$i], $_POST['vaktperiode_dato_start'][$i],
						$_POST['vaktperiode_type_slutt'][$i], $_POST['vaktperiode_dato_slutt'][$i]);
				continue;
			}
			do {
				if (!Funk::erDatoGyldigFormat($_POST['vaktperiode_dato_start'][$i]) || !Funk::erDatoGyldigFormat($_POST['vaktperiode_dato_slutt'][$i])) {
					$feilPeriode['Dato må være i formatet åååå-mm-dd.'] = 1;
					break;
				}
				if (!Funk::finsDato($_POST['vaktperiode_dato_start'][$i]) || !Funk::finsDato($_POST['vaktperiode_dato_slutt'][$i])) {
					$feilPeriode['En dato er ugyldig, datoen fins ikke.'] = 1;
					break;
				}
			} while(false);
			if (
					!isset($_POST['vaktperiode_type_start'][$i]) || !$_POST['vaktperiode_type_start'][$i]
					|| !in_array($_POST['vaktperiode_type_start'][$i], array("1", "2", "3", "4"))
					|| !isset($_POST['vaktperiode_type_slutt'][$i]) || !$_POST['vaktperiode_type_slutt'][$i]
					|| !in_array($_POST['vaktperiode_type_slutt'][$i], array("1", "2", "3", "4"))
			) {
				$feilPeriode['Velg mellom 1., 2., 3. eller 4. vakt.'] = 1;
			}
		}
		$_POST['vaktperiode_type_start'] = array_values($_POST['vaktperiode_type_start']);
		$_POST['vaktperiode_dato_start'] = array_values($_POST['vaktperiode_dato_start']);
		$_POST['vaktperiode_type_slutt'] = array_values($_POST['vaktperiode_type_slutt']);
		$_POST['vaktperiode_dato_slutt'] = array_values($_POST['vaktperiode_dato_slutt']);
		return array_keys($feilPeriode);
	}
}

?>
