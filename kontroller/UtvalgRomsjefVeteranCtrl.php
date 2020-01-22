<?php

namespace intern3;

use Group\GroupManage;

const VETERAN = 'sing-veteran@singsaker.no';

class UtvalgRomsjefVeteranCtrl extends AbstraktCtrl
{

    public function bestemHandling()
    {
        $group = new GroupManage();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (Funk::isValidEmail($post['epost'])) {
                $group->addToGroup($post['epost'], 'MEMBER', VETERAN);
                print("La til {$post['epost']} i Veteranlista!");
                exit();
            }
            print("Noe gikk galt! Er {$post['epost']} en gyldig e-postadresse?");
            exit();

        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if (Funk::isValidEmail($this->cd->getSisteArg())) {
                $group->removeFromGroup($this->cd->getSisteArg(), VETERAN);
                print("Sletta {$this->cd->getSisteArg()} fra Rådets epostliste!");
                exit();
            }
            print("Fikk ikke sletta denne eposten. Forsøk å gjøre det manuelt.");
            exit();
        } else {

            $deltakere = $group->listGroup(VETERAN, 9999);

            $dok = new Visning($this->cd);
            $dok->set('deltakere', $deltakere);
            $dok->vis('utvalg/romsjef/veteran.php');

        }

    }

}