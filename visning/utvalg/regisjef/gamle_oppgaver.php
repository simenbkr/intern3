<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>
<div class="col-md-12 table-responsive">
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Navn</th>
            <th>Prioritet</th>
            <th>Anslag timer</th>
            <th>Anslag personer</th>
            <th>Påmeldte</th>
            <th>Beskrivelse</th>
            <th>Opprettet</th>
            <th>Utførelsesdato</th>
            <th>Godkjent</th>
            <th>Godkjenn/Fjern</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php

        foreach ($oppgaveListe as $oppgave) {
            /* @var \intern3\Oppgave $oppgave */

            if($oppgave->erArkivert()) {
                continue;
            }

            $id = $oppgave->getId();
            $navn = $oppgave->getNavn();
            $pri = $oppgave->getPrioritetId();
            $timer = $oppgave->getAnslagTimer();
            $personer = $oppgave->getAnslagPersoner();
            $beskrivelse = $oppgave->getBeskrivelse();
            $oppretta = $oppgave->getTidOppretta();
            $tidgodkjent = $oppgave->getTidGodkjent();
            $godkjent = $oppgave->getGodkjent();
            $godkjentav = '';
            if ($oppgave->getGodkjent() && $oppgave->getGodkjentBruker() != null
                && $oppgave->getGodkjentBruker()->getPerson() != null) {

                $godkjentav = $oppgave->getGodkjentBruker()->getPerson()->getFulltNavn();
            }
            $paameldte = "";
            if (sizeof($oppgave->getPameldteBeboere()) > 0) {
                foreach ($oppgave->getPameldteBeboere() as $beboer) {
                    $paameldte .= $beboer->getFulltNavn() . ' <button onclick="fjernFraOppgave(' . $beboer->getId() . ',' . $id . ')">&#x2718;</button>, ';
                }
                $paameldte = rtrim($paameldte, ', ');
                // echo "      <button onclick=\"fjern(" . $general->getId() . ")\">&#x2718;</button>";
            }
            ?>
            <tr id="<?php echo $id; ?>">
                <td><a href="?a=utvalg/regisjef/oppgave/<?php echo $id; ?>"><?php echo $navn; ?></a></td>
                <td><?php echo $pri; ?> </td>
                <td><?php echo $timer; ?></td>
                <td><?php echo $personer; ?></td>
                <td><?php echo $paameldte; ?></td>
                <td><?php echo $beskrivelse; ?></td>
                <td><?php echo $oppretta; ?></td>
                <td><?php echo $oppgave->getTidTekst(); ?></td>
                <?php /*<td><?php echo $oppgave->getGodkjent () != 0 ? '<span title="Godkjent av ' . $godkjentav
                        . '" > ' . $oppgave->getTidGodkjent() . '</span>' : ''; ?></td>*/ ?>
                <td><?php
                    if ($oppgave->getGodkjent() != 0) { ?>
                        Godkjent av <?php echo $godkjentav; ?>, <?php echo $tidgodkjent; ?>
                        <?php
                    }
                    ?></td>

                <td><?php if ($godkjent == 0) { ?>
                        <button class="btn btn-default" onclick="godkjenn(<?php echo $id; ?>)">Godkjenn</button>
                    <?php } ?>

                    <button class="btn btn-default" onclick="fjern(<?php echo $id; ?>)">Fjern</button>

                    <?php if (!$oppgave->erFryst()) { ?>
                        <button class="btn btn-default" onclick="frys(<?php echo $id; ?>)">Frys</button>
                    <?php } else { ?>
                        <button class="btn btn-default" onclick="afrys(<?php echo $id; ?>)">Fjern frys</button>
                    <?php } ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="slett(<?php echo $oppgave->getId(); ?>)">Slett
                        oppgaven
                    </button>
                </td>
            </tr>
            <?php
        }


        ?>
        </tbody>
    </table>
</div>
<?php

require_once(__DIR__ . '/../../static/bunn.php');