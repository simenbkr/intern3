<?php
require_once(__DIR__ . '/../topp_utvalg.php');
?>


    <div class="col-lg-12">


        <h1>Utvalget » Regisjef » Regiliste » Ny</h1>
        <hr>

        <p>
            <input class="form-control" type="text" placeholder="Listens navn" id="navn">
        </p>

        <p>
        <button class="btn btn-warning" onclick="opprett()">Opprett</button>
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

        <p>
            <button class="btn btn-warning" onclick="opprett()">Opprett</button>
        </p>


    </div>

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

    function opprett(){
        var navn = document.getElementById('navn').value;
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/regiliste/opprett',
            data: 'navn=' + navn + '&valgte=' + JSON.stringify(valgte),
            method: 'POST',
            success: function (data) {
                window.location.replace("?a=utvalg/regisjef/regiliste/");
            },
            error: function (req, stat, err) {
                alert("Noe gikk galt!");
            }
        })

    }

</script>


<?php
require_once(__DIR__ . '/../../static/bunn.php');