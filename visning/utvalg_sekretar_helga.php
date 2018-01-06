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
    function dummy(){
        location.reload();
    }
</script>
<div class="container">
<h1>Utvalget » Sekretær » Helga</h1>
<div class="col-lg-6">
    <p>Informasjon om siste Helga (lag ny når helga er over, eventuelt hver høst):</p>
    <div id="formen">
    <h2><?php echo (isset($helga) && $helga != null && $helga->getTema() != null) ? $helga->getTema() . '-' : '';?>Helga
        <?php echo (isset($helga) && $helga != null && $helga->getAar() != null) ? $helga->getAar() : ''; ?></h2>
    <h3><?php if($helga != null && $helga->getStartDato() != null) {?>Fra <?php echo $helga->getStartDato(); ?> til <?php echo $helga->getSluttDato(); ?></h3><br/><?php } ?><h3>
    <?php
        if($helga != null && count($helga->getGeneraler()) > 0){
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

    <td>Beboer: <select name="beboerid" id="beboerid" class="form-control">
            <option value="0">- velg -</option>
            <?php
            foreach ($BeboerListe as $beboer) {

                if($beboer != null && !$beboer->erHelgaGeneral()){
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
        <form action="" method="post">
            <table class="table table-bordered table-responsive">
                <tr>
                <td>År:</td>
                <td><input type="text" name="aar" value="YYYY" class="form-control"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" class="btn btn-sm btn-info" value="Legg til" name="ny_helga"></td>
                </tr>
            </table>
        </form>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered table-responsive">
            <tr>
                <th>År</th>
                <th>Tema</th>
                <th>Generaler</th>
                <th>Start-dato</th>
            </tr>
        <?php
        foreach($alle_helga as $helga){
            ?>
            <tr>
               <td><a href="<?php echo $cd->getBase();?>utvalg/sekretar/helga/<?php echo $helga->getAar();?>"><?php echo $helga->getAar();?></a></td>
                <td><?php echo $helga->getTema(); ?></td>
                <td>
                    <?php
                    $generaler_tekst = "";
                    if(sizeof($helga->getGeneraler()) > 0) {
                        foreach ($helga->getGeneraler() as $generalen) {
                            if ($generalen != null) {
                                $generaler_tekst .= " " . $generalen->getFulltNavn() . ",";
                            }
                        }
                        $generaler_tekst = rtrim($generaler_tekst, ',');
                    }
                    echo $generaler_tekst;
                    ?>
                </td>
                <td><?php echo $helga->getStartDato(); ?></td>
            </tr>
            <?php
        }
        ?>
    </div>
</div>
<?php
require_once ('bunn.php');
?>
