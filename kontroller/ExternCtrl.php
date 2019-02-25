<?php

namespace intern3;


class ExternCtrl extends AbstraktCtrl
{

    private function validateRequest() {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        return $post['secret'] === SHARED_SECRET;
    }

    private function receiveSoknad() {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $name = $post['name'];
        $address = $post['address'];
        $email_address = $post['email'];
        $phone = $post['phone'];
        $birthyear = $post['birthyear'];
        $school = $post['school'];
        $studyfield = $post['studyfield'];
        $studyyear = $post['studyyear'];
        $fagbrev = $post['fagbrev'];
        $kompetanse = $post['kompetanse'];
        $kjennskap = $post['kjennskap'];
        $beboere = $post['beboere'];
        $personalletter = nl2br($post['personalletter']);
        $bilde_url = $post['bilde'];

        $tittel = "Søknad om plass fra {$name}";

        $message = '<html><body>';
        $message .= '<p><h2>Søknad om plass</h2>';
        $message .= $name .'<br>';
        $message .= $address . '</p>';
        $message .= '<p>Telefon: ' .$phone .'<br>';
        $message .= 'E-post: ' .$email_address .'</p>';
        $message .= '<p> født:' .$birthyear .'</p>';
        $message .= '<p> Studerer på ' .$school .' på ' .$studyyear .'. året ' .$studyfield .'</p>';
        $message .= '<p> Fagbrev: ' .$fagbrev .'<br>';
        $message .= 'Annen kompetanse: ' .$kompetanse .'</p>';
        $message .= '<p> Hørte om sing: ' .$kjennskap .'<br>';
        $message .= 'Kjenner: ' .$beboere .'</p>';
        $message .= '<h3>Søknadstekst</h3>' .$personalletter;
        $message .= "<br/><br/>Bilde: <img style='max-width: 300px' src='{$bilde_url}'/>";
        $message .= "</body></html>";

        Epost::sendEpost($email_address, $tittel, $message);
        Epost::sendEpost('data@singsaker.no', $tittel, $message);
        Epost::sendEpost('romsjef@singsaker.no', $tittel, $message);

    }

    public function bestemHandling() {

        if(!$this->validateRequest()) {
            exit("Error! Invalid shared secret!");
        }

        $aktueltArg = $this->cd->getAktueltArg();

        switch($aktueltArg) {
            case 'soknad':
                $this->receiveSoknad();
                break;
        }
    }

}