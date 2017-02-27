<link rel="stylesheet" type="text/css" href="css/gammelt_stilark.css"/>
<h1>Krysseliste for øl</h1>
<table id="krysselistetop">
    <tr>
        <th class="tittel">Singsaker Studenterhjem</th>
        <th class="dato">___ vakt, _______dag ____ / ____ 2017</th>
    </tr>
</table>

<table id="krysseliste">
    <tr>
        <th class="navn">Navn</th>
        <th class="antall">Antall øl</th>
        <th class="sum">Sum</th>
        <th>&nbsp;</th>
        <th class="navn">Navn</th>
        <th class="antall">Antall øl</th>
        <th class="sum">Sum</th>
    </tr>
    <?php for($i = 0; $i < count($beboerliste) ; $i+=2){ ?>
    <tr>
        <td class="navn"><?php echo ($beboerliste[$i] != null) ? $beboerliste[$i]->getFulltNavn() : '';?></td>
        <td class="antall"></td>
        <td class="sum"></td>
        <td class="tom"></td>
        <td class="navn"><?php echo ($beboerliste[$i] != null) ? $beboerliste[$i+1]->getFulltNavn() : '';?></td>
        <td class="antall"></td>
        <td class="sum"></td>
    </tr>
    <?php
}
?>
    </table>
<p>Antall beboere på krysselista: <?php echo count($beboerliste);?></p>
<h2>Viktig!</h2>
<ol>
    <li>Bare de som står på lista kan krysse</li>
    <li>Mangler det øl, kan du krysse dem på deg selv</li>
    <li>Hvis det ikke har vært noen omsetning i løpet av vakta så skriv likevel en notat på forrige krysseliste: mottatt og avlevert +evt påfylling. Husk å skrive under!!</li>
</ol>
<table id="krysselistebunn">
    <tr>
        <td class="top" colspan="7">Totalt antall øl krysset i løpet av vakta:</td>
    </tr>
    <tr>
        <td class="tom" colspan="7">&nbsp;</td>
    </tr>
    <tr>
        <td class="midt">&nbsp;</td>
        <td class="midt">+</td>
        <td class="midt">&nbsp;</td>
        <td class="midt">-</td>
        <td class="midt">&nbsp;</td>
        <td class="midt">=</td>
        <td class="midt">&nbsp;</td>
    </tr>
    <tr>
        <td class="tom">Mottatt</td>
        <td class="tom">&nbsp;</td>
        <td class="tom">Påfyll</td>
        <td class="tom">&nbsp;</td>
        <td class="tom">Avlevert</td>
        <td class="tom">&nbsp;</td>
        <td class="tom">Antall øl ut av skapet</td>
    </tr>
    <tr>
        <td class="tom" colspan="7">&nbsp;</td>
    </tr>
    <tr>
        <td class="tom" colspan="7">Vaktas underskrift</td>
    </tr>
    <tr>
        <td class="tom" colspan="7">&nbsp;</td>
    </tr>
    <tr>
        <td class="bunn" colspan="7">&nbsp;</td>
    </tr>
</table>