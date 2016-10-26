<?php
require_once ('topp_utvalg.php');
?>
<script>
    function fjern(beboerId) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/sekretar/helga',
            data: 'fjern=' + beboerId,
            method: 'POST',
            success: function (html) {
                $("#formen").replaceWith($('#formen', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="container">
<h1>Utvalget » Sekretær » Helga</h1>
<div class="col-lg-6">
    <p>Informasjon om siste Helga (lag ny hvis dette ikke stemmer!):</p>
    <div id="formen">
    <h2><?php echo $helga->getTema() != null ? $helga->getTema() . '-' : '';?>Helga <?php echo $helga->getAar(); ?></h2>
    <h3><?php if($helga->getStartDato() != null) {?>Fra <?php echo $helga->getStartDato(); ?> til <?php echo $helga->getSluttDato(); ?></h3><br/><?php } ?><h3>
    <?php
        if(count($helga->getGeneraler()) > 0){
            echo "<h2>Generaler:<br/></h2>";
            foreach($helga->getGeneraler() as $general){
                echo $general->getFulltNavn();
                echo "      <button onclick=\"fjern(" . $general->getId() . ")\">&#x2718;</button>";
                echo "<br/>";
            }
        }
        ?>
        </h3>
    </div>
</div>
    <div class="col-lg-6">
        <h2>Legg til ny Helga-general!</h2>
    <form action="" method="post" id="helga">
        <table class="table-bordered table">
        <tr></tr>

    <td>Beboer: <select name="beboerid" id="beboerid">
            <option value="0">- velg -</option>
            <?php
            foreach ($BeboerListe as $beboer) {

                if(!$beboer->erHelgaGeneral()){
                    ?>
            <option name="<?php echo $beboer->getId(); ?>" value="<?php echo $beboer->getId(); ?>">
                <?php echo $beboer->getFulltNavn(); ?>
            </option>
            <?php
                }

            ?>


        <?php } ?></td>

            <td><input type="submit" class="btn btn-sm btn-info" value="Legg til" name="leggtil"></td>
    </select>
        </table>
        </form>
    </div>

    <div class="col-lg-6">

        <h2>Lag en ny Helga!</h2>

        <form action="" method="post" id="nyhelga">

            <table class="table table-bordered table-responsive">
                <tr>
                <td>År:</td>
                <td><input type="text" name="aar" value="YYYY"</td>
                </tr>

                <tr>
                    <td></td>
                    <td><input type="submit" class="btn btn-sm btn-info" value="Legg til" name="ny_helga"></td>
                </tr>
            </table>
        </form>



    </div>

</div>
<?php
require_once ('bunn.php');
?>
