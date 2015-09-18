<?php

namespace intern3;

class LogginnCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		if ($this->cd->getAktueltArg() == 'loggut') {
			$this->loggUt();
		}
		else if (isset($_POST['brukernavn']) && isset($_POST['passord'])) {
			$this->loggInn();
		}
		$this->visSkjema();
	}
	private function loggUt() {
		setcookie('brukernavn', '', -1);
		setcookie('passord', '', -1);
		Header('Location: ' . $_GET['ref']);
		exit();
	}
	private function loggInn() {
		$bruker = Bruker::medEpost($_POST['brukernavn']);
		if ($bruker <> null) {
			$passordHash = $bruker->getPassordHash($_POST['passord']);
			setcookie('brukernavn', $_POST['brukernavn'], $_SERVER['REQUEST_TIME'] + 31556926);
			setcookie('passord', $passordHash, $_SERVER['REQUEST_TIME'] + 31556926);
		}
		Header('Location: ' . $_SERVER['REQUEST_URI']);
		exit();
	}
	private function visSkjema() {
		$dok = new Visning($this->cd);
		$dok->set('skjulMeny', 1);
		$dok->vis('logginn.php');
	}
	public static function getAktivBruker() {
		if (!isset($_COOKIE['brukernavn']) || !isset($_COOKIE['passord'])) {
			return null;
		}
		$bruker = Bruker::medEpost($_COOKIE['brukernavn']);
		if ($bruker == null) {
			return null;
		}
		if (!$bruker->passordErGyldig($_COOKIE['passord'])) {
			return null;
		}
		return $bruker;
	}
	public static function genererHash($passord) {
		if (defined('CRYPT_BLOWFISH') && CRYPT_BLOWFISH) {
			$salt = '$2y$11$' . substr(md5(uniqid(mt_rand(), true)), 0, 22);
			return crypt($passord, $salt);
		}
		throw new \Exception('Sugefisk?');
	}
}

?>
