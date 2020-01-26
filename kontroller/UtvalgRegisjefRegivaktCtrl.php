<?php


namespace intern3;


class UtvalgRegisjefRegivaktCtrl extends AbstraktCtrl
{
    public function bestemHandling()
    {

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
                $dok->vis('utvalg/regisjef/regivakt/administrer_modal.php');
                return;
        }

    }

    private function handlePOST()
    {

        switch ($this->cd->getAktueltArg()) {

            case 'add':
                $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                Regivakt::ny($post['dato'], $post['start'], $post['slutt'], $post['beskrivelse'], $post['nokkelord'], $post['antall']);
                Funk::setSuccess('La til en ny regivakt!');
                return;
        }

    }
}