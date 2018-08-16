<?php

require_once(__DIR__ . '/../topp_utvalg.php');

/* @var \intern3\Oppgave $oppgaven */

?>
    <div class="col-md-12">
        <h1>Utvalget &raquo; Regisjef &raquo; Oppgave &raquo; Detaljert for <?php echo $oppgaven->getNavn(); ?></h1>
    <hr>


<?php require_once (__DIR__ . '/../../static/tilbakemelding.php'); ?>

    <table class="form table table-responsive table-bordered">
        <tr>
            <th>Navn</th>
            <td><?php echo $oppgaven->getNavn(); ?></td>
        </tr>
        <tr>
            <th>Opprettet</th>
            <td><?php echo $oppgaven->getTidOppretta(); ?></td>
        </tr>
        
        <?php if($oppgaven->getTidUtfore() !== null){ ?>
        <tr>
            <th>Utføringsdato</th>
            <td><?php echo $oppgaven->getTidUtfore(); ?></td>
        </tr>
        <?php } ?>
        
        <tr>
            <th>Anslag timer</th>
            <td><?php echo $oppgaven->getAnslagTimer(); ?></td>
        </tr>
        <tr>
            <th>Anslag personer</th>
            <td><?php echo $oppgaven->getAnslagPersoner(); ?></td>
        </tr>
        <tr>
            <th>Beskrivelse</th>
            <td><?php echo $oppgaven->getBeskrivelse(); ?></td>
        </tr>
        <tr>
            <th>Påmeldte</th>
            <td>
                <?php
                $paameldte = "";
                if (sizeof($oppgaven->getPameldteBeboere()) > 0) {
                    foreach ($oppgaven->getPameldteBeboere() as $beboer) {
                        $paameldte .= $beboer->getFulltNavn() . ' <button onclick="fjernFraOppgave(' . $beboer->getId() . ',' . $oppgaven->getId() . ')">&#x2718;</button>, ';
                    }
                    $paameldte = rtrim($paameldte, ', ');
                }
                
                echo $paameldte;
                ?>
            </td>
        </tr>
        
        <tr>
            <th>Legg til beboere</th>
            <td>
                <div>
                    <input type="button" class="btn btn-sm btn-info" value="Legg Til"
                           onclick="modal.call(this)" data-target="beboerlista"
                           data-name="beboerlista">
                    <div id="modal-beboerlista">
                    </div>
                </div>
            </td>
        </tr>
        

        <tr>
            <th>Godkjent</th>
            <td><?php
                if ($oppgaven->getGodkjent() != 0) { ?>
                    Godkjent av <?php echo $oppgaven->getGodkjentBruker()->getPerson()->getFulltNavn(); ?>,
                    <?php echo $oppgaven->getTidGodkjent(); ?>
                    <?php
                }
                ?>
            </td>
        </tr>

        <tr>
            <th>Godkjenn/Fjern/Frys</th>
            <td>
                <?php if ($oppgaven->getGodkjent() == 0) { ?>
                    <button class="btn btn-default" onclick="godkjenn(<?php echo $oppgaven->getId(); ?>)">Godkjenn
                    </button>
                <?php } ?>

                <button class="btn btn-default" onclick="fjern(<?php echo $oppgaven->getId(); ?>)">Fjern</button>
                
                <?php if (!$oppgaven->erFryst()) { ?>
                    <button class="btn btn-default" onclick="frys(<?php echo $oppgaven->getId(); ?>)">Frys</button>
                <?php } else { ?>
                    <button class="btn btn-default" onclick="afrys(<?php echo $oppgaven->getId(); ?>)">Fjern frys
                    </button>
                <?php } ?>

            </td>
        </tr>

        <tr>
            <th>Slett</th>
            <td>
                <button class="btn btn-sm btn-danger" onclick="slett(<?php echo $oppgaven->getId(); ?>)">Slett
                    oppgaven
                </button>
            </td>
        </tr>


    </table>
    </div>


    <script>
        function fjern(id) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/oppgave/',
                data: 'fjern=' + id,
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function slett(id) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/oppgave/',
                data: 'slett=' + id,
                method: 'POST',
                success: function (html) {
                    window.location.href = "?a=utvalg/regisjef/oppgave";
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function fjernFraOppgave(id, oppgaveId) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/oppgave/',
                data: 'fjernFraOppgave=' + id + "&oppgaveId=" + oppgaveId,
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function godkjenn(id) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/oppgave/',
                data: 'godkjenn=' + id,
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function frys(id) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/oppgave/',
                data: 'frys=' + id,
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function afrys(id) {
            $.ajax({
                type: 'POST',
                url: '?a=utvalg/regisjef/oppgave/',
                data: 'afrys=' + id,
                method: 'POST',
                success: function (html) {
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function modal() {
            $.ajax({
                cache: false,
                type: 'POST',
                url: '?a=utvalg/regisjef/beboermodal',
                data: 'oppgaven=' + '<?php echo $oppgaven->getId(); ?>',
                success: function (data) {
                    $('#modal-beboerlista').html(data);
                    $('#beboerlista').modal('show');
                    $('#beboerlista').on('hidden.bs.modal', function () {
                        $('#beboerlista').html(' ');
                    });
                }
            });
        }
        
    </script>
    
    <?php
    
    require_once(__DIR__ . '/../../static/bunn.php');