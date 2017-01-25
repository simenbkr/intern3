<?php
require_once ('topp.php');
?>
<div class="container">
  <h1>Kjellermester » Lister</h1>
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