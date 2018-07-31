<?php

require_once('static/topp.php');

?>
<script>
    function mer(elem){
        document.getElementById(elem + 'mer').style.display = 'block';
        document.getElementById(elem + 'mindre').style.display = 'none';
        console.log(document.getElementById(elem + 'mer').style.display = 'block');
    }

    function mindre(elem){
        document.getElementById(elem + 'mer').style.display = 'none';
        document.getElementById(elem + 'mindre').style.display = 'block';
    }


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


    $(document).ready(function(){
        $('.read-more-content').addClass('hide')
        // Set up a link to expand the hidden content:
            .before(' <a class="read-more-show" href="#">Mer</a>')

            // Set up a link to hide the expanded content.
            .append(' <a class="read-more-hide" href="#">Mindre</a>');

        // Set up the toggle effect:
        $('.read-more-show').on('click', function(e) {
            $(this).next('.read-more-content').removeClass('hide');
            $(this).addClass('hide');
            e.preventDefault();
        });

        $('.read-more-hide').on('click', function(e) {
            $(this).parent('.read-more-content').addClass('hide').parent().children('.read-more-show').removeClass('hide');
            e.preventDefault();
        });
    })

</script>

<div class="col-sm-6 col-xs-12">
    <h1>Datoer framover</h1>
    <hr>
    <p><span class="tekst_dinevakter"><?php
            if ($cd->getAktivBruker()->getPerson()->harVakt()) {
                echo '<span class="merke_dinevakter" title=""; > </span> Dine vakter';
            } else {
                echo 'Du skal ikke sitte vakter';
            }
            ?></span>, <span class="merke_bursdag" title="" ;> </span><span class="tekst_bursdager"> Bursdager</span>,
        <span class="merke_viktigedatoer" title="" ;></span><span class="tekst_viktigedatoer"> Viktige datoer</span></p>
    <table class="table-bordered table">
        <tr>
            <th>U<span class="hidden-sm hidden-xs">ke</span></th>
            <th>M<span class="hidden-sm hidden-xs">andag</span></th>
            <th>T<span class="hidden-sm hidden-xs">irsdag</span></th>
            <th>O<span class="hidden-sm hidden-xs">nsdag</span></th>
            <th>T<span class="hidden-sm hidden-xs">orsdag</span></th>
            <th>F<span class="hidden-sm hidden-xs">redag</span></th>
            <th>L<span class="hidden-sm hidden-xs">ørdag</span></th>
            <th>S<span class="hidden-sm hidden-xs">øndag</span></th>
        </tr>
        <?php

        $denneManed = date('m');
        $detteAr = date('Y');
        $manedSlutt = strtotime('first day of this month, midnight');

        foreach (range($denneManed, $denneManed > 6 ? 12 : 6) as $maned) {
            $manedStart = $manedSlutt;
            $manedSlutt = strtotime('next month', $manedStart);
            ?>
            <tr>
            <th colspan="8"><?php echo ucfirst(strftime('%B', $manedStart)); ?></th>
            <?php
            $dag = strtotime('last Monday', $manedStart);
            $ant = 0;
            do {
                if ($ant++ % 7 == 0) {
                    ?>
                    </tr>
                    <tr>
                    <td><?php echo date('W', $dag); ?></td>
                    <?php
                }
                ?>
                <td<?php
                if ($dag < $manedStart || $manedSlutt <= $dag) {
                    echo ' style="opacity: .5;"';
                } else if ($dag == strtotime('today, midnight')) {
                    echo ' style="outline: 2px dashed #000;"';
                }
                ?>><?php
                    echo date('j', $dag);
                    $bursdager = intern3\BeboerListe::medBursdag(date('m-d', $dag));
                    if (count($bursdager) > 0) {
                        echo '<span class="kalender_merke_bursdag" title="';
                        foreach ($bursdager as $bursdag) {
                            echo $bursdag->getFulltNavn() . ' (' . $bursdag->getAlderIAr() . ' år)' . PHP_EOL;
                        }
                        echo '"> </span>';
                    }
                    $dineVakter = intern3\VaktListe::medDatoBrukerId(date('Y-m-d', $dag), $cd->getAktivBruker()->getId());
                    if (count($dineVakter) > 0) {
                        echo '<span class="kalender_merke_dinevakter" title="';
                        foreach ($dineVakter as $vakt) {
                            echo $vakt->getVakttype() . '. vakt' . PHP_EOL;
                        }
                        echo '"> </span>';
                    }
                    ?></td>
                <?php
            } while (($dag = strtotime('next day', $dag)) < $manedSlutt || $ant % 7 <> 0);
            ?>
            </tr>
            <?php
        }
        ?>
    </table>
