<link rel="stylesheet" type="text/css" href="css/print.css"/>
<div class="col-md-12">
    <table id="beboerlistetop">
        <tr>
            <th class="left">Beboerliste</th>
            <th class="center">Singsaker Studenterhjem</th>
            <th class="right">Utskriftsdato: <?php echo date('Y-m-d'); ?></th>
        </tr>
    </table>
    <table class="table table-bordered table-responsive" id="beboerliste">
        <tr>
            <th class="heading">Navn</th>
            <th class="heading">Epost</th>
            <th class="heading">Verv</th>
            <th class="heading">Regitimer</th>
        </tr>
        <?php
        foreach($apmandsverv as $verv){ ?>
            <tr><?php
                if($verv == null || !isset($verv)){ continue; }
                $beboere_med_verv = $verv->getApmend();
                if($beboere_med_verv == null || !isset($beboere_med_verv) || sizeof($beboere_med_verv) < 1){
                    ?>
                    <td></td>
                    <td><?php echo $verv->getEpost(); ?></td>
                    <td><?php echo $verv->getNavn();?></td>
                    <td><?php echo $verv->getRegitimer(); ?></td>
                    <?php
                }else {
                    ?>
                    <td><?php
                        $str = "";
                        foreach($beboere_med_verv as $beboer){
                            $str .= " " . $beboer->getFulltNavn() . ",";
                        }
                        $str = rtrim($str, ',');
                        echo $str;
                        ?></td>
                    <td><?php echo $verv->getEpost(); ?></td>
                    <td><?php echo $verv->getNavn(); ?></td>
                    <td><?php echo $verv->getRegitimer(); ?></td>
                    <?php
                }
                ?>
            </tr>
            <?php
        }
        ?>
</div>