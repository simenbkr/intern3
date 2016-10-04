<?php

namespace intern3;

class UtvalgVaktsjefCtrl extends AbstraktCtrl {
	public function bestemHandling() {
		$aktueltArg = $this->cd->getAktueltArg();
		switch ($aktueltArg) {
      // case 'vaktstyring':
      //   $valgtCtrl = new UtvalgVaktsjefVaktstyringCtrl($this->cd->skiftArg());
      //   $valgtCtrl->bestemHandling();
      //   break;
      case 'generer':
        $valgtCtrl = new UtvalgVaktsjefGenererCtrl($this->cd->skiftArg());
        $valgtCtrl->bestemHandling();
        break;
			case 'vaktoversikt':
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
				break;
      case 'vaktstyring':
        if (isset($_POST['vaktId_1']) && isset($_POST['vaktId_2'])) {
          $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
          $vaktId_1 = $post['vaktId_1'];
          $vaktId_2 = $post['vaktId_2'];
          Vakt::byttVakt($vaktId_1, $vaktId_2);
          $page = '?a=utvalg/vakstsjef/vaktstyring';
          header('Location: '.$page, true, 303);
          exit;
        }
        $beboerListe = BeboerListe::harVakt();
        $torild = Ansatt::medId(1);
        $dok = new Visning($this->cd);
        $dok->set('torild', $torild);
        $dok->set('denneUka', @date('W'));
        $dok->set('detteAret', @date('Y'));
        $dok->set('beboerListe', $beboerListe);
        $dok->vis('utvalg_vaktsjef_vaktstyring.php');
        break;
      case 'vaktstyring_settvakt':
        if (isset($_POST['beboerId'])) {
          $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
          $beboerId = $post['beboerId'];
          $beboer = Beboer::medId($beboerId);
          if ($beboer == NULL) {
            exit();
          } else {
            $dok = new Visning($this->cd);
            $dok->set('visFerdig', 1);
            $dok->set('beboer', $beboer);
            $dok->vis('utvalg_vaktsjef_vaktstyring_settvakt.php');
          }
        }
        break;
      case 'vaktstyring_modal':
        $beboerListe = BeboerListe::harVakt();
        $dok = new Visning($this->cd);
        $dok->set('beboerListe', $beboerListe);
        $dok->vis('utvalg_vaktsjef_vaktstyring_modal.php');
        break;
      case 'ukerapport':
        $Uke = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
        $Aar = $this->cd->getArg($this->cd->getAktuellArgPos() + 2);
        if (is_numeric($Uke)) {
          $krysseinstans = new Journal($Uke, $Aar);
        } else {
          $krysseinstans = new Journal();
        }
        $dok = new Visning($this->cd);
        $dok->set('krysseting', $krysseinstans->getUkeKryss());
        $dok->set('journal', $krysseinstans->getKrysseInfo());
        $dok->vis('utvalg_vaktsjef_ukesrapport.php');
        break;
      case 'ukerapport_tabell':
        $Uke = $this->cd->getArg($this->cd->getAktuellArgPos() + 1);
        $Aar = $this->cd->getArg($this->cd->getAktuellArgPos() + 2);
        if (is_numeric($Uke)) {
          $krysseinstans = new Journal($Uke, $Aar);
        } else {
          $krysseinstans = new Journal();
        }
        $dok = new Visning($this->cd);
        $dok->set('krysseting', $krysseinstans->getUkeKryss());
        $dok->set('journal', $krysseinstans->getKrysseInfo());
        $dok->vis('utvalg_vaktsjef_ukesrapport_tabell.php');
        break;
			default:
				$dok = new Visning($this->cd);
				$dok->vis('utvalg_vaktsjef.php');
				break;
		}
	}
}

?>
