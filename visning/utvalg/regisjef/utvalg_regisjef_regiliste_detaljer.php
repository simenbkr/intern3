<?php
require_once(__DIR__ . '/../topp_utvalg.php');
/* @var \intern3\Regiliste $regiliste */
?>

    <div class="col-lg-12">


    <h1>Utvalget » Regisjef » Regiliste » Detaljer for <?php echo $regiliste->getNavn(); ?></h1>
    <hr>

    <p>
        Denne regilisten har til sammen igjen <?php echo round($regiliste->getTotaleTimerIgjen(),2 ); ?> timer.
    </p>

    <p>
        <input class="form-control" type="text" value="<?php echo $regiliste->getNavn(); ?>" id="navn">
    </p>

    <p>
        <button class="btn btn-danger" onclick="endre()">Endre</button>
    </p>


    <table class="table table-bordered table-responsive" id="tabellen">
        <thead>
        <tr>
            <th>Velg</th>
            <th>Navn</th>
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

    <script>
        var lista = <?php echo json_encode($regiliste->getIdliste(), true); ?>;
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

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/regiliste/endre/<?php echo $regiliste->getId(); ?>',
                data: 'add=' + id,
                method: 'POST',
                success: function (data) {
                    console.log(data)
                    //window.location.replace("?a=utvalg/regisjef/regiliste/");
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert("Noe gikk galt!");
                }
            });

        }




        function fjern(id) {
            var index = valgte.indexOf(id);
            if (index !== -1) {
                valgte.splice(index, 1);
            }
            oppdaterKnapp(id, "velg");

            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/regiliste/endre/<?php echo $regiliste->getId(); ?>',
                data: 'del=' + id,
                method: 'POST',
                success: function (data) {
                    console.log(data)
                    //window.location.replace("?a=utvalg/regisjef/regiliste/");
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert("Noe gikk galt!");
                }
            })

        }

        $(document).ready(function() {


            lista.forEach(function(element) {

                valgte.push(element);
                oppdaterKnapp(element, 'fjern');

            });

        });

        function endre(){
            var navn = document.getElementById('navn').value;
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/regiliste/endre/<?php echo $regiliste->getId(); ?>',
                data: 'navn=' + navn, // + '&valgte=' + JSON.stringify(valgte),
                method: 'POST',
                success: function (data) {
                    console.log(data)
                    //window.location.replace("?a=utvalg/regisjef/regiliste/");
                    //location.reload();
                },
                error: function (req, stat, err) {
                    alert("Noe gikk galt!");
                }
            })

        }


    </script>





<?php
require_once(__DIR__ . '/../../static/bunn.php');