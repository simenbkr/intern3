<?php
require_once('topp_utvalg.php');
?>

<div class="container">
    <h1>Utvalget » Vaktsjef » Krysserapport historie</h1>

    <div class="tilbakemelding">
        <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $_SESSION['msg']; ?>
            </div>
            <p></p>
            <?php
        } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
            <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $_SESSION['msg']; ?>
            </div>
            <p></p>
            <?php
        }
        unset($_SESSION['success']);
        unset($_SESSION['error']);
        unset($_SESSION['msg']);
        ?></div>
    <div id="feedback"></div>

    Fra:
    <select onchange="setFra(this.value)" id="fra">
        <?php foreach ($datoer as $dato) { ?>

            <option value="<?php echo $dato; ?>"><?php echo $dato; ?></option>
        <?php } ?>
    </select>
    <br/>
    <br/>
    Til:
    <select onchange="setTil(this.value)" id="til">
        <?php foreach ($datoer as $dato) { ?>

            <option value="<?php echo $dato; ?>"><?php echo $dato; ?></option>
        <?php } ?>
    </select>
    <hr>
    <br/>
    <div id="historien"></div>

</div>
<script>

    window.onload = function(){
        $('#til option').eq(0).prop('selected', true).trigger('change');
        $('#til option').eq(1).prop('selected', true).trigger('change');
    };

    var fra = "<?php echo $datoer[0]; ?>";
    var til = "<?php echo end($datoer); ?>";

    function setFra(dato) {
        fra = dato;
        change();
    }

    function setTil(dato) {
        til = dato;
        change();
    }

    function compareTime(time1, time2) {
        return new Date(time1) >= new Date(time2); // true if time1 is later
    }

    function error() {

        var errorTekst = "'Fra' må være et tidspunkt før 'Til'!";
        var a = "<div class=\"alert alert-danger fade in\" id=\"danger\" style=\"display:table; margin: auto; margin-top: 5%\">"
            + errorTekst +
            "<a href=\"#\" class=\"close\" data-dismiss=\"alert\" aria-label=\"close\">&times;</a></div>";

        document.getElementById("feedback").innerHTML = a;
        document.getElementById("feedback").style.display = 'block';
    }

    function change(elem) {

        if (compareTime(fra, til)) {
            error();
        } else {
            document.getElementById("feedback").style.display = 'none';
            $.ajax({
                type: 'GET',
                url: '?a=utvalg/vaktsjef/krysserapport_historie_tabell/' + fra + '/' + til,
                method: 'GET',
                success: function (html) {
                    document.getElementById("historien").innerHTML = html;
                    //$("#historien").replaceWith($('.container', $(html)));
                    //$(".subcontainer").replaceWith($('.subcontainer', $(html)));
                    //$(".tekst-ting").replaceWith($('.tekst-ting', $(html)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });

        }
    }


</script>

<?php
require_once('bunn.php');
?>