</div>


<?php if(count($oppgaveListe) > 0){ ?>
<div class="col-sm-6 col-xs-12">
    <h1>Regioppgaver <a href="?a=regi/oppgave"><button class="btn btn-primary btn-sm">Påmelding</button></a><br/></h1>
    <hr>
    <table class="table-bordered table-responsive table">
        <thead>
        <tr>
            <th>Navn</th>
            <th>Beskrivelse</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($oppgaveListe as $oppgave) {
            /* @var \intern3\Oppgave $oppgave */
            ?>
            <tr>
                <td><?php echo $oppgave->getNavn(); ?></td>
                <td><?php echo htmlspecialchars($oppgave->getBeskrivelse()); ?></td>
                <td>
                    <?php //Kan bare melde seg på hvis man har (halv regi, full regi) og det er ledige plasser, og du er ikke påmeldt fra før.
                    if(sizeof($oppgave->getPameldteId()) < $oppgave->getAnslagPersoner() && !in_array($aktuell_beboer, $oppgave->getPameldteBeboere())
                    && in_array($aktuell_beboer->getRolleId(), array(1,3)) && !$oppgave->erFryst()){ ?>
                <button class="btn btn-sm btn-default" onclick="meldPa(<?php echo $oppgave->getId(); ?>)">Meld på</button>
                <?php }
                else if(in_array($aktuell_beboer, $oppgave->getPameldteBeboere()) && !$oppgave->erFryst()){ ?>
                    <button class="btn btn-sm btn-danger" onclick="meldAv(<?php echo $oppgave->getId();?>)">Meld av</button>
                    <?php
                } ?>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
    
<?php }

if(count($verv_meldinger) > 0) {
   
    ?>
<div class="col-lg-6">
    <h1>Meldinger fra åpmend</h1>
    <hr>
    <table class="table table-responsive table-bordered">
        <?php foreach ($verv_meldinger as $verv_melding) {
            /* @var \intern3\VervMelding $verv_melding */
    
            if($verv_melding == null || $verv_melding->getVerv() == null ||
                $verv_melding->getBeboer() == null || strlen($verv_melding->getTekst()) < 3){
                continue;
            }
            ?>
            <tr>
                <td>
                    <?php echo $verv_melding->getVerv()->getNavn(); ?>,
                    <?php echo $verv_melding->getBeboer()->getFulltNavn(); ?>
                    (<?php echo $verv_melding->getDato();?>):
                </td>
                <td><p><span id="<?php echo $verv_melding->getId(); ?>mindre">
                            
                            <?php echo substr($verv_melding->getTekst(),0,50); ?>
                        
                            <a href="#" onclick="mer('<?php echo $verv_melding->getId(); ?>')">Mer</a>
                        </span>
                        
                        
                        <?php if(strlen($verv_melding->getTekst()) > 50) { ?>
                            
                            
                            <span id="<?php echo $verv_melding->getId(); ?>mer" style="display:none">
                            
                            <?php echo $verv_melding->getTekst(); ?>
                                
                                <a href="#" onclick="mindre('<?php echo $verv_melding->getId(); ?>')">Mindre</a>
                            </span>
                        <?php } ?>
                    </p>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php } ?>
<?php

require_once('static/bunn.php');

?>
