<?php

require_once(__DIR__ . '/../topp_utvalg.php');

/* @var \intern3\BeboerListe $beboerliste */

?>

    <script>

        var valgte = [];

        function oppdaterKnapp(id, op) {
            var knappen = document.getElementById("btn-" + id);

            if (op === 'fjern') {
                var knappen = document.getElementById("btn-" + id);
                knappen.innerHTML = "Fjern";
                knappen.className = "btn btn-danger";
                knappen.onclick = function () {
                    fjern(id)
                };
            } else {
                knappen.innerHTML = "Velg";
                knappen.className = "btn btn-primary";
                knappen.onclick = function () {
                    velg(id)
                };
            }
        }

        function velg(id) {

            valgte.push(id);
            oppdaterKnapp(id, "fjern");

        }

        function fjern(id) {
            var index = valgte.indexOf(id);
            if (index !== -1) {
                valgte.splice(index, 1);
            }
            oppdaterKnapp(id, "velg");
        }

        //Inkrementerer ansienniteten til en beboer med et poeng.
        function inkrementer(id) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/ansiennitet/inkrement/' + id,
                method: 'POST',
                success: function (data) {
                    document.getElementById("ans-" + id).value = parseInt(document.getElementById("ans-" + id).value) + 1;
                },
                error: function (req, stat, err) {
                    alert("Noe gikk galt!");
                }
            })

        }

        function oppdater(id, nyverdi) {

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/ansiennitet/oppdater/' + id,
                data: 'nyverdi=' + nyverdi,
                method: 'POST',
                success: function (data) {
                },
                error: function (req, stat, err) {
                    alert("Noe gikk galt!");
                }
            })
        }

        function oppdaterEnkel(id) {
            var nyverdi = document.getElementById("ans-" + id).value;
            oppdater(id, nyverdi);
            tilbakemelding("Oppdaterte beboerens ansiennitet!");

        }

        function inkrementerEnkelt(id) {
            inkrementer(id);
            tilbakemelding("Inkrementerte beboerens ansiennitet!");
        }

        function tilbakemelding(beskjed) {
            document.getElementById("success").style.display = "table";
            document.getElementById("tilbakemelding-text").innerHTML = beskjed;
        }


        function bulkInkrement(modus) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/romsjef/ansiennitet/bulkinkrement/',
                data: 'valgte=' + JSON.stringify(valgte) + "&modus=" + modus,
                method: 'POST',
                success: function (data) {
                    $("#tabellen").load("?a=utvalg/romsjef/ansiennitet #tabellen");
                    if (modus === 1) {
                        tilbakemelding("Inkrementerte alle VALGTE!");
                    } else {
                        tilbakemelding("Inkrementerte alle UVALGTE!");
                    }
                },
                error: function (req, stat, err) {
                    alert("Noe gikk galt!");
                }
            })
        }


    </script>


    <div class="col-md-12">
        <h1>Utvalget &raquo; Romsjef &raquo; Ansiennitet</h1>
        <hr>

        <div class="alert alert-success fade in" id="success" style="margin: auto; margin-top: 5%; display:none">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <p id="tilbakemelding-text"></p>
        </div>
        <p></p>

        <a href="?a=utvalg/romsjef/ansiennitet/eksporter"><button class="btn btn-warning pull-right">Eksporter til CSV</button></a>

        <p>
            <button class="btn btn-info btn-block" onclick="bulkInkrement(1)">Inkrementer alle VALGTE</button>
            <button class="btn btn-primary btn-block" onclick="bulkInkrement(0)">Inkrementer alle UVALGTE</button>
        </p>

        <table class="table table-bordered table-responsive" id="tabellen">
            <thead>
            <tr>
                <th>Velg</th>
                <th>Navn</th>
                <th>Rom</th>
                <th>Ansiennitet</th>
                <th></th>
                <th>Studie</th>
                <th>Født</th>
                <th>Rolle</th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($beboerliste as $beboer) {
                /* @var \intern3\Beboer $beboer */
                if ($beboer == null || !isset($beboer)) {
                    continue;
                }
                ?>
                <tr>
                    <td>
                        <button class="btn btn-primary" id="btn-<?php echo $beboer->getId(); ?>"
                                onclick="velg(<?php echo $beboer->getId(); ?>)">Velg
                        </button>
                    </td>
                    <td>
                        <a href="<?php echo $cd->getBase(); ?>beboer/<?php echo $beboer->getId(); ?>">
                            <?php echo $beboer->getFulltNavn(); ?></a>
                    </td>

                    <td><?php echo $beboer->getRom()->getNavn(); ?></td>
                    <td>
                        <input class="form-control" id="ans-<?php echo $beboer->getId(); ?>" name="ansiennitet"
                               type="number"
                               value="<?php echo $beboer->getAnsiennitet(); ?>">
                    </td>
                    <td>
                        <button class="btn btn-warning" id="oppdater-<?php echo $beboer->getId(); ?>"
                                onclick="oppdaterEnkel(<?php echo $beboer->getId(); ?>)">
                            Oppdater
                        </button>
                        <button class="btn btn-danger" id="inkrementer-<?php echo $beboer->getId(); ?>"
                                onclick="inkrementerEnkelt(<?php echo $beboer->getId(); ?>)">
                            Inkrementer
                        </button>
                    </td>
                    <td><?php
                        $studie = $beboer->getStudie();
                        $skole = $beboer->getSkole();
                        if ($studie == null || $skole == null) {
                            echo ' ';
                        } else {
                            echo $beboer->getKlassetrinn();
                            ?>. <a href="<?php echo $cd->getBase(); ?>studie/<?php echo $studie->getId(); ?>">
                                <?php echo $studie->getNavn(); ?></a>&nbsp;(<a href="<?php echo $cd->getBase(); ?>
                                skole/<?php echo $skole->getId(); ?>"><?php echo $skole->getNavn(); ?></a>)
                            <?php
                        }
                        ?></td>
                    <td><?php echo $beboer->getFodselsdato(); ?></td>
                    <td><?php
                        $utvalgVervListe = $beboer->getUtvalgVervListe();
                        if (count($utvalgVervListe) == 0) {
                            echo str_replace(' ', '&nbsp;', $beboer->getRolle()->getNavn());
                        } else {
                            echo '<strong>' . $utvalgVervListe[0]->getNavn() . '</strong>';
                        }
                        ?></td>
                </tr>
                <?php
            }

            ?>
            </tbody>
        </table>

    </div>


<?php

require_once(__DIR__ . '/../../static/bunn.php');