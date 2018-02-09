<?php
require_once ('topp.php');
?>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister">Lister</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/oversikt">Oversikt</a> ]
    </p>
<?php
require_once ('bunn.php');
?>