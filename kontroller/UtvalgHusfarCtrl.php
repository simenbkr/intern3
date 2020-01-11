<?php


namespace intern3;

use Group\GroupManage;

const RAADET = 'raadet@singsaker.no';

class UtvalgHusfarCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {

        switch ($this->cd->getAktueltArg()) {

            case 'epost':
                $group = new GroupManage();

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
                    if (Funk::isValidEmail($post['epost'])) {
                        $group->addToGroup($post['epost'], 'MEMBER', RAADET);
                        print("La til {$post['epost']} i Rådets epostliste!");
                        exit();
                    }
                    print("Noe gikk galt! Er {$post['epost']} en gyldig e-postadresse?");
                    exit();

                } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    if (Funk::isValidEmail($this->cd->getSisteArg())) {
                        $group->removeFromGroup($this->cd->getSisteArg(), RAADET);
                        print("Sletta {$this->cd->getSisteArg()} fra Rådets epostliste!");
                        exit();
                    }
                    print("Fikk ikke sletta denne eposten. Forsøk å gjøre det manuelt.");
                    exit();
                } else {

                    $deltakere = $group->listGroup(RAADET);

                    $dok = new Visning($this->cd);
                    $dok->set('deltakere', $deltakere);
                    $dok->vis('utvalg/husfar/epost.php');

                }

        }


    }

}