<?php


namespace intern3;

use Group\GroupManage;
const JUBILEUMLISTE = 'jubileum@singsaker.no';

class JubileumCtrl extends AbstraktCtrl implements CtrlInterface
{

    public function bestemHandling()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->handlePOST();
                break;
            case 'DELETE':
                $this->handleDEL();
                break;
            case 'GET':
            default:
                $this->handleGET();
        }
    }

    private function handlePOST()
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $group = new GroupManage();
        switch ($this->cd->getAktueltArg()) {
            case 'single':
                if (Funk::isValidEmail($post['epost'])) {
                    $group->addToGroup($post['epost'], 'MEMBER', JUBILEUMLISTE);
                    print("La til {$post['epost']} i Jubileumets epostliste!");
                    exit();
                }
                break;
            case 'multiple':
                $i = 0;
                $fp = fopen($_FILES['tekstfil']['tmp_name'], 'r');
                while (($line = fgets($fp)) !== false) {
                    $epost = rtrim($line);

                    if (strpos($epost, '@') !== false && strlen($epost) > 4) {
                        try {
                            $group->addToGroup($epost, 'MEMBER', JUBILEUMLISTE);
                            $i++;
                        } catch (\Exception $e) {
                            error_log("Google likte visst ikke $epost som en gyldig epostadresse...");
                        }
                    }

                }

                Funk::setSuccess("La til {$i} personer på Jubileumslista!");
                header('Location: ?a=jubileum');
                exit();

                break;
        }
    }

    private function handleDEL()
    {
        $group = new GroupManage();
        if (Funk::isValidEmail($this->cd->getSisteArg())) {
            $group->removeFromGroup($this->cd->getSisteArg(), JUBILEUMLISTE);
            print("Sletta {$this->cd->getSisteArg()} fra Jubileumets epostliste!");
            exit();
        }
    }

    private function handleGET()
    {

        switch ($this->cd->getAktueltArg()) {
            case 'eksempel':
                print "EPOSTADRESSE<br/>navn@navnesen.no<br/>ola@nordmann.no<br/>sing@star.com";
                break;
            case '':
            default:
                $group = new GroupManage();
                $deltakere = $group->listGroup(JUBILEUMLISTE);

                $dok = new Visning($this->cd);
                $dok->set('deltakere', $deltakere);
                $dok->vis('jubileum/main.php');
                break;
        }
    }


}