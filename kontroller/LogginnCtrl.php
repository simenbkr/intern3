<?php

namespace intern3;

class LogginnCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		if ($this->cd->getAktueltArg()=='loggut') {
			Header('Location:'.$_GET['ref']);
			exit();
		}
		else if (isset($_POST['brukernavn'])&&isset($_POST['passord'])) {
			setcookie("brukernavn", $_POST['brukernavn']);
			setcookie("passord", crypt($_POST['passord'], $bruker->passord), time()+60*60*24*365); //må krypteres
			Header('Location:'.$_SERVER['REQUEST_URI']);
			exit();
		}
		$dok = new Visning($this->cd);
		$dok->vis('logginn.php');
	}
	public static function getAktivBruker() {
		function verify($passord, $hash) {
			return crypt($passord, $hash) == $hash;
		}
		return NULL;
	}
	public static function genererHash($passord) {
		if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
			$salt = '$2y$11$' . substr(md5(uniqid(mt_rand(), true)), 0, 22);
			return crypt($passord, $salt);
		}
	}
}

?>
