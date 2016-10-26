<?php

namespace intern3;

class LogginnCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		if ($this->cd->getAktueltArg() == 'loggut') {
			$this->loggUt();
		}
		else if($this->cd->getAktueltArg() == 'passord'){
			$this->glemtPassord();
			exit();
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
		setcookie('brukernavn', $_POST['brukernavn'], $_SERVER['REQUEST_TIME'] + 31556926, NULL, NULL, NULL, TRUE);
		setcookie('passord', self::genererHash($_POST['passord']), $_SERVER['REQUEST_TIME'] + 31556926, NULL, NULL, NULL, TRUE);
		Header('Location: ' . $_SERVER['REQUEST_URI']);
		exit();
	}
	private function visSkjema() {
		$dok = new Visning($this->cd);
		$dok->set('skjulMeny', 1);
		$dok->vis('logginn.php');
	}

	private function glemtPassord(){
		$dok = new Visning($this->cd);
		if(isset($_POST) && isset($_POST['brukernavn'])) {
			$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
			$epost = $post['brukernavn'];
			$aktuellBruker = Bruker::medEpost($epost);

			if($aktuellBruker != null){
				$bruker_id = $aktuellBruker->getId();
				$nyttPassord = Funk::generatePassword();
				$hash = self::genererHash($nyttPassord);

				$st = DB::getDB()->prepare('UPDATE bruker SET passord=:passord WHERE id=:id');
				$st->bindParam(':passord', $hash);
				$st->bindParam(':id', $bruker_id);
				$st->execute();

				$beskjed = "<html><body>Hei<br/><br/>Du, eller noen som later som de er deg har forsøkt å resette ditt passord på <a href='https://intern.singsaker.no'>Internsidene</a><br/><br/>Ditt nye passord er : $nyttPassord<br/>Vi anbefaler deg om å logge inn og bytte passord så fort som mulig. Hvis du lurer på noe, ta kontakt med oss på epost: <a href='mailto:data@singsaker.no'>data@singsaker.no</a> eller ta turen innom.<br/><br/>Med vennlig hilsen<br/>Robottene på Singsaker<br/><br/>(Dette var en automagisk beskjed. Feil? Ta kontakt!)</body></html>";
				$tittel = "[SING-INTERN] Ditt passord har blitt resatt.";
				$sendEpost = new Epost($beskjed);
				$sendEpost->addBrukerId($bruker_id);
				$sendEpost->send($tittel);
				$dok->set('epostSendt', 1);
			}

		}
		$dok->set('skjulMeny',1);
		$dok->vis('glemtpassord.php');
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
