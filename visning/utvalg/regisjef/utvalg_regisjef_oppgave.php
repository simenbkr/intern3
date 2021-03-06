<?php

require_once(__DIR__ . '/../topp_utvalg.php');

?>
<link rel="stylesheet" href="css/chosen.min.css">
<script src="js/chosen.jquery.min.js"></script>

<script>

    $(function() {
        $('#datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(datetext){
                var d = new Date(); // for now
                var h = d.getHours();
                h = (h < 10) ? ("0" + h) : h ;

                var m = d.getMinutes();
                m = (m < 10) ? ("0" + m) : m ;

                var s = d.getSeconds();
                s = (s < 10) ? ("0" + s) : s ;

                datetext = datetext + " " + h + ":" + m;
                $('#datepicker').val(datetext);
            },
        });
    });

    $(function() {
        $('#datepicker2').datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect: function(datetext){
                var d = new Date(); // for now
                var h = d.getHours();
                h = (h < 10) ? ("0" + h) : h ;

                var m = d.getMinutes();
                m = (m < 10) ? ("0" + m) : m ;

                var s = d.getSeconds();
                s = (s < 10) ? ("0" + s) : s ;

                datetext = datetext + " " + h + ":" + m;
                $('#datepicker2').val(datetext);
            },
        });
    });


    
    var i = 0;
    
    function forslag(){
        
        var antall = document.getElementById("anslag-pers").value;
        var regiliste_id = $('#regiliste option:selected').attr('id');
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/oppgave/forslag',
            data: 'antall=' + antall + '&regiliste_id=' + regiliste_id,
            method: 'POST',
            success: function (html) {
                var obj = jQuery.parseJSON(html);
                
                var select = $('#selecten');
                
                jQuery.each(obj, function(i, val){
                    console.log(val.navn);
                    if(i === 0) {
                        selectChosenOptions(select, val.id);
                    } else {
                        selectAdditionalChosenOptions(select, val.id);
                    }
                    i++;
                })
                
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
        
    }

    function selectChosenOptions($select, values) {
        $select.val(null);                                  //delete current options
        $select.val(values);                                //add new options
        $select.trigger('chosen:updated');
    }

    function selectAdditionalChosenOptions($select, values) {
        var selected = $select.val() || [];                 //get current options
        selected = selected.concat(values);                 //merge with new options
        selected = $.grep(selected, function(el, index) {
            return index === $.inArray(el, selected);       //make options unique
        });
        $select.val(null);                                  //delete current options
        $select.val(selected);                              //add new options
        $select.trigger('chosen:updated');
    }
    

    function fjern(id) {
        $.ajax({
            type: 'POST',
            url: '?a=utvalg/regisjef/oppgave/',
            data: 'fjern=' + id,
            method: 'POST',
            success: function (html) {
                var parser = new DOMParser();
                var response = parser.parseFromString(html, "text/html");
                $('#' + id).replaceWith(response.getElementById(id));
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
                document.getElementById(id).remove();
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
                $(".container").replaceWith($('.container', $(html)));
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
                var parser = new DOMParser();
                var response = parser.parseFromString(html, "text/html");
                $('#' + id).replaceWith(response.getElementById(id));
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
                var parser = new DOMParser();
                var response = parser.parseFromString(html, "text/html");
                $('#' + id).replaceWith(response.getElementById(id));
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
                var parser = new DOMParser();
                var response = parser.parseFromString(html, "text/html");
                $('#' + id).replaceWith(response.getElementById(id));
            },
            error: function (req, stat, err) {
                alert(err);
            }
        });
    }

    function byttPolymorfkategori(id) {
        $('#polymorfkategori_ymse').hide();
        $('#polymorfkategori_feil').hide();
        $('#polymorfkategori_rapp').hide();
        $('#polymorfkategori_oppg').hide();
        switch (id) {
            case 'ymse':
                $('#polymorfkategori_ymse').show();
                break;
            case 'feil':
                $('#polymorfkategori_feil').show();
                break;
            case 'rapp':
                $('#polymorfkategori_rapp').show();
                break;
            case 'oppg':
                $('#polymorfkategori_oppg').show();
                break;
        }
    }

    $(function () {
        $("#datepicker").datepicker({dateFormat: "yy-mm-dd"});
        $("#datepicker2").datepicker({dateFormat: "yy-mm-dd"});
    });

    $(document).ready(function () {
        byttPolymorfkategori('<?php echo isset($_POST['polymorfkategori_velger']) ? $_POST['polymorfkategori_velger'] : 'ymse'; ?>');
    });

    jQuery(document).ready(function(){
        jQuery(".chosen").chosen();
    });
    
</script>

<div class="container">

    <div class="col-md-12">
        <h1>Utvalget &raquo; Regisjef &raquo; Oppgave</h1>

        <a href="?a=utvalg/regisjef/oppgave/gamle">Gamle oppgaver</a>

        <?php include(__DIR__ . '/../../static/tilbakemelding.php'); ?>
        <?php if (isset($feilSubmit)) { ?>
            <div class="alert alert-danger fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <p>Oppgaven ble ikke lagt inn - du manglet et felt.</p>
            </div>
            <p></p>
        <?php }
        unset($feilSubmit); ?>
        <p>
            Her kan du håndtere oppgaver som Regisjef. Legg merke til at enkelte felt kan være blanke. Obligatoriske felt er merket med (*).
        </p>

        <hr>
    </div>

    <div class="tilbakemeldinger">
        <?php if (isset($slettet) && isset($melding)) { ?>
            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $melding; ?>
            </div>
            <p></p>
            <?php
        }
        unset($slettet); ?>
    </div>
    <div class="col-md-6 col-sm-12">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
            <table class="table table-bordered">
                <tr>
                    <th>Navn (*)</th>
                    <td><input name="navn"
                               class="form-control" <?php echo isset($_POST['navn']) ? ' value="' . $_POST['navn'] . '"' : ''; ?>>
                    </td>
                </tr>

                <tr>
                    <th>Utførelsesdato (fra)</th>
                    <td>
                        <div class="form-group">
                            <input class="form-control" id="datepicker" name="dato" size="3">
                        </div>
                    </td>

                </tr>

                <tr>
                    <th>Tid ferdig (til)</th>
                    <td>
                        <div class="form-group">
                            <input class="form-control" id="datepicker2" name="dato2" size="3">
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>Anslag timer (*)</th>
                    <td><input name="timer" class="form-control" id="anslag-timer"
                               placeholder="0:00"<?php echo isset($_POST['timer']) ? ' value="' . $_POST['timer'] . '"' : ''; ?>>
                    </td>
                </tr>
                <tr>
                    <th>Anslag personer (*)</th>
                    <td><input type="number" id="anslag-pers"
                                name="personer"
                                class="form-control" <?php echo isset($_POST['personer']) ? ' value="' . $_POST['personer'] . '"' : ''; ?>>
                    </td>
                </tr>

                <tr>
                    <th>Velg Regilisten forslag skal komme fra (*)</th>
                    <td>
                        <select id="regiliste" class="form-control">

                            <?php foreach($regilister as $regiliste) { ?>

                            <option id="<?php echo $regiliste->getId(); ?>"><?php echo $regiliste->getNavn(); ?></option>

                            <?php } ?>


                        </select>

                    </td>
                </tr>

                <tr>
                    <th>Meld på personer (*)</th>
                    <td>
                        <select class="chosen" multiple="multiple" name="tildelte[]" id="selecten">
                            <?php
                            
                            foreach ($beboerListe as $beboer) {
                                /* @var \intern3\Beboer $beboer */
                                ?>
                                <option value="<?php echo $beboer->getId(); ?>"><?php echo $beboer->getFulltNavn(); ?></option>
                            <?php }
                            ?>
                        </select>
                        
                        <input type="button" class="btn btn-danger btn-sm" onclick="forslag()" value="Forslag">

                    </td>
                </tr>
                <tr>
                    <th>Beskrivelse (*)</th>
                    <td><textarea name="beskrivelse" cols="50" class="form-control"
                                  rows="5"><?php echo isset($_POST['beskrivelse']) ? $_POST['beskrivelse'] : ''; ?></textarea>
                    </td>
                </tr>

                <tr>
                    <th>Huk av for å ikke sende e-post.</th>
                    <td><input type="checkbox" name="epost" value="1"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" class="btn btn-primary" name="registrer" value="Registrer"></td>
                </tr>
            </table>
        </form>
    </div>

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
</div>
<?php

require_once(__DIR__ . '/../../static/bunn.php');

?>
