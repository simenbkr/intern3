<?php
require_once('topp.php');
?>
    <script>
        $("#ajaxform").submit(function (e) {
            var postData = $(this).serializeArray();
            var formURL = $(this).attr("action");
            $.ajax(
                {
                    url: formURL,
                    type: "POST",
                    data: postData,
                    success: function (html) {
                        $(".container").replaceWith($('.container', $(html)));
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            e.preventDefault();	//STOP default action
        });
        $("#ajaxform").submit(); //SUBMIT FORM
    </script>
    <div class="container">
        <?php if (isset($oppdatert)) {
            ?>
            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Du er oppdaterte Helga! Bra jobba!
            </div>
            <?php
        }
        ?>
        <?php
        echo "<h1>Helga " . $helga->getAar() . "</h1>";
        ?>
        <div class="row">
            <div class="col-lg-6">
                <hr>
                <br/>
                <h3> De modige generalene i år er: </h3><br/>
                <p>
                    <?php
                    foreach ($helga->getGeneraler() as $general) {
                        echo $general->getFulltNavn() . "<br/>";
                    }

                    ?></p>

            </div>
            <div class="col-lg-6">
                <hr>
                <h3><?php echo $helga->getTema(); ?>-Helga <?php echo $helga->getAar(); ?> varer
                    fra <?php echo $helga->getStartDato(); ?> til <?php echo $helga->getSluttDato(); ?></h3>
                <p>Endre Helga:</p>

                <div id="formen" class="formen">
                    <form name="ajaxform" id="ajaxform" action="" method="POST">
                        <table class="table table-bordered table-responsive">
                            <input type="hidden" name="aar" value="<?php echo $helga->getAar(); ?>">
                            <tr>
                                <td>Start-dato</td>
                                <td><input type="text" name="start" value="<?php echo $helga->getStartDato(); ?>"/></td>
                            </tr>
                            <tr>
                                <td>Tema:</td>
                                <td><input type="text" name="tema" value="<?php echo $helga->getTema(); ?>"/></td>
                            </tr>
                            <td>Klar (dette gjør <br/>Helga-siden synlig for alle):</td>
                            <td><input type="checkbox"
                                       name="klar" <?php if (isset($helga) && $helga != null && $helga->getKlar()) {
                                    echo 'checked="checked"';
                                } ?>></td>
                            <tr>
                                <td>Antall gjester per beboer:</td>
                                <td><input type="text" name="max_gjest" value="<?php echo $helga->getMaxGjester(); ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td><input class="btn btn-primary" type="submit" value="Endre"></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <h3>Eposten vil bli seende (omtrent) slik ut:</h3>

            <?php echo $helga->getEpostTekst(); ?><br/>
            Denne invitasjonen gjelder for [dag] [dato]<br/><br/>
            Med vennlig hilsen<br/>
            <?php echo $helga->getTema() . '-Helga ' . $helga->getAar(); ?>
        </div>
        <div class="col-lg-6">
            <form action="" method="post">
                <table class="table table-bordered">
                    <tr>
                        <th>Epost-tekst</th>
                        <td><textarea name="epost_tekst" cols="50" rows="5"><?php echo $helga->getEpostTekst(); ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" class="btn btn-primary" name="endre" value="Endre"></td>
                    </tr>
                </table>
            </form>
        </div>



    </div>
<?php
require_once('bunn.php');
?>