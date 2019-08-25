<?php

require_once(__DIR__ . '/../static/topp.php');

?>
<script type="text/javascript" src="js/dataTables.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.css"/>
<script>
    $(document).ready(function () {
        var a = $('#tabellen').DataTable({
            "paging": true,
            "searching": false,
            "order": [[0, "desc"]]
        });


    });
</script>
<div class="col-md-12">
    <h1>Kryss &raquo; Historikk</h1>
    <?php /*<p>[ <a href="?a=kryss">Nylig kryssing</a> ] [ <a href="?a=kryss/historikk">Historikk</a> ] [ <a href="?a=kryss/statistikk">Statistikk</a> ]</p>*/ ?>
    <hr>
</div>
<div class="col-md-4 col-sm-6">
    <h2>Alle transaksjoner</h2>
    <table class="table table-bordered" id="tabellen">
        <thead>
        <th data-sortable="true">Tid</th>
        <th>Antall</th>
        <th>Drikke</th>
        </thead>
        <?php

        foreach ($transaksjoner as $kryss) {
            ?>
            <tr>
                <td><?php echo $kryss->tid; ?></td>
                <td><?php echo $kryss->antall; ?></td>
                <td><?php echo $kryss->drikke; ?></td>
            </tr>
            <?php
        }
        foreach ($vinkryss as $kryss) {
            ?>
            <tr>
                <td><?php echo $kryss->getTiden(); ?></td>
                <td><?php echo round($kryss->getAntall(), 2); ?></td>
                <td><?php echo $kryss->getVin()->getNavn(); ?></td>
            </tr>

            <?php
        }

        //TODO Implementer slik at man kan se månedskryss for hver måned med en droppdown meny.
        ?>
    </table>
</div>

<script>

    function periode_select(a) {
        id = a.options[a.selectedIndex].value;
        if(id === 'prehistorisk') {
            $("#perioden").load("?a=kryss/prehistorisk");
        } else {
            $("#perioden").load("?a=kryss/periode/" + id);
        }
    }
</script>

<div class="col-md-4">
    <h2>Ditt alkoholkonsum denne perioden</h2>

    <div class="form-group">
        <select class="form-control" onchange="periode_select(this)">
            <?php
            foreach ($periode as $p) {
                /* @var \intern3\Periode $p */
                ?>
                <option class="form-control" value="<?php echo $p->getId(); ?>"><?php echo $p->toString(); ?></option>
                <?php
            }

            if($prehistorisk) { ?>
                <option class="form-control" value="prehistorisk">Prehistorisk kryssedata</option>
            <?php
            }

            ?>
        </select>
    </div>


    <div id="perioden">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Drikke</th>
                <th>Antall</th>
                <th>Prisanslag (stemmer ikke nødvendigvis)</th>
            </tr>

            <?php
            $totalt = 0;
            $antallet = 0;
            foreach ($mndkryss as $navn => $antall) {

                if($antall < 1) {
                    continue;
                }

                $antallet += $antall;
                ?>
                <tr>
                    <td><?php echo $navn; ?></td>
                    <td><?php echo $antall; ?></td>
                    <td><?php echo($pris = $drikke[$navn] * $antall);
                        $totalt += $pris;
                        ?>kr
                    </td>
                </tr>
            <?php } ?>
            <?php
            foreach ($vin_array as $kryss) {
                $totalt += $kryss['kostnad'];
                $antallet += $kryss['antall'];
                ?>
                <tr>
                    <td><?php echo $kryss['aktuell_vin']->getNavn(); ?></td>
                    <td><?php echo $kryss['antall']; ?></td>
                    <td><?php echo round($kryss['kostnad'], 2); ?>kr</td>
                </tr>
                <?php
            }
            ?>
            <tr>
                <td><b>TOTALT</b></td>
                <td><b><?php echo $antallet; ?></b></td>
                <td><b><?php echo $totalt; ?>kr</b></td>
            </tr>
        </table>
    </div>
</div>

<div class="col-md-4 col-sm-6">
    <h2>Ditt totale alkoholkonsum</h2>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Drikke</th>
            <th>Antall</th>
        </tr>
        <?php

        foreach ($sumKryss as $navn => $antall) {
            ?>
            <tr>
                <td><?php echo $navn; ?></td>
                <td><?php echo $antall; ?></td>
            </tr>
            <?php
        }
        foreach ($vin_totalt as $kryss) { ?>
            <tr>
                <td><?php echo $kryss['aktuell_vin']->getNavn(); ?></td>
                <td><?php echo round($kryss['antall'], 1); ?></td>
            </tr>
            <?php
        }

        ?>
    </table>
</div>
<div class="col-md-4 col-sm-6">
    <h2>Gjennom ukedagene (%)</h2>
    <?php

    $hist = $ukedager;
    include('histogram.php');

    ?>
</div>
<?php

require_once(__DIR__ . '/../static/bunn.php');

?>
