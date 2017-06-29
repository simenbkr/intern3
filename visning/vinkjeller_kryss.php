<?php
require_once ('topp_vinkjeller.php');
require_once('topp.php');
?>
<style>
    #sliders {
        width: 300px;
        padding: 20px;
    }

    #sliders li {
        margin-bottom: 10px;
    }

    #sliders div {
        margin-bottom: 5px;
    }
</style>
<div class="container">

    <h1>Vinkjeller » Kryss » <?php echo $vinen->getNavn();?></h1>
    <hr>
    <div class="col-md-6">

        <h3>Velg antall <i><?php echo $vinen->getNavn(); ?></i> du/dere ønsker krysset.</h3>

        <input type="range" min="1" max="<?php echo $vinen->getAntall(); ?>" value="1" step="1" onchange="showValue(this.value, 'range')">
        <h4><span id="range">1</span></h4>
    </div>

    <div class="col-md-6">

        <ul id="sliders">
        <?php foreach($beboerene as $beboer){
            /* @var intern3\Beboer $beboer */
            ?>

            <li>
                <div class="navn"><?php echo $beboer->getFulltNavn(); ?></div>
                <div class="slider"><?php echo round(1/count($beboerene),2) * 100; ?></div>
                <span class="value">0</span>%, (ca)
                <span class="pris"><?php echo round(1 * 1/count($beboerene) * $vinen->getPris(),2); ?></span>kr
            </li>


        <?php
        }
        ?>

        </ul>
    </div>



    <button class="btn btn-block btn-primary">KRYSS</button>


</div>
<?php
$ider = array();
foreach($beboerene as $beboer){
    $ider[] = $beboer->getId();
}
?>
<script>
    var pris = <?php echo $vinen->getPris(); ?>;
    var antall = 1;
    var ids = <?php echo json_encode($ider, true); ?>;

    function showValue(newValue, id)
    {
            document.getElementById(id).innerHTML = newValue;
            antall = newValue;
    }

    var sliders = $("#sliders .slider");
    var availableTotal = 100;

    sliders.each(function() {
        var init_value = parseInt($(this).text());

        $(this).siblings('.value').text(init_value);

        $(this).empty().slider({
            value: init_value,
            min: 0,
            max: availableTotal,
            range: "max",
            step: 2,
            animate: 0,
            slide: function(event, ui) {

                // Update display to current value
                $(this).siblings('.value').text(ui.value);
                $(this).siblings('.pris').text(Math.round(ui.value/100 * antall * pris * 100)/100);

                // Get current total
                var total = 0;

                sliders.not(this).each(function() {
                    total += $(this).slider("option", "value");
                });

                // Need to do this because apparently jQ UI
                // does not update value until this event completes
                total += ui.value;

                var delta = availableTotal - total;

                // Update each slider
                sliders.not(this).each(function() {
                    var t = $(this),
                        value = t.slider("option", "value");

                    var new_value = value + (delta/2);

                    if (new_value < 0 || ui.value == 100)
                        new_value = 0;
                    if (new_value > 100)
                        new_value = 100;

                    t.siblings('.value').text(new_value);
                    t.slider('value', new_value);

                    t.siblings('.pris').text(Math.round(antall * new_value/100 * pris * 100)/100);

                });
            }
        });
    });
</script>
<?php
require_once('bunn.php');
?>
