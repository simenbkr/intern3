<?php
require_once ('topp.php');
?>
<div class="container">
  <h1>Kjellermester » Lister</h1>
    <p>[ <a href="<?php echo $cd->getBase(); ?>kjeller/admin">Vinadministrasjon</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/slettet_vin">Slettet vin</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/leggtil">Legg til vin</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/add_type">Vintyper</a> ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/pafyll">Påfyll</a> ] [ Lister ]
        [ <a href="<?php echo $cd->getBase(); ?>kjeller/regning">Regning</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/svinn">Svinn</a> ] [ <a href="<?php echo $cd->getBase(); ?>kjeller/lister/beboere_vin">Fakturer</a> ]</p>
    <hr>
    <div class="col-lg-6">
        <table class="table table-responsive">
            <tr>
                <td><a href="?a=kjeller/lister/rapport">Rapport (til kontoret)</a></td>
            </tr>
            <tr>
                <td><a href="?a=kjeller/lister/beboere_vin">Oversikt over beboere og vin (ikke-fakturerte)</a></td>
            </tr>
            <tr>
                <td><a href="?a=kjeller/lister/beboere_vin_utskrift">Oversikt over beboere og vin (utskrift) (ikke-fakturerte)</a></td>
            </tr>

            <tr>
                <td><a href="?a=kjeller/lister/beboere_vin_fakturerte">Oversikt over beboere og vin (fakturerte)</a></td>
            </tr>
            <tr>
                <td><a href="?a=kjeller/lister/beboere_vin_utskrift_fakturerte">Oversikt over beboere og vin (utskrift) (fakturerte)</a></td>
            </tr>

            <tr>
                <td><a href="?a=kjeller/admin">Varebeholdning</a></td>
            </tr>
            <tr>
                <td><a href="?a=kjeller/lister/varebeholdning_utskrift">Varebeholdning (utskrift)</a></td>
            </tr>
        </table>
    </div>
</div>
<?php
require_once ('bunn.php');
?>