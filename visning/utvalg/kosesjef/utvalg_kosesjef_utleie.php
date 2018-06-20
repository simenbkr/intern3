<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>
<script>
    $(function () {
        $("#datepicker").datepicker({dateFormat: "yy-mm-dd"});
    });

    function fjernUtleie(id) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/kosesjef/utleie',
            data: 'fjern=utleie&utleieid=' + id,
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function fjernBeboer(beboerid, utleieid, felt) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/kosesjef/utleie',
            data: 'fjern=beboer&beboerid=' + beboerid + "&utleieid=" + utleieid + "&felt=" + felt,
            method: 'POST',
            success: function (html) {
                $(".container").replaceWith($('.container', $(html)));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

</script>
<div class="container">
    <h1>Utvalget &raquo; Kosesjef &raquo; Utleie</h1>
    <?php if (isset($success)) {
        ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Du la til en utleie!
        </div>
        <?php
        unset($success);
    }
    if (isset($slettetBeboer)) {
        ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Du slettet en beboer fra et utleie!
        </div>
        <?php
    }
    unset($slettetBeboer);

    if (isset($slettetUtleie)) {
        ?>
        <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            Du slettet et utleie!
        </div>
        <?php
    }


    ?>
    <p></p>
    <h2>Legg til utleie:</h2>
    <div class="col-md-7">
        <form action="" method="post">
            <table class="table-bordered table">
                <tr>
                    <th>Dato</th>
                    <th>Rom</th>
                    <th>Leietaker</th>
                </tr>
                <tr>
                    <th>
                        <div class="form-group">
                            <input class="form-control" id="datepicker" name="dato" size="3">
                        </div>
                    </th>
                    <th>
                        <select name="rom" class="form-control">
                            <option value="Bodegaen">Bodegaen</option>
                            <option value="Salongen">Salongen</option>
                        </select>
                    </th>
                    <th><input type="text" class="form-control" name="leietaker"></th>
                </tr>
                <tr>
                    <th>Sende ut e-post?</th>
                    <td><input type="checkbox" name="epost" value="1"/></td>
                </tr>
            </table>
            <input type="submit" class="btn btn-sm btn-info" name="leggtil" value="Legg til">
        </form>
    </div>


    <div class="col-lg-6">
        <br/>
        <hr>
        <h2>Utleier</h2>

        <table class="table table-bordered table-responsive">
            <tr>
                <th>Dato</th>
                <th>Rom</th>
                <th>Leietaker</th>
                <th>Barvakt 1</th>
                <th>Barvakt 2</th>
                <th>Vask</th>
                <th></th>
            </tr>

            <?php

            foreach ($utleier as $utleie) {
                ?>
                <tr>
                    <td><?php echo $utleie->getDato(); ?></td>
                    <td><?php echo $utleie->getRom(); ?></td>
                    <td><?php echo $utleie->getNavn(); ?></td>
                    <td><?php echo $utleie->getBeboer1() != null ? $utleie->getBeboer1()->getFulltNavn() . "<br/><input class=\"btn btn-primary\" type=\"submit\" value=\"Slett\" onclick=\"fjernBeboer(" . $utleie->getBeboer1()->getId() . ',' . $utleie->getId() . ",1)\">" : ''; ?></td>
                    <td><?php echo $utleie->getBeboer2() != null ? $utleie->getBeboer2()->getFulltNavn() . "<br/><input class=\"btn btn-primary\" type=\"submit\" value=\"Slett\" onclick=\"fjernBeboer(" . $utleie->getBeboer2()->getId() . ',' . $utleie->getId() . ",2)\">" : ''; ?></td>
                    <td><?php echo $utleie->getBeboer3() != null ? $utleie->getBeboer3()->getFulltNavn() . "<br/><input class=\"btn btn-primary\" type=\"submit\" value=\"Slett\" onclick=\"fjernBeboer(" . $utleie->getBeboer3()->getId() . ',' . $utleie->getId() . ",3)\">" : ''; ?></td>
                    <td><input class="btn btn-primary" type="submit" value="Slett Utleie"
                               onclick="fjernUtleie(<?php echo $utleie->getId(); ?>)"></td>
                </tr>
                <?php } ?>


        </table>

    </div>
</div>

<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>
