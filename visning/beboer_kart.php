<?php
$kart = imagecreatetruecolor( 2896, 1535 );
$dest = imagecreatefromjpeg("beboerkart/kart.jpg");
imagecopy($kart, $dest, 0, 0, 0, 0, 2896, 2896);

$background = imagecolorallocate( $kart, 255, 255, 255 );
$text_colour = imagecolorallocate( $kart, 0, 0, 0 );
$line_colour = imagecolorallocate( $kart, 0, 0, 0 );

$image_width = 122;
$image_height = 92;


$pos = array(
#    ROM           X     Y
    "100" => array(1324, 1395),
    "101" => array(1150, 1395),     "201" => array(1150, 1255),
    "103" => array(1000, 1395),     "203" => array(1000, 1255),
    "105" => array(790, 1395),      "205" => array(790, 1255),
    "107" => array(530, 1395),      "207" => array(530, 1255),
    "109" => array(315, 1395),      "209" => array(315, 1255),
    "111" => array(100, 1395),      "211" => array(100, 1255),

    "212" => array(40, 1030),      "112" => array(190, 1030),
    "213" => array(40, 890),      "113" => array(190, 890),
    "215" => array(40, 750),      "115" => array(190, 750),
    "217" => array(40, 610),      "117" => array(190, 610),
    "219" => array(40, 473),      "119" => array(190, 473),
    "221" => array(40, 333),      "121" => array(190, 333),
    "223" => array(40, 193),      "123" => array(190, 193),
    "225" => array(40, 53),      "125" => array(190, 53),

    "226" => array(440, 53),      "126" => array(590, 53),
    "224" => array(440, 193),      "124" => array(590, 193),
    "222" => array(440, 333),      "122" => array(590, 333),
    "220" => array(440, 473),      "120" => array(590, 473),
    "218" => array(440, 610),      "118" => array(590, 610),
    "216" => array(440, 750),      "116" => array(590, 750),
    "214" => array(440, 890),      "114" => array(590, 890),

    "210" => array(745, 890), "110" => array(745, 1030),
    "208" => array(895, 890), "108" => array(895, 1030),
    "206" => array(1045, 890), "106" => array(1045, 1030),
    "204" => array(1195, 890), "104" => array(1195, 1030),
    "202" => array(1345, 890), "102" => array(1345, 1030),

    "252" => array(1590, 890), "152" => array(1590, 1030),
    "254" => array(1740, 890), "154" => array(1740, 1030),
    "256" => array(1890, 890), "156" => array(1890, 1030),
    "258" => array(2040, 890), "158" => array(2040, 1030),

    "260" => array(2583, 1006), "160" => array(2733, 1006),
    "263" => array(2583, 826), "163" => array(2733, 826),
    "265" => array(2583, 626), "165" => array(2733, 626),
    "267" => array(2583, 470), "167" => array(2733, 470),
    "269" => array(2583, 335), "169" => array(2733, 335),
    "271" => array(2583, 195), "171" => array(2733, 195),
    "273" => array(2583, 53), "173" => array(2733, 53),

    "272" => array(2190, 53), "172" => array(2340, 53),
    "270" => array(2190, 195), "170" => array(2340, 195),
    "268" => array(2190, 335), "168" => array(2340, 335),
    "266" => array(2190, 470), "166" => array(2340, 470),
    "264" => array(2190, 626), "164" => array(2340, 626),
    "262" => array(2190, 826), "162" => array(2340, 826),

    "151" => array(1584, 1395),     "251" => array(1584, 1255),
    "153" => array(1860, 1395),     "253" => array(1860, 1255),
    "155" => array(2070, 1395),     "255" => array(2070, 1255),
    "157" => array(2290, 1395),     "257" => array(2290, 1255),
    "159" => array(2475, 1395),     "259" => array(2475, 1255),
    "161" => array(2685, 1395),     "261" => array(2685, 1255),

    "240" => array(1325, 510),
    "242" => array(1325, 210),
    "244" => array(1325, 53),
    "245" => array(1615, 53),
    "243" => array(1615, 293),
    "241" => array(1615, 478),
    "239" => array(1615, 632),

    "60" => array(-200, 0),
    "060" => array(-200, 0)
);

$rom = array();

$fontfile = "fonts/DejaVuSansCondensed.ttf";
foreach($beboerlista as $beboer){
    $navn = $beboer->getFornavn() . ' ' . $beboer->getEtternavn();
    $navn = strlen($navn) > 21 ? substr($navn,0,21) . '...' : $navn;

    $rom[$beboer->getRom()->getNavn()][] = array("navn" => $navn, "bilde" => $beboer->getBilde());
}

foreach($rom as $nr => $d)
{
    foreach($d as $data)
    {
        $image_url = "profilbilder/$data[bilde]";

        if(!file_exists($image_url) || strlen($image_url) == strlen('profilbilder/'))
            $image_url = 'beboerkart/ayy.jpg';

        $src = imagecreatefromjpeg($image_url);
        $dest = imagecreatetruecolor($image_width, $image_height);

        list($width, $height) = getimagesize($image_url);

        $h = $image_width * ($height / $width);

        imagecopyresampled($dest, $src, 0, 0, 0, 0, $image_width, $h, $width, $height);

        if($h > $image_height)
            $y = -($image_height - $h)/2;
        else
            $y = 0;

        imagecopy($kart, $dest, $pos[$nr][0], $pos[$nr][1], 0, 0, $image_width, $image_height);
        imagettftext($kart, 9, 0, $pos[$nr][0], $pos[$nr][1]+$image_height+13,  $text_colour, $fontfile, $data['navn'] );

        if($nr == 242)
        {
            $pos[$nr][1] += 96;
        }
        else
        {
            $pos[$nr][0] += 128;
        }
    }
}
imagesetthickness ( $kart, 5 );
header( "Content-type: image/jpeg");
imagejpeg( $kart );

imagecolordeallocate( $line_colour );
imagecolordeallocate( $text_colour );
imagecolordeallocate( $background );
imagedestroy( $kart );
?>