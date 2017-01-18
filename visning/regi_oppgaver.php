<?php

require_once('topp.php');

?>
<script>
    function meldPa(id) {
        $.ajax({
            type: 'POST',
            url: '?a=regi/oppgave',
            data: 'meldPa=1&id=' + id,
            method: 'POST',
            success: function (html) {
               // $(".container").replaceWith($('.container', $(html)));
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
    function meldAv(id) {
        $.ajax({
            type: 'POST',
            url: '?a=regi/oppgave',
            data: 'meldAv=1&id=' + id,
            method: 'POST',
            success: function (html) {
                // $(".container").replaceWith($('.container', $(html)));
                location.reload();
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }
</script>
<div class="col-md-12">
    <h1>Regi &raquo; Oppgaver</h1>
</div>

<div class="col-md-12 table-responsive">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Navn</th>
            <th>Prioritet</th>
            <th>Anslag timer</th>
            <th>Anslag personer</th>
            <th>Beskrivelse</th>
            <th>Påmeldte</th>
            <th></th>
            <!-- <th> </th> -->
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($oppgaveListe as $oppgave) {
            ?>
            <tr>
                <td><?php echo $oppgave->getNavn(); ?></td>
                <td><?php echo $oppgave->getPrioritetId(); ?></td>
                <td><?php echo $oppgave->getAnslagTimer(); ?></td>
                <td><?php echo $oppgave->getAnslagPersoner(); ?></td>
                <td><?php echo htmlspecialchars($oppgave->getBeskrivelse()); ?></td>
                <td><?php $tekst = "";
                    if(sizeof($oppgave->getPameldteBeboere()) > 0) {
                        foreach ($oppgave->getPameldteBeboere() as $beboer) {
                            $tekst .= $beboer->getFulltNavn() . ',';
                        }
                        $tekst = rtrim($tekst, ',');
                    }
                    echo $tekst
                    ?></td>

                <?php //Kan bare melde seg på hvis man har (halv regi, full regi) og det er ledige plasser, og du er ikke påmeldt fra før.
                if(sizeof($oppgave->getPameldteId()) < $oppgave->getAnslagPersoner() && !in_array($aktuell_beboer, $oppgave->getPameldteBeboere())
                && in_array($aktuell_beboer->getRolleId(), array(1,3))){ ?>
                <td><button class="btn btn-sm btn-default" onclick="meldPa(<?php echo $oppgave->getId(); ?>)">Meld på</button></td>
                <?php }
                    else if(in_array($aktuell_beboer, $oppgave->getPameldteBeboere())){ ?>
                        <td><button class="btn btn-sm btn-danger" onclick="meldAv(<?php echo $oppgave->getId();?>)">Meld av</button></td>
                        <?php
                } else { ?>
                    <td></td>
                <?php } ?>
            </tr>
            <?php
        }

        ?>
        </tbody>
    </table>
</div>

<?php

require_once('bunn.php');

?>
