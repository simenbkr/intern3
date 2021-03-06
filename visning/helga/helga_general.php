<?php
require_once(__DIR__ . '/../static/topp.php');

/* @var \intern3\Helga $helga */

?>
    <script>

        var toggle = <?php echo $helga->erSameMax() ? 1 : 0;?>;

        $(document).ready(function(){
            var single = document.getElementById("single");
            var multiple = document.getElementById("multiple");

            if(toggle === 1) {
                multiple.style = "display:none";
            } else {
                single.style = "display:none";
            }

        });

        function same() {

            if(toggle === 0) {
                toggle = 1;

                single.style = "display:block;";
                multiple.style = "display:none;";

            } else {
                toggle = 0;
                single.style = "display:none;";
                multiple.style = "display:block;";
            }
        }

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
        <?php include(__DIR__ . '/../static/tilbakemelding.php'); ?>
        <?php if (isset($oppdatert)) {
            ?>
            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                Du er oppdaterte HELGA!
            </div>
            <?php
        }
        ?>
        <?php
        echo "<h1>HELGA » HELGA-" . $helga->getAar() . "</h1>";
        ?>
        <div class="row">
            <div class="col-lg-6">
                <hr>
                <br/>
                <h3> Generaler for HELGA-<?php echo $helga->getAar(); ?>: </h3><br/>
                <?php
                foreach ($helga->getGeneraler() as $general) {
                    echo $general->getFulltNavn() . "<br/>";
                }

                ?>
                <h4>Antall gjester totalt: <b><?php echo $helga->getAntallGjester(); ?></b></h4>
                <h4>Torsdag: <?php echo $helga->getAntallPerDag()['torsdag']; ?></h4>
                <h4>Fredag: <?php echo $helga->getAntallPerDag()['fredag']; ?></h4>
                <h4>Lørdag: <?php echo $helga->getAntallPerDag()['lordag']; ?></h4>

                <hr>
                <h4>Hvordan funker dette?</h4>
                <p>
                    HELGA-generalene fyller inn skjemaet etter deres preferanser. Deretter trykker de på klar-knappen.
                    Etter dette kan beboerene invitere sine gjester. Gjestene får en e-post med en QR-kode, og blir også
                    oppført på inngangslista. Ved inngangen scannes denne QR-koden av sikkerhet, hvorpå de trykker på en
                    link til internsiden. Dette registrerer gjesten. Det er også mulig å registrere gjester manuelt i
                    inngangs-visningen.
                </p>

            </div>
            <div class="col-lg-6">
                <hr>
                <h3><?php echo $helga->getTema(); ?>-HELGA <?php echo $helga->getAar(); ?> varer
                    fra <?php echo $helga->getStartDato(); ?> til <?php echo $helga->getSluttDato(); ?></h3>
                <p>Endre HELGA:</p>

                <div id="formen" class="formen">
                    <form name="ajaxform" id="ajaxform" action="" method="POST">
                        <table class="table table-bordered table-responsive">
                            <input type="hidden" name="aar" value="<?php echo $helga->getAar(); ?>">
                            <tr>
                                <td>Start-dato</td>
                                <td><input type="text" name="StartDato" id="datepicker" class="datepicker form-control"
                                           value="<?php echo $helga->getStartDato(); ?>"></td>
                            </tr>
                            <tr>
                                <td>Tema:</td>
                                <td><input class="form-control" type="text" name="Tema" value="<?php echo $helga->getTema(); ?>"/></td>
                            </tr>
                            <td>Klar (dette gjør <br/>HELGA-siden synlig for alle):</td>
                            <td><input type="checkbox"
                                       name="klar" <?php if (isset($helga) && $helga != null && $helga->getKlar()) {
                                    echo 'checked="checked"';
                                } ?>></td>

                           <?php /* <tr>
                                <td>Antall gjester per beboer:</td>
                                <td><input type="text" name="max_gjest" value="<?php echo $helga->getMaxGjester(); ?>"/>
                                </td>
                            </tr> */ ?>

                            <tr>
                                <td>Samme antall gjester hver dag?</td>
                                <td><input type="checkbox" name="SameMax" id="samme" onclick="same()"
                                           <?php if($helga->erSameMax()) {echo 'checked';} ?>></td>
                            </tr>

                            <tr id="single">
                                <td>Maks antall gjester:</td>
                                <td><input class="form-control" type="number" name="MaxAlle" value="<?php echo $helga->getMaxAlle(); ?>"></td>
                            </tr>

                            <tr id="multiple">
                                <td>Maks antall gjester:</td>
                                <td>Torsdag:<input class="form-control" type="number" name="MaxTorsdag" value="<?php echo $helga->getMaxGjester()['torsdag']; ?>">
                                    Fredag:<input class="form-control" type="number" name="MaxFredag" value="<?php echo $helga->getMaxGjester()['fredag']; ?>">
                                    Lørdag:<input class="form-control" type="number" name="MaxLordag" value="<?php echo $helga->getMaxGjester()['lordag']; ?>"></td>
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

                <h3>Legg til egetdefinert gjesteantall</h3>

                <select id="beboerselect" onchange="vis(this)">

                    <option>- Velg -</option>
                    <?php foreach($beboerListe as $beboer) { ?>
                        <option value="<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></option>
                    <?php } ?>
                </select>

                <hr/>
            </div>

            <div class="col-lg-6">

                <h3>Egetdefinert gjesteantall</h3>
                <table class="table table-bordered table-condensed">

                    <thead>
                    <tr>
                        <td>Navn</td>
                        <td>Torsdag</td>
                        <td>Fredag</td>
                        <td>Lørdag</td>
                        <td></td>
                    </tr>

                    </thead>

                    <tbody>
                        <?php foreach($helga->medEgendefinertAntall() as $beboer_id => $array) { ?>
                            <tr id="<?php echo $beboer_id; ?>">
                                <td id="<?php echo $beboer_id; ?>" onclick="vis_tabell(this)" class="btn-link"><?php echo $array['beboer']->getFulltNavn(); ?></td>
                                <td><?php echo $array['torsdag']; ?></td>
                                <td><?php echo $array['fredag']; ?></td>
                                <td><?php echo $array['lordag']; ?></td>
                                <td><button class="btn btn-warning" onclick="slettEgendefinert(<?php echo $beboer_id; ?>)">Slett</button></td>
                            </tr>


                        <?php } ?>
                    </tbody>


                </table>

                <hr/>


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
                <p>
                    Det trengs som regel ikke annet enn en sikkerhetssjef. Denne får da tilgang til HELGA-Inngang, som
                    det som regel settes opp en datamaskin med tilgang til.
                </p>
                <form method="POST">
                    <h4>Legg til verv:</h4>
                    <table class="table table-bordered table-responsive">
                        <input type="hidden" name="form" value="addverv">
                        <tr>
                            <td>Navn:</td>
                            <td><input class="form-control" type="text" name="navn" value=""></td>
                        </tr>
                        <tr>
                            <td>Tilgang (tall fra 0 til hva du vil. > 0 gir tilgang til inngang):</td>
                            <td><input class="form-control" type="text" name="tilgang" value=""></td>
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

        function vis(select) {
            var id = select.options[select.selectedIndex].value;
            $("#beboer").load("?a=helga/beboermodal/" + id);
            $("#beboer-modal").modal("show");
        }

        function vis_tabell(elem) {
            $("#beboer").load("?a=helga/beboermodal/" + elem.id);
            $("#beboer-modal").modal("show");
        }

        function slettEgendefinert(bid) {
            $.ajax({
                type: 'POST',
                url: '?a=helga/general/slettegendefinert',
                data: 'beboer_id=' + bid,
                method: 'POST',
                success: function (data) {
                    window.location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

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


    <div class="modal fade" id="beboer-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Sett gjesteantall</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="beboer">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Lukk</button>
                </div>
            </div>
        </div>
    </div>

<?php
require_once(__DIR__ . '/../static/bunn.php');
?>