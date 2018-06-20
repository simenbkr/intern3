<style>
    table#gen{
        font-family: sans-serif;
        border-right: 1px solid #000000;
        border-bottom: 1px solid #000000;
        font-size: 80%;
        text-align: left;
    }
    table#kalender{
        font-family: sans-serif;
        font-size: 20px;
        text-align: center;
    //        float: right;
        width: 100%;
    }
    table#kalender, .nonmonthdays, .days, .today {
        border: 1px dotted #000;
    //		width: 120px;
        height: 60px;
    }
    table#kalender .weekend {
    //	width: 120px;
        height: 60px;
        border: 1px dotted #000;
        background-color: #CCC;
    }
    table#kalender .fullvakt {
    //	width: 120px;
        height: 60px;
        border: 1px dotted #000;
        background-color: #729fcf;
    }
    table#kalender .today {
        border: 1px dotted #000;
        background-color: #396;
    }
    a {
        color: #396;
        text-decoration: none;
    }
    a:visited {
        color: #669;
        text-decoration: none;
    }
    /* GENERAL ########################################################## */
    body    {
        font-family: sans-serif;
        font-size: 12px;
        text-align: left;
        /*	background-color: #FFF; */
        background-color: #AD9A7F;
        margin: 0;
        padding: 0;
        border: 0;
    }

    #diverse p {
        font-size: 1em;
        color: #EEE;
    }
    #diverse hr {
        border: 1px solid #DDD;
        width: 50%;
        /*	color: #333;
            width: 50%;
            height: 1px;
        */
        noshade;
    }

    h2 	{
        font-size: 1.2em;
    }
    h1 	{
        width: 300px;
        color: #fff;
        font-size: 2em;
    }

    p 	{
        font-size: 1em;
        color: black;
    }



    table#kalender{
        font-family: sans-serif;
        font-size: 14px;
        text-align: center;
        float: center;
    }
    table#kalender, .nonmonthdays, .days, .today {
        border: 1px dotted #000;
        background-color: #EEE;
    }
    table#kalender .weekend {
        border: 1px dotted #000;
        background-color: #CCC;
    }
    table#kalender .today {
        border: 1px dotted #000;
        color: #EEE;
        background-color: #4c1f0c;
    }

</style>
<?php
$denneUka = @date('W');
$detteAret = @date('Y');
$ukeStart = strtotime('last sunday - 6 days, midnight');
$dager = array('MANDAG', 'TIRSDAG', 'ONSDAG', 'TORSDAG', 'FREDAG', 'LØRDAG', 'SØNDAG');
$tider = array('00.45-08:00', '06:45-13:00', '12:45-19:00', '18:45-01:00');
foreach (range($denneUka, $denneUka > 26 ? date('W', mktime(0, 0, 0, 12, 31, date('Y'))) : 26) as $uke){
    $ukeStart = strtotime('+1 week', $ukeStart); ?>
    <table id="kalender">
        <tr>
            <th colspan="7">VAKTLISTE UKE <?php echo $uke;?></th>
        </tr>
        <tr>
            <td>DAG</td>
            <td>DATO</td>
            <td>1. VAKT<br/><?php echo $tider[0];?></td>
            <td>2. VAKT<br/><?php echo $tider[1];?></td>
            <td>3. VAKT<br/><?php echo $tider[2];?></td>
            <td>4. VAKT<br/><?php echo $tider[3];?></td>
        </tr>
        <?php
        foreach(range(0,6) as $dag){ ?>
            <tr>
                <td><?php echo $dager[$dag];?></td>
                <td><?php echo date('Y-m-d',strtotime('+' . $dag . ' day', $ukeStart));?></td>
                <?php
            foreach(range(1,4) as $vakttype){
                $vakta = intern3\Vakt::medDatoVakttype(date('Y-m-d',strtotime('+' . $dag . ' day', $ukeStart)), $vakttype);
                $class = 'days';
                if(date('N', strtotime(date('Y-m-d',strtotime('+' . $dag . ' day', $ukeStart)))) > 5){
                    $class = 'weekend';
                } else {
                    $class = 'days';
                }
                if($vakta != null && $vakta->getBruker() != null && $vakta->getBruker()->getPerson() != null) {
                    ?>
                    <td class="<?php echo $class;?>"><?php echo ($vakta->getBruker()) != null ? $vakta->getBruker()->getPerson()->getFulltNavn() : 'uhh'; ?></td>
                    <?php
                } else { ?>
                    <td></td>
                <?php
                }
            }
            ?>
                </tr>
        <?php
        }
        ?>
    </table>
    <p></p>
    <p></p>
    <?php /*Antall <br/> funnet ved empiri. */ ?>
    <br/><br/><br/><br/><br/> <br/><br/><br/><br/> <br/><br/><br/><br/> <br/><br/>
<?php
}
?>

