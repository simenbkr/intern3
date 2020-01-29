<?php


namespace intern3;


class UtvalgRegisjefRegivaktCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {
        if (!$this->cd->getAktivBruker()->getPerson()->harUtvalgVerv()) {
            return;
        }

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':
                return $this->handlePOST();
            case 'GET':
            default:
                if (strlen($this->cd->getAktueltArg()) > 0) {
                    return $this->handleGET();
                }

                $dok = new Visning($this->cd);
                $dok->vis('utvalg/regisjef/regivakt/oversikt.php');
                break;
        }

        return 0;
    }

    private function handleGET()
    {

        $dok = new Visning($this->cd);
        switch ($this->cd->getAktueltArg()) {


            case 'vis':
                $dok->set('dato', $this->cd->getSisteArg());
                $dok->vis('utvalg/regisjef/regivakt/add_modal.php');
                return;
            case 'administrer':
                $dok->set('rv', Regivakt::medId($this->cd->getSisteArg()));
                $dok->set('beboere', BeboerListe::aktiveMedRegi());
                $dok->vis('utvalg/regisjef/regivakt/administrer_modal.php');
                return;
        }

    }

    private function handlePOST()
    {

        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        switch ($this->cd->getAktueltArg()) {
            case 'underkjenn':
                if (!is_null(($rv = Regivakt::medId($post['rvid']))) && in_array($rv->getStatusInt(), [0, 1, 2])) {
                    $rv->underkjenn();
                    Funk::setSuccess('Regivakten ble underkjent!');
                    return;
                }
                break;
            case 'godkjenn':
                if (!is_null(($rv = Regivakt::medId($post['rvid']))) && in_array($rv->getStatusInt(), [0, 1, 3])) {
                    $rv->godkjenn();
                    Funk::setSuccess('Regivakten ble godkjent, og regi ført for deltakerene.');
                    return;
                }
                break;
            case 'slett':
                if (!is_null(($rv = Regivakt::medId($post['rvid'])))) {
                    $rv->slett();
                    Funk::setSuccess('Slettet regivakten!');
                    return;
                }
                break;
            case 'add':
                Regivakt::ny($post['dato'], $post['start'], $post['slutt'], $post['beskrivelse'], $post['nokkelord'],
                    $post['antall']);
                Funk::setSuccess('La til en ny regivakt!');
                return;

            case 'fjern':
                if (($bruker = Bruker::medId($post['brid'])) != null && ($rv = Regivakt::medId($post['rvid'])) != null) {
                    $rv->removeBrukerId($bruker->getId());
                }
                return;

            case 'endre':
                if (is_null(($rv = Regivakt::medId($post['rvid'])))) {
                    return;
                }

                $rv->setBeskrivelse($post['beskrivelse']);
                $rv->setDato($post['dato']);
                $rv->setSluttTid($post['slutt']);
                $rv->setStartTid($post['start']);
                $rv->setStatusInt($post['status']);
                $rv->setNokkelord($post['nokkelord']);
                $rv->lagre();

                Funk::setSuccess('Endret regivakten!');

            case 'add_bruker':
                if (is_null(($rv = Regivakt::medId($post['rvid'])))) {
                    return;
                }

                $rv->addBrukerId($post['brid']);

        }

    }
}