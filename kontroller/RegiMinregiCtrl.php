<?php

namespace intern3;

class RegiMinregiCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$feil = array();
		if (isset($_POST['registrer'])) {
			$feil = $this->registrerArbeid();
			if (count($feil) == 0) {
				header('Location: ' . $_SERVER['REQUEST_URI']);
				exit();
			}
		}
		$arbeidListe = ArbeidListe::medBrukerIdSemester($this->cd->getAktivBruker()->getId());
		$regitimer = array(0, 0);
		foreach ($arbeidListe as $arbeid) {
			$regitimer[$arbeid->getGodkjent() ? 1 : 0] += $arbeid->getSekunderBrukt() / 3600;
		}
		$dok = new Visning($this->cd);
		$dok->set('feil', $feil);
		$dok->set('regitimer', $regitimer);
		$dok->set('arbeidListe', $arbeidListe);
		$dok->vis('regi_minregi.php');
	}
	private function registrerArbeid() {
		$feil = $this->godkjennArbeid();
		if (count($feil) == 0) {
			$endringer = array(
					'bruker_id' => ':bruker_id',
					'polymorfkategori_id' => ':polymorfkategori_id',
					'polymorfkategori_velger' => ':polymorfkategori_velger',
					'sekunder_brukt' => ':sekunder_brukt',
					'tid_utfort' => ':tidUtfort',
					'kommentar' => ':kommentar'
					);
			$parametre = array(
					':bruker_id' => $this->cd->getAktivBruker()->getId(),
					':polymorfkategori_id' => $_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']],
					':polymorfkategori_velger' => $this->getPolymorfkategoriVelger(),
					':sekunder_brukt' => $this->getSekunderBrukt(),
					':tidUtfort' => $_POST['tid_utfort'],
					':kommentar' => $_POST['kommentar']
			);
			$sql = 'INSERT INTO arbeid(' . implode(',', array_keys($endringer)) . ') VALUES(' . implode(',', $endringer) . ');';
			$st = DB::getDB()->prepare($sql);
			foreach ($parametre as $navn => $verdi) {
				$st->bindValue($navn, $verdi);
			}
			$st->execute();
		}
		return $feil;
	}
	private function godkjennArbeid() {
		$feil = array();
		do {
			if (!isset($_POST['polymorfkategori_velger']) || !$_POST['polymorfkategori_velger']) {
				$feil[] = 'Tilhørighet mangler.';
				break;
			}
			if ($this->getPolymorfkategoriVelger() == -1) {
				$feil[] = 'Valgt tilhørighet fins ikke.';
				break;
			}
		} while(false);
		do {
			if (!isset($_POST['polymorfkategori_id']) || !isset($_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']]) || !$_POST['polymorfkategori_id'][$_POST['polymorfkategori_velger']]) {
				$feil[] = 'Kategori mangler.';
				break;
			}
		} while(false);
		do {
			if (!isset($_POST['tid_utfort']) || !$_POST['tid_utfort']) {
				$feil[] = 'Utførelsesdato mangler.';
				break;
			}
			if (!Funk::erDatoGyldigFormat($_POST['tid_utfort'])) {
				$feil[] = 'Utførelsesdato må være i formatet åååå-mm-dd.';
				break;
			}
			if (!Funk::finsDato($_POST['tid_utfort'])) {
				$feil[] = 'Utførelsesdato er ugyldig, datoen fins ikke.';
				break;
			}
		} while(false);
		do {
			if (!isset($_POST['tid_brukt']) || !$_POST['tid_brukt']) {
				$feil[] = 'Tid brukt mangler.';
				break;
			}
			if (
					!preg_match('/^[0-9]+(\:[0-9]{2})?$/', $_POST['tid_brukt'])
					&& !preg_match('/^[0-9]+(\,[0-9]+)?$/', $_POST['tid_brukt'])
					&& !preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['tid_brukt'])
			) {
				$feil[] = 'Tid brukt må være på formatet timer:minutter eller timer som desimaltall.';
				break;
			}
			if ($this->getSekunderBrukt() == 0) {
				$feil[] = 'Tid brukt må være noe annet enn 0.';
				break;
			}
		} while(false);
		if (!isset($_POST['kommentar']) || !$_POST['kommentar']) {
			$feil[] = 'Kommentar mangler.';
		}
		return $feil;
	}
	private function getSekunderBrukt() {
		if (preg_match('/^([0-9]+)$/', $_POST['tid_brukt'], $treff)) {
			return $treff[1] * 3600;
		}
		if (preg_match('/^([0-9]+)(\:([0-9]{2}))?$/', $_POST['tid_brukt'], $treff)) {
			return $treff[1] * 3600 + $treff[3] * 60;
		}
		if (preg_match('/^[0-9]+(\,[0-9]+)?$/', $_POST['tid_brukt'])) {
			return str_replace(',', '.', $_POST['tid_brukt']) * 3600;
		}
		if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $_POST['tid_brukt'])) {
			return $_POST['tid_brukt'] * 3600;
		}
		return 0;
	}
	private function getPolymorfkategoriVelger() {
		$polymorf = -1;
		if (!isset($_POST['polymorfkategori_velger'])) {
			return $polymorf;
		}
		switch ($_POST['polymorfkategori_velger']) {
			case 'ymse':
				$polymorf = ArbeidPolymorfkategori::YMSE;
				break;
			case 'feil':
				$polymorf = ArbeidPolymorfkategori::FEIL;
				break;
			case 'rapp':
				$polymorf = ArbeidPolymorfkategori::RAPP;
				break;
			case 'oppg':
				$polymorf = ArbeidPolymorfkategori::OPPG;
				break;
			default:
				$polymorf = -1;
				break;
		}
		return $polymorf;
	}
}

?>
