<?php
require_once('topp_vinkjeller.php');
require_once('topp.php');

var_dump($_SESSION);
var_dump($_COOKIE);

//var_dump($_SESSION['test']['fordeling'][617]);

//var_dump(explode(',',$_SESSION['test']['fordeling']));

/*foreach (explode(',', $_SESSION['test']['fordeling']) as $key => $elem) {
    if ($key && $elem) {
        echo "$key => $elem<br/>";
    }
}*/


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

    <h1>Vinkjeller » Kryss » <?php echo $vinen->getNavn(); ?></h1>
    <h3>Enhetspris: <?php echo $vinen->getPris() * $vinen->getAvanse(); ?>kr</h3>
    <hr>
    <div class="col-md-6">

        <h3>Velg antall <i><?php echo $vinen->getNavn(); ?></i> du/dere ønsker krysset.</h3>

        <input type="range" min="1" max="<?php echo floor($vinen->getAntall()); ?>" value="1" step="1"
               onchange="showValue(this.value, 'range')">
        <h4><span id="range">1</span></h4>
    </div>

    <div class="col-md-6">


            <ul id="sliders">
                <?php foreach ($beboerene as $beboer) {
                    /* @var intern3\Beboer $beboer */
                    ?>

                    <li>
                        <div class="navn"><?php echo $beboer->getFulltNavn(); ?></div>
                        <div class="slider"
                             id="<?php echo $beboer->getId(); ?>"><?php echo round(1 / count($beboerene), 2) * 100; ?></div>
                        <span class="value">0</span>%, (ca)
                        <span class="pris"><?php echo round(1 * 1 / count($beboerene) * $vinen->getPris() * $vinen->getAvanse(), 2); ?></span>kr
                    </li>


                    <?php
                } ?>
            </ul>


    </div>


    <button class="btn btn-block btn-primary" onclick="kryss()">KRYSS</button>


</div>
<?php
$ider = array();
foreach ($beboerene as $beboer) {
    $ider[] = $beboer->getId();
}
?>
<script>
    var vinID = <?php echo $vinen->getId(); ?>;


    function kryss() {
        $.ajax({
            type: 'POST',
            url: '?a=vinkjeller/kryss_vin/',
            data: 'beboerId=' + ids + '&vinid=' + vinID + "&fordeling=" + prosent + "&antall=" + antall,
            method: 'POST',
            success: function (data) {
                window.location.href = '?a=vinkjeller';
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });

    }

</script>


<script>
    var pris = <?php echo $vinen->getPris() * $vinen->getAvanse(); ?>;
    var antall = 1;
    var ids = <?php echo json_encode($ider, true); ?>;
    var prosent = [];
    for (var i = 0; i < ids.length; i++) {
        prosent[ids[i]] = (1 / ids.length) * 100;
    }


    var sliders = $("#sliders .slider");
    var availableTotal = 100;
    sliders.each(function () {
        var init_value = parseInt($(this).text());

        $(this).siblings('.value').text(init_value);

        $(this).empty().slider({
            value: init_value,
            min: 0,
            max: availableTotal,
            range: "max",
            step: 2,
            animate: 0,
            slide: function (event, ui) {

                // Update display to current value
                if (ids.length > 1) {
                    $(this).siblings('.value').text(ui.value);
                    $(this).siblings('.pris').text(Math.round(ui.value / 100 * antall * pris * 100) / 100);


                    //console.log($(this).context.id);
                    var id = $(this).context.id;
                    prosent[id] = ui.value;

                    // Get current total
                    var total = 0;

                    sliders.not(this).each(function () {
                        total += $(this).slider("option", "value");
                    });

                    // Need to do this because apparently jQ UI
                    // does not update value until this event completes
                    total += ui.value;

                    var delta = availableTotal - total;

                    // Update each slider
                    sliders.not(this).each(function () {

                        //console.log($(this).context.id);

                        var t = $(this),
                            value = t.slider("option", "value");

                        var new_value = value + Math.round((delta / (ids.length - 1)* 10))/10;

                        if (new_value < 0 || ui.value == 100)
                            new_value = 0;
                        if (new_value > 100)
                            new_value = 100;

                        t.siblings('.value').text(new_value);
                        t.slider('value', new_value);

                        id = $(this).context.id;
                        prosent[id] = new_value;

                        t.siblings('.pris').text(Math.round(antall * new_value / 100 * pris * 100) / 100);
                    });
                }
            }
        });
    });


    function showValue(newValue, id) {
        document.getElementById(id).innerHTML = newValue;
        antall = newValue;

        if (ids.length > 1) {
            sliders.each(function () {

                var t = $(this),
                    value = t.slider("option", "value");

                new_value = t.slider("option", "value");
                new_pris = Math.round(antall * new_value / 100 * pris * 100) / 100;
                t.siblings('.value').text(new_value);
                t.siblings('.pris').text(new_pris);
            })
        } else {

            sliders.each(function () {

                var t = $(this),
                    value = t.slider("option", "value");

                new_value = t.slider("option", "value");
                new_pris = Math.round(antall * pris * 100) / 100;
                t.siblings('.value').text(new_value);
                t.siblings('.pris').text(new_pris);
            })
        }

    }

</script>
<?php
require_once('bunn.php');
?>
