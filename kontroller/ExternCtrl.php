<?php

namespace intern3;


class ExternCtrl extends AbstraktCtrl
{

    private function validateRequest()
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        return $post['secret'] === SHARED_SECRET;
    }

    private function receiveSoknad()
    {
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
        $message .= $name . '<br>';
        $message .= $address . '</p>';
        $message .= '<p>Telefon: ' . $phone . '<br>';
        $message .= 'E-post: ' . $email_address . '</p>';
        $message .= '<p> født:' . $birthyear . '</p>';
        $message .= '<p> Studerer på ' . $school . ' på ' . $studyyear . '. året ' . $studyfield . '</p>';
        $message .= '<p> Fagbrev: ' . $fagbrev . '<br>';
        $message .= 'Annen kompetanse: ' . $kompetanse . '</p>';
        $message .= '<p> Hørte om sing: ' . $kjennskap . '<br>';
        $message .= 'Kjenner: ' . $beboere . '</p>';
        $message .= '<h3>Søknadstekst</h3>' . $personalletter;
        $message .= "<br/><br/>Bilde: <img style='max-width: 300px' src='{$bilde_url}'/>";
        $message .= "</body></html>";

        Epost::sendEpost_replyto($email_address, $tittel, $message, 'romsjef@singsaker.no');
        Epost::sendEpost('data@singsaker.no', $tittel, $message);
        Epost::sendEpost_replyto('romsjef@singsaker.no', $tittel, $message, $email_address);


        $st = DB::getDB()->prepare('INSERT INTO soknad(navn, adresse, epost, telefon, fodselsar, skole, studie, fagbrev, kompetanse, kjennskap, kjenner, tekst, bilde)
                                                 VALUES(:navn,:adresse,:epost,:telefon,:fodselsar,:skole,:studie,:fagbrev,:kompetanse,:kjennskap,:kjenner,:tekst,:bilde)');
       /* $st->execute(['navn' => $post['navn'], 'adresse' => $post['adresse'], 'epost' => $post['epost'], 'telefon' => $post['telefon'], 'fodselsar' => $post['fodselsar'],
            'skole' => $post['skole'], 'studie' => $post['studie'], 'fagbrev' => $post['fagbrev'], 'kompetanse' => $post['kompetanse'],
            'kjennskap' => $post['kjennskap'], 'kjenner' => $post['kjenner'], 'tekst' => $post['tekst'], 'bilde' => $post['bilde']]);*/
        $st->execute(['navn' => $name, 'adresse' => $address,  'epost' => $email_address,  'telefon' => $phone,
            'fodselsar' => $birthyear, 'skole' => $school,  'studie' => $studyfield,  'fagbrev' => $fagbrev,  'kompetanse' => $kompetanse,  'kjennskap' => $kjennskap,
            'kjenner' => $beboere,  'tekst' => $personalletter,  'bilde' => $bilde_url]);

    }

    public function bestemHandling()
    {

        if (!$this->validateRequest()) {
            exit("Error! Invalid shared secret!");
        }

        $aktueltArg = $this->cd->getAktueltArg();

        switch ($aktueltArg) {
            case 'soknad':
                $this->receiveSoknad();
                break;
        }
    }

}