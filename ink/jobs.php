<?php

namespace intern3;

require_once("/var/www/intern.singsaker.no/ink/autolast_absolute.php");
require_once("/var/www/intern.singsaker.no/vendor/autoload.php");

$funk_array = array('fiks_kjonn_nye', 'add_to_sing_alle', 'arkiver_gamle_oppgaver');

foreach ($funk_array as $funk) {
    call_user_func("intern3\\$funk");
}


function arkiver_gamle_oppgaver()
{
    $year = date('Y');
    $running_dates = array("$year-08-01", "$year-01-01");
    $now = date('Y-m-d');

    if (!in_array($now, $running_dates)) {
        return;
    }

    foreach (OppgaveListe::ikkeGodkjente() as $oppgave) {
        /* @var Oppgave $oppgave */
        $oppgave->arkiver();
    }

    print "Arkiverte gamle oppgaver";
}


function fiks_kjonn_nye()
{

    $year = date('Y');
    $running_dates = array("$year-09-01", "$year-01-20");
    $now = date('Y-m-d');

    if (!in_array($now, $running_dates)) {
        return;
    }

    /*
     * ------------------------------------------------------------------------------
     */

    $db = DB::getDB();

// Jentenavn henta fra SSB (https://www.ssb.no/befolkning/statistikker/navn).
    $jentenavn = array(
        'Gjertrud',
        'Filippa',
        'Daniella',
        'Irene',
        'Kathrin',
        'Nelly',
        'Aagot',
        'Halldis',
        'Nadia',
        'Fredrikke',
        'Ellie',
        'Tiril',
        'Eldrid',
        'Mira',
        'Teresa',
        'Ilona',
        'Magny',
        'Klara',
        'Ivana',
        'Andrea',
        'Åse',
        'Elle',
        'Kamilla',
        'Agnes',
        'Kine',
        'Ingunn',
        'Katinka',
        'Wendy',
        'Evelyn',
        'Toril',
        'Mathilda',
        'Wigdis',
        'Tine',
        'Muna',
        'Marthine',
        'Astrid',
        'Solfrid',
        'Eilin',
        'Ninni',
        'Oddbjørg',
        'Ester',
        'Ellen',
        'Anneli',
        'Pernille',
        'Beathe',
        'Lina',
        'Linea',
        'Joanna',
        'Siham',
        'Carolina',
        'Gunnhild',
        'Jenny',
        'Annette',
        'Vivi',
        'Unni',
        'Valentina',
        'Aisha',
        'Åsa',
        'Alma',
        'Asta',
        'Målfrid',
        'Eilen',
        'Alfhild',
        'Susanna',
        'Amal',
        'Sofia',
        'Alexandra',
        'Audhild',
        'Christin',
        'Gunhild',
        'Seline',
        'Margot',
        'Tilde',
        'Lovise',
        'Line',
        'Sina',
        'Dagny',
        'Caroline',
        'Rita',
        'Marie',
        'Solbjørg',
        'Benedikte',
        'Anne-Lise',
        'Lilian',
        'Britt',
        'Thea',
        'Else',
        'Felicia',
        'Helle',
        'Janne',
        'Mathilde',
        'Ingfrid',
        'Laura',
        'Anne-Lene',
        'Alina',
        'Elinor',
        'Patrycja',
        'Molly',
        'Mariana',
        'Frøya',
        'Viola',
        'Renata',
        'Benthe',
        'Jacqueline',
        'Erle',
        'Edle',
        'Ulla',
        'Tea',
        'Betina',
        'Viktoria',
        'Kari',
        'Naima',
        'Gabriella',
        'Arna',
        'Christina',
        'Anne-Britt',
        'Tilla',
        'Tina',
        'Solveig',
        'Venche',
        'Rania',
        'Zara',
        'Bertha',
        'Alice',
        'Tora',
        'Nadja',
        'Sabrina',
        'Ieva',
        'Ane',
        'Alette',
        'Anne-Mari',
        'Otilie',
        'Kim',
        'Zuzanna',
        'Else-Marie',
        'Dorota',
        'Gry',
        'Thilde',
        'Reidunn',
        'Gunnbjørg',
        'Sana',
        'Anna',
        'Tone',
        'Inghild',
        'Inger-Lise',
        'Vivian',
        'Fanny',
        'Zofia',
        'Yvonne',
        'Edit',
        'Trine',
        'Mina',
        'Amy',
        'Elfrid',
        'Tara',
        'Weronika',
        'Guri',
        'Kaisa',
        'Synøve',
        'Paulina',
        'Rose',
        'Kitty',
        'Henny',
        'Brynhild',
        'Vibecke',
        'Malena',
        'Cathrin',
        'Brith',
        'Hanan',
        'Madelen',
        'Jeanette',
        'Hong',
        'Ailin',
        'Eira',
        'Livia',
        'Ada',
        'Gudny',
        'Turi',
        'Evy',
        'Melina',
        'Vanessa',
        'Dorthea',
        'Hodan',
        'Jasmine',
        'Cornelia',
        'Sahar',
        'Zeinab',
        'Silvia',
        'Torunn',
        'Emine',
        'Amelia',
        'Cesilie',
        'Mariam',
        'Saima',
        'Lidia',
        'Ingvil',
        'Sidra',
        'Veslemøy',
        'Ågot',
        'Kirsten',
        'Renate',
        'Lene',
        'Amira',
        'Izabela',
        'Rebecca',
        'Betty',
        'Marzena',
        'Ayla',
        'Kristine',
        'Farah',
        'Eir',
        'Maryam',
        'Iryna',
        'Aya',
        'Emilia',
        'Dagrun',
        'Nina',
        'Sylvi',
        'Eden',
        'Torild',
        'Tanja',
        'Marija',
        'Martha',
        'Sissel',
        'Margarita',
        'Fatima',
        'Aline',
        'Elna',
        'Isabella',
        'Kjersti',
        'Grazyna',
        'Annbjørg',
        'Urszula',
        'Anne-Grethe',
        'Hedda',
        'Berit',
        'Elizabeth',
        'Juni',
        'Herborg',
        'Herdis',
        'Ramona',
        'Linn',
        'Hafsa',
        'Ann',
        'Jill',
        'Sanne',
        'Leni',
        'Runa',
        'Janita',
        'Elbjørg',
        'Magda',
        'Live',
        'Ekaterina',
        'Thi',
        'Rasa',
        'Bianca',
        'Ewelina',
        'Dalia',
        'Silja',
        'Katharina',
        'Rebecka',
        'Doris',
        'Hulda',
        'Brit',
        'Solvår',
        'Mila',
        'Emely',
        'Bjørg',
        'Agathe',
        'Vilja',
        'Liva',
        'Møyfrid',
        'Zainab',
        'Anniken',
        'Gunlaug',
        'Frid',
        'Danuta',
        'Simone',
        'Petra',
        'May-Liss',
        'Leyla',
        'Arnhild',
        'Ariana',
        'Karina',
        'Josefine',
        'Oddrun',
        'Olivia',
        'Borgny',
        'Beata',
        'Hedvig',
        'Torbjørg',
        'Trude',
        'Elisabeth',
        'Eliana',
        'Tirill',
        'Helena',
        'Cindy',
        'Kristiane',
        'Lykke',
        'Vera',
        'Emilie',
        'Lilly',
        'Carmen',
        'Ann-Karin',
        'Iren',
        'Ida',
        'Birgit',
        'Isabelle',
        'Matilde',
        'Sonja',
        'Birgitta',
        'Eirill',
        'Erna',
        'Margunn',
        'Alva',
        'Stella',
        'Jennifer',
        'Marita',
        'Tove',
        'Emina',
        'Solvor',
        'Lydia',
        'Gunvor',
        'Kamila',
        'Kathinka',
        'Ina',
        'Hatice',
        'Elise',
        'Henrikke',
        'Serine',
        'Julia',
        'Christel',
        'Hild',
        'Mary-Ann',
        'Tordis',
        'Bella',
        'Jolanta',
        'Maja',
        'Claudia',
        'Carina',
        'Liz',
        'Kaia',
        'Rigmor',
        'Milena',
        'Maya',
        'Nada',
        'Elma',
        'Mariell',
        'Jannicke',
        'Kornelia',
        'Oda',
        'Ragni',
        'Samira',
        'Tale',
        'Stina',
        'Hanna',
        'Melinda',
        'Lindis',
        'Monika',
        'Irena',
        'Paula',
        'Kirsti',
        'Kjerstin',
        'Juliana',
        'Kinga',
        'Michaela',
        'Alicia',
        'Ava',
        'Edna',
        'Regina',
        'Oline',
        'Gerda',
        'Ella',
        'Mildrid',
        'Natalia',
        'Lisbet',
        'Julianne',
        'Mai',
        'Karianne',
        'Malin',
        'Laila',
        'Joan',
        'Margrete',
        'Eldbjørg',
        'Helen',
        'Ayan',
        'Agnete',
        'Mathea',
        'Lotta',
        'Hjørdis',
        'Wanja',
        'Malene',
        'Jane',
        'Siri',
        'Ranveig',
        'Marian',
        'Lucy',
        'Ingerid',
        'Serina',
        'Helene',
        'Snefrid',
        'Leikny',
        'Kajsa',
        'Gunn',
        'Aylin',
        'Ingvill',
        'Eileen',
        'Inger-Johanne',
        'Othilie',
        'Anette',
        'Emily',
        'Cathrine',
        'Nikoline',
        'Kjellaug',
        'Ragne',
        'Mia',
        'Nancy',
        'Siril',
        'Anlaug',
        'Rønnaug',
        'Rina',
        'Marina',
        'Linnea',
        'Magna',
        'June',
        'Margareth',
        'Khadija',
        'Une',
        'Olena',
        'Ingun',
        'Saba',
        'Kaja',
        'Gunnvor',
        'Mai-Britt',
        'Johanna',
        'Angela',
        'Sonia',
        'Hilde',
        'Irina',
        'Sumaya',
        'Beatrice',
        'Synne',
        'Fadumo',
        'Oksana',
        'Rachel',
        'Gunda',
        'Hannah',
        'Solrun',
        'Norah',
        'Tyra',
        'Iben',
        'Susan',
        'Edyta',
        'Liv',
        'Marielle',
        'Kari-Anne',
        'Ann-Christin',
        'Oliwia',
        'Bertine',
        'Borghild',
        'Michelle',
        'Aud',
        'Ruta',
        'Sophia',
        'Elsie',
        'Hala',
        'Judith',
        'Katherine',
        'Salma',
        'Jorun',
        'Martine',
        'Deborah',
        'Freya',
        'Rebekka',
        'Sharon',
        'Alise',
        'Kristin',
        'Marwa',
        'Belinda',
        'Violeta',
        'Iwona',
        'Iman',
        'Aslaug',
        'Svanhild',
        'Thelma',
        'Aneta',
        'Edita',
        'May',
        'Sadia',
        'Isabel',
        'Hege',
        'Nour',
        'Melanie',
        'Berta',
        'Birthe',
        'Wenche',
        'Malika',
        'Torgunn',
        'Miranda',
        'Natasha',
        'Ma',
        'Peggy',
        'Christine',
        'Samantha',
        'Ingebjørg',
        'Tamara',
        'Signe',
        'Eirin',
        'Dana',
        'Lena',
        'Terese',
        'Dominika',
        'Vigdis',
        'Hana',
        'Eivor',
        'Vår',
        'Cassandra',
        'Ayse',
        'Elida',
        'Katrin',
        'Rakel',
        'Anne-Grete',
        'Camilla',
        'Anne-Sofie',
        'Lea',
        'Sara',
        'Maria',
        'Cecilia',
        'Eline',
        'Amelie',
        'Barbro',
        'Andrine',
        'Turid',
        'Charlotte',
        'Bente',
        'Gabriele',
        'Ingvild',
        'Eliza',
        'Gine',
        'Elvira',
        'Siw',
        'Irmelin',
        'Sophie',
        'Åshild',
        'Marion',
        'Cristina',
        'Norma',
        'Hanne',
        'Agnieszka',
        'Martyna',
        'Ann-Kristin',
        'Katarzyna',
        'Najma',
        'Senait',
        'Åslaug',
        'Nikola',
        'Sine',
        'Catrine',
        'Oddny',
        'Renathe',
        'Rannveig',
        'Alicja',
        'Enya',
        'Iris',
        'Emma',
        'Aasta',
        'Bozena',
        'Annika',
        'Linda',
        'Azra',
        'Torun',
        'Solgunn',
        'Annlaug',
        'Ingjerd',
        'Sahra',
        'Tatiana',
        'Magnhild',
        'Gøril',
        'Kristel',
        'Vanja',
        'Adriana',
        'Grete',
        'Kristina',
        'Liana',
        'Beate',
        'Sandra',
        'Oddveig',
        'Lajla',
        'Tomine',
        'Anastasia',
        'Bushra',
        'Kathe',
        'Rabia',
        'Venke',
        'Merete',
        'Bergljot',
        'Veronica',
        'Benedicte',
        'Elzbieta',
        'Gitte',
        'Karin',
        'Sanna',
        'Eli',
        'Victoria',
        'Elly',
        'Bettina',
        'Lisa',
        'Ebba',
        'Justyna',
        'Margrethe',
        'Grethe',
        'Rosa',
        'Nora',
        'Kjellrun',
        'Maylen',
        'Ana',
        'Suzanne',
        'Sidsel',
        'Hamdi',
        'Adelen',
        'Greta',
        'Asma',
        'Tuva',
        'Janniche',
        'Nova',
        'Jorunn',
        'Ewa',
        'Evelina',
        'Selina',
        'Ariel',
        'Naomi',
        'Leah',
        'Ann-Helen',
        'Haldis',
        'Gun',
        'Anbjørg',
        'Thale',
        'Ea',
        'Mirjam',
        'Isabell',
        'Johanne',
        'Anne',
        'Sabine',
        'Gina',
        'Wencke',
        'Marianne',
        'Margaret',
        'Rut',
        'Daiva',
        'Harriet',
        'Julie',
        'Anne-Mette',
        'Emmy',
        'Sigrunn',
        'Hennie',
        'Olga',
        'Angelina',
        'Vaida',
        'Valborg',
        'Mary',
        'Jurgita',
        'Celia',
        'Liss',
        'Therese',
        'Anne-Karin',
        'Cecilie',
        'Wiktoria',
        'Jamila',
        'Lin',
        'Emelie',
        'Ronja',
        'Agata',
        'Mille',
        'Gintare',
        'Saga',
        'Maud',
        'Erika',
        'Tanya',
        'Bodil',
        'Vida',
        'Aina',
        'Åsne',
        'Wenke',
        'Zahra',
        'Amalie',
        'Ragnhild',
        'Marlen',
        'Lone',
        'Unn',
        'Kathrine',
        'Anne-Marie',
        'Monica',
        'Susann',
        'Mette',
        'Jasmin',
        'Idun',
        'Norunn',
        'Madeleine',
        'Lill',
        'Anne-Kari',
        'Hildegunn',
        'Anita',
        'Janicke',
        'Ines',
        'Ngoc',
        'Rahel',
        'Krystyna',
        'Vilma',
        'Vilde',
        'Ingeborg',
        'Yngvild',
        'Olaug',
        'Savannah',
        'Anine',
        'Daria',
        'Agnethe',
        'Liliana',
        'Liza',
        'Embla',
        'Lilja',
        'Jana',
        'Carine',
        'Mariel',
        'Barbara',
        'Åsta',
        'Eiril',
        'Ruth',
        'Marte',
        'Kristi',
        'Elea',
        'Anja',
        'Kate',
        'Gloria',
        'Elisabet',
        'Aase',
        'Elen',
        'Celina',
        'Emilija',
        'Patricia',
        'Brita',
        'May-Britt',
        'Natalie',
        'Anett',
        'Amanda',
        'Alvhild',
        'Mariann',
        'Nanna',
        'Birgitte',
        'Vilje',
        'Clara',
        'Anne-Kristin',
        'Katarina',
        'Dagmar',
        'Tonje',
        'Vårin',
        'Nathalie',
        'Maj',
        'Jennie',
        'Juliane',
        'Esther',
        'Dina',
        'Aileen',
        'Nadine',
        'Ellinor',
        'Sylvia',
        'Nicoline',
        'Stine',
        'Elsa',
        'Viktorija',
        'Anne-Berit',
        'Siren',
        'Guro',
        'Margun',
        'Renee',
        'Henriette',
        'Eva',
        'Daniela',
        'Edda',
        'Karen',
        'Irma',
        'Madelene',
        'Fride',
        'Oddlaug',
        'Gerd',
        'Anne-Marit',
        'Alba',
        'Larisa',
        'Ylva',
        'Iselin',
        'Angelica',
        'Leila',
        'Amina',
        'Martina',
        'Selma',
        'Desiree',
        'Inger-Marie',
        'Regine',
        'Yasmin',
        'Merethe',
        'Hermine',
        'Frøydis',
        'Torill',
        'Ragna',
        'Agne',
        'Marthe',
        'Frida',
        'Halima',
        'Janet',
        'Veronika',
        'Ingri',
        'Pauline',
        'Phuong',
        'Tia',
        'Thuy',
        'Ulrikke',
        'Ine',
        'Adele',
        'Mie',
        'Hilda',
        'Josephine',
        'Stephanie',
        'Lana',
        'Maren',
        'Elina',
        'Alisa',
        'Gabriela',
        'Asbjørg',
        'Jelena',
        'Idunn',
        'Kjellfrid',
        'Egle',
        'Elin',
        'Jorid',
        'Heidi',
        'Magni',
        'Gyda',
        'Sølvi',
        'Fiona',
        'Alida',
        'Birte',
        'Edel',
        'Lillian',
        'Una',
        'Carla',
        'Lucia',
        'Karolina',
        'Jeanett',
        'Dorthe',
        'Sigfrid',
        'Annie',
        'Synnøve',
        'Jette',
        'Sunniva',
        'Milla',
        'Lotte',
        'Sigrun',
        'Judit',
        'Jessica',
        'Klaudia',
        'Gabrielle',
        'Siv',
        'Gudrun',
        'Lara',
        'Aria',
        'Grace',
        'Sofie',
        'Ingelin',
        'Thanh',
        'Trine-Lise',
        'Torhild',
        'Annelise',
        'Edith',
        'Svetlana',
        'Lisbeth',
        'Isa',
        'Olava',
        'Vibeke',
        'Randi',
        'Galina',
        'Inger',
        'Maiken',
        'Amna',
        'My',
        'Leonora',
        'Karoline',
        'Ann-Mari',
        'Celine',
        'Catharina',
        'Alvilde',
        'Silje',
        'Pia',
        'Safia',
        'Karla',
        'Sarah',
        'Signy',
        'Reidun',
        'Sigrid',
        'Sabina',
        'Jofrid',
        'Rikke',
        'Maia',
        'Gro',
        'Aleksandra',
        'Simona',
        'Elisa',
        'Thora',
        'Cicilie',
        'Iqra',
        'Luna',
        'Miriam',
        'Marlene',
        'Jasmina',
        'Noor',
        'Christiane',
        'Astri',
        'Jannike',
        'Tatjana',
        'Lilli',
        'Gretha',
        'Sol',
        'Mari',
        'Matilda',
        'Connie',
        'Melissa',
        'Sienna',
        'Angelika',
        'Magdalena',
        'Karine',
        'Mona',
        'Anisa',
        'Aashild',
        'Maryan',
        'Ausra',
        'Marry',
        'Asha',
        'Mari-Ann',
        'Thu',
        'Lily',
        'Nicole',
        'Catherine',
        'Anny',
        'Anni',
        'Kerstin',
        'Rahma',
        'Erica',
        'Theresa',
        'Marit',
        'Audny',
        'Mali',
        'Katrine',
        'Helga',
        'Aida',
        'Gudveig',
        'Susanne',
        'Ingrid',
        'Fatemeh',
        'Sylwia',
        'Olea',
        'Wilma',
        'Marta',
        'Malgorzata',
        'Katja',
        'Fatma',
        'Margit',
        'Gunnlaug',
        'Katrina',
        'Louise',
        'Inge',
        'Hildur',
        'Elena',
        'Lise',
        'Diana',
        'Aurora',
        'Leona',
        'Inga'
    );

    $st = $db->prepare('UPDATE beboer SET kjonn=1 WHERE id=:id');
    $groupmanager = new \Group\GroupManage();

    foreach (BeboerListe::aktive() as $beboer) {
        /* @var Beboer $beboer */
        if ($beboer->getRomhistorikk()->getAntallSemestre() > 1) {
            continue;
        }

        if (in_array($beboer->getFornavn(), $jentenavn)) {
            $st->execute(['id' => $beboer->getId()]);
            $groupmanager->removeFromGroup($beboer->getEpost(), SING_GUTTER);
            $groupmanager->addToGroup($beboer->getEpost(), 'MEMBER', SING_JENTER);

        }
    }
    print "Fikset kjønn og relevant epostlisteting.";

}

function add_to_sing_alle()
{

    $groupmanager = new \Group\GroupManage();
    $sing_alle = $groupmanager->listGroup(SING_ALLE);

    $emails = array();
    foreach($sing_alle as $member) {
        $emails[] = strtolower($member["email"]);
    }

    foreach (BeboerListe::aktive() as $beboer) {
        /* @var Beboer $beboer */

        if(!in_array(strtolower(str_replace(' ', '', $beboer->getEpost())), $emails)) {
            try {
                $groupmanager->addToGroup(strtolower(str_replace(' ', '', $beboer->getEpost())), 'MEMBER', SING_ALLE);
            } catch(\Exception $e) {}
        }

    }

}