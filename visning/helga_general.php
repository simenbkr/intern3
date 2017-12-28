<?php
require_once('topp.php');

/* @var \intern3\Helga $helga */

?>
    <script>
        $(function () {
            $('#datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                onSelect: function (datetext) {
                    var d = new Date(); // for now
                    var h = d.getHours();
                    h = (h < 10) ? ("0" + h) : h;

                    var m = d.getMinutes();
                    m = (m < 10) ? ("0" + m) : m;

                    var s = d.getSeconds();
                    s = (s < 10) ? ("0" + s) : s;

                    datetext = datetext + " " + h + ":" + m + ":" + s;
                    $('#datepicker').val(datetext);
                },
            });
        });


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
        <?php include('tilbakemelding.php'); ?>
        <?php if (isset($oppdatert)) {
            ?>
            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Du er oppdaterte Helga!
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
                <h3> Generaler for Helga-<?php echo $helga->getAar(); ?>: </h3><br/>
                <?php
                foreach ($helga->getGeneraler() as $general) {
                    echo $general->getFulltNavn() . "<br/>";
                }

                ?>
                <h4>Antall gjester totalt: <b><?php echo $helga->getAntallGjester(); ?></b></h4>
                <h4>Torsdag: <?php echo $helga->getAntallPerDag()['torsdag']; ?></h4>
                <h4>Fredag: <?php echo $helga->getAntallPerDag()['fredag']; ?></h4>
                <h4>Lørdag: <?php echo $helga->getAntallPerDag()['lordag']; ?></h4>

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
                                <td><input type="text" name="dato" id="datepicker" class="datepicker"
                                           value="<?php echo $helga->getStartDato(); ?>"></td>
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

            <div class="col-lg-6">

                <h4>Tilganger</h4>
                <p>Gi sikkerhetssjef/ansvarlige tilgang til inngangsvisningen.</p>
                <table class="table table-bordered table-responsive">
                    <tr>
                        <td>Verv</td>
                        <td>Ansvarlig(e)</td>
                        <td>Tilgang til inngang?</td>
                        <td></td>
                    </tr>

                    <?php
                    foreach ($helgaverv as $verv) {
                        /* @var \intern3\Helgaverv $verv */
                        $ansvarlige = '';
                        foreach ($verv->getAnsvarlige() as $beboer) {
                            /* @var \intern3\Beboer $beb */
                            $ansvarlige .= $beboer->getFulltNavn() . ' <button onclick="fjern(' . $beboer->getId() . ',' . $verv->getId() . ')">&#x2718;</button>, ';
                        }
                        $ansvarlige = rtrim($ansvarlige, ', ');

                        ?>
                        <tr>
                            <td><?php echo $verv->getNavn(); ?></td>
                            <td><?php echo $ansvarlige; ?>

                                <div>
                                    <input type="button" class="btn btn-sm btn-info" value="Legg Til"
                                           onclick="modal.call(this)" data-target="<?php echo $verv->getId(); ?>"
                                           data-name="<?php echo $verv->getNavn(); ?>">
                                    <div id="modal-<?php echo $verv->getId(); ?>">
                                    </div>
                                </div>

                            </td>
                            <td><?php echo $verv->getTilgang() > 0 ? "Ja" : "Nei"; ?>
                                <br/><button class="btn btn-warning" onclick="endreTilgang(<?php echo $verv->getId();?>)">Endre</button>
                            </td>
                            <td>
                                <button class="btn btn-danger" onclick="slettverv(<?php echo $verv->getId(); ?>)">
                                    Slett verv
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

            <div class="col-lg-6">
                <form method="POST">
                    <h4>Legg til verv:</h4>
                    <table class="table table-bordered table-responsive">
                        <input type="hidden" name="form" value="addverv">
                        <tr>
                            <td>Navn:</td>
                            <td><input type="text" name="navn" value=""></td>
                        </tr>
                        <tr>
                            <td>Tilgang (tall fra 0 til hva du vil. > 0 gir tilgang til inngang):</td>
                            <td><input type="text" name="tilgang" value=""></td>
                        </tr>
                        <td></td>
                        <td><input class="btn btn-primary" type="submit" value="Legg til"></td>
                        </tr>
                    </table>
                </form>

            </div>

        </div>
        <?php /*
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


*/ ?>
    </div>

    <script>
        function fjern(beboerId, vervId) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/general',
                data: 'fjern=' + beboerId + '&verv=' + vervId,
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function modal() {
            var vervId = $(this).attr('data-target');
            var vervNavn = $(this).attr('data-name');
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=helga/vervmodal',
                data: 'vervId=' + vervId + '&vervNavn=' + vervNavn,
                success: function (data) {
                    $('#modal-' + vervId).html(data);
                    $('#' + vervId + '-åpmand').modal('show');
                    $('#' + VervId + '-åpmand').on('hidden.bs.modal', function () {
                        $('#modal-' + vervId).html(' ');
                    });
                }
            });
        }

        function slettverv(id) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/general',
                data: 'fjernverv=' + id,
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function endreTilgang(id) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/general',
                data: 'endretilgang=' + id,
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }


    </script>
<?php
require_once('bunn.php');
?>