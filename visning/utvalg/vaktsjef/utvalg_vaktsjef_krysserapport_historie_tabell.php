<table class="table table-bordered table-responsive">

    <tr><th class="">Navn</th>
        <?php
        foreach($drikke as $drikken) {
            /*
            if($drikken->getId() == 1 || $drikken->getNavn() == 'Pant'){
                continue;
            } */
            if (//($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' ||
            (!$drikken->harBlittDrukketSiden($sistFakturert) && $drikken->getAktiv() == 0)){
                continue;
            }
            ?>
            <th class=""><?php echo $drikken->getNavn();?></th>
        <?php }
        ?>
    </tr>
    <?php foreach($krysseListeMonthListe as $beboerID => $krysseliste){
    $beboeren = $beboerListe[$beboerID];

    if($beboeren == null){
        continue;
    }

    ?>

    <tr>
        <td class="navn"><a href="?a=utvalg/vaktsjef/detaljkryss/<?php echo $beboeren->getId();?>"><?php echo $beboeren->getFulltNavn();?></td>
        <?php foreach($drikke as $drikken){
            /*
            if($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' || $drikken->harBlittDrukketSiden($sistFakturert)){
                continue;
            } */
            if (//($drikken->getId() == 1 || $drikken->getNavn() == 'Pant' ||
            (!$drikken->harBlittDrukketSiden($sistFakturert) && $drikken->getAktiv() == 0)){
                continue;
            }
            ?>
            <td class="<?php echo $drikken->getNavn();?>"><?php echo $krysseliste[$drikken->getNavn()];?></td>
        <?php } ?>
    </tr>

<?php
}
?>