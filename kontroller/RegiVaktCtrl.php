<?php


namespace intern3;


class RegiVaktCtrl extends AbstraktCtrl implements CtrlInterface
{
    public function bestemHandling()
    {

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->handlePOST();
                break;
            case 'GET':
            default:
                $this->handleGET();
                break;
        }
    }

    private function handleGET()
    {

        $dok = new Visning($this->cd);
        switch ($this->cd->getAktueltArg()) {

            case 'se_forslag':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg()))) && $this->cd->getAktivBruker()->getId() == $bytte->getBrukerId()) {
                    $dok->set('bytte', $bytte);
                    $dok->vis('regi/regivakt/se_forslag_modal.php');
                }
                break;
            case 'forslag':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg()))) && !in_array($this->cd->getAktivBruker()->getId(),
                        $bytte->getRegivakt()->getBrukerIder())) {

                    $dok->set('bytte', $bytte);
                    $dok->set('egne_regivakter',
                        Regivakt::regivakterDetteSemesteretBruker($this->cd->getAktivBruker()->getId()));
                    $dok->set('aktiv_bruker', $this->cd->getAktivBruker());
                    $dok->vis('regi/regivakt/forslag_modal.php');
                }
                break;
            case 'gisbort':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg()))) && !in_array($this->cd->getAktivBruker()->getId(),
                        $bytte->getRegivakt()->getBrukerIder())) {

                    $dok->set('bytte', $bytte);
                    $dok->vis('regi/regivakt/gisbort_modal.php');
                }
                break;
            case 'vis_bytte':
                if (!is_null($rv = Regivakt::medId($this->cd->getSisteArg()))) {
                    $dok->set('rv', $rv);
                    $dok->vis('regi/regivakt/bytte_modal.php');
                }
                break;
            case 'bytte':
                $dok->set('aktiv_bruker', $this->cd->getAktivBruker());
                $dok->set('bytter', Regivaktbytte::liste());
                $dok->set('mine_vakter',
                    Regivakt::regivakterDetteSemesteretBruker($this->cd->getAktivBruker()->getId()));
                $dok->vis('regi/regivakt/byttemarked.php');
                return;
            case 'vis':
                if (!is_null($rv = Regivakt::medId($this->cd->getSisteArg()))) {
                    $dok->set('aktiv_bruker', $this->cd->getAktivBruker());
                    $dok->set('rv', $rv);
                    $dok->vis('regi/regivakt/detaljer_modal.php');
                    return;
                }
                break;
            case '':
            default:
                $dok->set('mine_vakter',
                    Regivakt::regivakterDetteSemesteretBruker($this->cd->getAktivBruker()->getId()));
                $dok->vis('regi/regivakt/oversikt.php');
        }
    }

    private function handlePOST()
    {

        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        switch ($this->cd->getAktueltArg()) {
            case 'godta':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg())))
                    && $this->cd->getAktivBruker()->getId() == $bytte->getBrukerId()) {

                    $bruker_id = explode(':', $post['rvidbrid'])[1];
                    $rvid = explode(':', $post['rvidbrid'])[0];

                    if (is_null(($forslaget = Regivakt::medId($rvid))) || !in_array($bruker_id,
                            $forslaget->getBrukerIder())) {
                        error_log("big yikes");
                        exit("Woopsidaisy");
                    }


                    $forslaget->removeBrukerId($bruker_id);
                    $forslaget->addBrukerId($bytte->getBrukerId());
                    $forslaget->setBytte(false);
                    $forslaget->lagre();

                    Regivaktbytte::fjernFraBytter($this->cd->getAktivBruker()->getId(), $bytte->getRegivakt()->getId());

                    $bytte->getRegivakt()->removeBrukerId($bytte->getBrukerId());
                    $bytte->getRegivakt()->addBrukerId($bruker_id);
                    $bytte->getRegivakt()->setBytte(false);
                    $bytte->getRegivakt()->lagre();
                    $bytte->slett();

                    Regivaktbytte::fjernFraBytter($bruker_id, $rvid);
                    Regivaktbytte::slettBytteFraVakt($rvid);
                    Regivaktbytte::slettBytteFraVakt($forslaget->getId());

                    Funk::setSuccess("Godtok bytteforslaget! Du er nå satt opp på {$forslaget->toString()}!");
                    header('Location: ?a=regi/regivakt/bytte');
                    exit();
                }
                break;
            case 'slett_forslag':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg()))) && !in_array($this->cd->getAktivBruker()->getId(),
                        $bytte->getRegivakt()->getBrukerIder()) && in_array($post['rvid'], $bytte->getForslagIder())) {

                    $bytte->slettForslag($this->cd->getAktivBruker()->getId(), $post['rvid']);
                    Funk::setSuccess('Slettet forslaget!');
                    header('Location: ?a=regi/regivakt/bytte');
                    exit();
                }
                break;
            case 'forslag':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg()))) && !in_array($this->cd->getAktivBruker()->getId(),
                        $bytte->getRegivakt()->getBrukerIder()) && !in_array($post['rvid'], $bytte->getForslagIder())) {

                    if ($bytte->harPassord() && !$bytte->riktigPassord($post['passord'])) {
                        Funk::setError('Feil passord!');
                        header('Location: ?a=regi/regivakt/bytte');
                        exit();
                    }


                    $bytte->leggTilForslag($this->cd->getAktivBruker()->getId(), $post['rvid']);
                    Funk::setSuccess('La til forslaget!');
                    header('Location: ?a=regi/regivakt/bytte');
                    exit();
                }
                break;
            case 'gisbort':
                if (!is_null(($bytte = Regivaktbytte::medId($this->cd->getSisteArg()))) && !in_array($this->cd->getAktivBruker()->getId(),
                        $bytte->getRegivakt()->getBrukerIder())) {

                    if ($bytte->harPassord() && !$bytte->riktigPassord($post['passord'])) {
                        Funk::setError('Feil passord!');
                        header('Location: ?a=regi/regivakt/bytte');
                        exit();
                    }

                    $bytte->getRegivakt()->removeBrukerId($bytte->getBrukerId());
                    $bytte->getRegivakt()->addBrukerId($this->cd->getAktivBruker()->getId());
                    $bytte->getRegivakt()->setBytte(false);
                    $bytte->getRegivakt()->lagre();
                    $bytte->slett();
                    Funk::setSuccess("Du tok regivakta {$bytte->getRegivakt()->medToString()}");
                    header('Location: ?a=regi/regivakt/bytte');
                    exit();
                }
                break;
            case 'leggtil':
                if (!is_null($rv = Regivakt::medId($this->cd->getSisteArg())) && in_array($this->cd->getAktivBruker()->getId(),
                        $rv->getBrukerIder())) {
                    $gisbort = ($post['byttes'] == 'byttes' ? 0 : 1);
                    $pw = $post['passordtekst'];
                    if ($post['passord'] === 'no') {
                        $pw = '';
                    }

                    Regivaktbytte::ny($this->cd->getAktivBruker()->getId(), $rv->getId(), $gisbort, $pw,
                        $post['merknad']);
                    $rv->setBytte(true);
                    $rv->lagre();
                    Funk::setSuccess('La ut regivakten på byttemarkedet!');
                    header('Location: ?a=regi/regivakt/bytte');
                    exit();
                }
                break;

            case 'fjern':
                if (!is_null($rv = Regivakt::medId($this->cd->getSisteArg()))
                    && !is_null(($rvb = Regivaktbytte::medRegivaktIdBrukerId($rv->getId(),
                        $this->cd->getAktivBruker()->getId())))
                    && $rvb->getBrukerId() == $this->cd->getAktivBruker()->getId()) {

                    $rvb->slett();
                    $rv->setBytte(false);
                    $rv->lagre();
                    Funk::setSuccess('Fjernet regivakten fra byttemarkedet!');
                }
                break;
            case 'blimed':
                if (!is_null(($rv = Regivakt::medId($post['id']))) && $rv->harPlass() && !in_array($this->cd->getAktivBruker()->getId(),
                        $rv->getBrukerIder()) && $rv->getStatusInt() == 0) {
                    $rv->addBrukerId($this->cd->getAktivBruker()->getId());
                    Funk::setSuccess("Du meldte deg på regivakta!");
                    return;
                } else {
                    Funk::setError('Du kan ikke bli med på denne regivakten!');
                }
                break;

        }
    }
}