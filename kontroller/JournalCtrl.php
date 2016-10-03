<?php

namespace intern3;

class JournalCtrl extends AbstraktCtrl {
	public function bestemHandling() {
    if (LogginnCtrl::getAktivBruker() != null) {
      $aktivBruker = LogginnCtrl::getAktivBruker();
      if ($aktivBruker->getPerson()->harUtvalgVerv()) {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
          case 'journal':
          default:
            setcookie('brukernavn', 'journal');
            setcookie('passord', '', -1);
            setcookie('du', '', -1);
            Header('Location: ' . $_GET['ref']);
    				$dok = new Visning($this->cd);
            $dok->set('success', 1);
            $dok->set('skjulMeny', 1);
    				$dok->vis('journal.php');
            break;
        }
      }
    } else if (isset($_COOKIE['brukernavn'])) {
      if ($_COOKIE['brukernavn'] == 'journal') {
        $aktueltArg = $this->cd->getAktueltArg();
        switch ($aktueltArg) {
          case 'journal':
            $dok = new Visning($this->cd);
            $dok->set('skjulMeny', 1);
            $dok->vis('journal.php');
            break;
          default:
            $dok = new Visning($this->cd);
        		$dok->set('skjulMeny', 1);
        		$dok->set('visError', 1);
            $dok->vis('logginn.php');
        }
      }
    } else {
        $dok = new Visning($this->cd);
    		$dok->set('skjulMeny', 1);
    		$dok->set('visError', 1);
        $dok->vis('logginn.php');
    }
  }
}

?>
