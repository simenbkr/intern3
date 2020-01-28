<?php


namespace intern3;


class RegiVaktCtrl extends AbstraktCtrl implements CtrlInterface
{
    public function bestemHandling() {

        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->handlePOST();
                break;
            case 'GET':
            default:
                $this->handleGET();
                break;
        }
    }

    private function handleGET() {

        $dok = new Visning($this->cd);
        switch ($this->cd->getAktueltArg()) {

            case 'vis':
                if(!is_null($rv = Regivakt::medId($this->cd->getSisteArg()))) {
                    $dok->set('rv', $rv);
                    $dok->vis('regi/regivakt/detaljer_modal.php');
                    return;
                }
                break;
            case '':
            default:
                $dok->vis('regi/regivakt/oversikt.php');
        }
    }

    private function handlePOST() {

        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        switch($this->cd->getAktueltArg()) {

            case 'blimed':
                if(!is_null(($rv = Regivakt::medId($post['id']))) && $rv->harPlass()) {
                    $rv->addBrukerId($this->cd->getAktivBruker()->getId());
                    Funk::setSuccess("Du meldte deg på regivakta!");
                    return;
                }
                break;

        }

    }

}