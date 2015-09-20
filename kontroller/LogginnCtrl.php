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
	private static function loggUt() {
		setcookie('brukernavn', '', -1);
		setcookie('passord', '', -1);
		Header('Location: ' . $_GET['ref']);
		exit();
	}
	private static function loggInn() {
		setcookie('brukernavn', $_POST['brukernavn'], $_SERVER['REQUEST_TIME'] + 31556926);
		setcookie('passord', self::genererHash($_POST['passord']), $_SERVER['REQUEST_TIME'] + 31556926);
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
			$salt = '$2y$11$' . substr(md5($passord . 'V@Q?0q%FCB5?iIB'), 0, 27);
			return crypt('Z\'3s+uc(WDk<,7Q' . crypt($passord, $salt), '$6$rounds=5000$VM5wn6AvwUOAdUO24oLzGQ$');
		}
		throw new \Exception('Sugefisk?');
	}
}

?>
