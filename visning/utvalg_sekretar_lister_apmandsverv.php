<?php
require_once ('topp_utvalg.php');
?>
<h1>Sekretar &raquo; Lister &raquo; Åpmandsverv</h1>
<div class="container">

    <table class="table table-bordered table-responsive">
    <tr>
        <th>Navn</th>
        <th>Epost</th>
        <th>Verv</th>
        <th>Regitimer</th>
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
<?php
require_once ('bunn.php');
?>