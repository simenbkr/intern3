<?php
require_once('topp.php');
?>
    <script>
        <?php
        foreach (intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId()) as $vakt) {
            $temp = $vakt->getId();
            echo "var action$temp = 0; //1 = gisbort, 0 = byttes
        var passord$temp = 0;

        function setAction$temp (int) {
            action$temp = int;
        }
        function setPassord$temp(int, id) {
            passord$temp = int;

            if (int == 0) {
                hideElem(id);
            }
            else {
                showElem(id);
            }
        }
        ";
        }
        ?>
        function hideElem(id) {
            document.getElementById(id).style.display = 'none';
        }
        function showElem(id) {
            document.getElementById(id).style.display = 'block';
        }

        function doShit(id) {
            var passordet = document.getElementById(id).value;
            var merknad = document.getElementById(id + "merk").value;
            //merknad = btoa(merknad);
            //console.log('vaktbytte=1&id=' + id + '&action=' + action + "&passord=" + passord + "&passordet=" + passordet + "&merknad=" + merknad);
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=1&id=' + id + '&action=' + window['action' + id] + "&passord=" + window['passord' + id] + "&passordet=" + passordet + "&merknad=" + merknad,
                method: 'POST',
                success: function (data) {
                    //$(".container").replaceWith($('.container', $(data)));
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function fjernVaktBytte(id, vaktId) {
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=2&id=' + id + '&vaktId=' + vaktId,
                method: 'POST',
                success: function (data) {
                    //$(".container").replaceWith($('.container', $(data)));
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function taBortgittVakt(id, vaktId) {
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=3&id=' + id + '&vaktId=' + vaktId,
                method: 'POST',
                success: function (data) {
                    //$(".container").replaceWith($('.container', $(data)));
                    location.reload();
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

    </script>
    <div class="col-md-12">
        <h1>Vakt &raquo; Vaktbytte</h1>
        <p>[ <a href="<?php echo $cd->getBase(); ?>vakt">Vaktliste</a> ] [ Vaktbytte ]</p>
    </div>
    <div class="container">
        <div class="col-md-3 col-sm-6 col-sx-12">
            <?php
            function visDineVakter($visFerdig = true)
            {
                global $cd;
                ?>
                <table class="table table-bordered">
                    <tr>
                        <th>Dine vakter</th>
                    </tr>
                    <?php
                    foreach (intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId()) as $vakt) {
                        $tid = strtotime($vakt->getDato());
                        $tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
                        ?>
                        <tr>
                            <?php
                            if ($vakt->erFerdig() || $vakt->getBytte()) {
                                if ($visFerdig) {
                                    ?>
                                    <td class="celle_graa"><?php echo $tekst; ?></td>
                                    <?php
                                }
                            } else {
                                ?>
                                <td>
                                    <input type="button" class="btn btn-default"
                                           value="<?php echo $tekst ?>" data-toggle="modal"
                                           data-target="#ledig<?php echo $vakt->getId(); ?>"></td>


                                    <div class="modal fade" id="ledig<?php echo $vakt->getId(); ?>" role="dialog">
                                        test
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content panel-primary">
                                                <div class="modal-header panel-heading">
                                                    <button type="button" class="close"
                                                            data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title"
                                                        align="center"><?php echo $tekst; ?></h4>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="radio">
                                                        <label><input type="radio" name="optradio<?php echo $vakt->getId(); ?>"
                                                                      id="bytt" onclick="setAction<?php echo $vakt->getId(); ?>(0)"
                                                                      value="0" checked="checked">Byttes</label>
                                                        <label><input type="radio" name="optradio<?php echo $vakt->getId(); ?>"
                                                                      id="gibort" onclick="setAction<?php echo $vakt->getId(); ?>(1)"
                                                                      value="1">Gis bort</label>
                                                    </div>
                                                    Passord?
                                                    <div class="radio">
                                                        <label><input type="radio" name="optradio1<?php echo $vakt->getId(); ?>"
                                                                      id="uten"
                                                                      onclick="setPassord<?php echo $vakt->getId(); ?>(0,<?php echo $vakt->getId(); ?>)"
                                                                      value="1"
                                                                      checked="checked">Uten passord</label>
                                                        <label><input type="radio" name="optradio1<?php echo $vakt->getId(); ?>"
                                                                      id="med"
                                                                      onclick="setPassord<?php echo $vakt->getId(); ?>(1, <?php echo $vakt->getId(); ?>)"
                                                                      value="0">Med passord</label>
                                                    </div>
                                                    <input type="password" name="passord" placeholder="Passord"
                                                           id="<?php echo $vakt->getId(); ?>"
                                                           style="display:none;">
                                                    <input type="text" name="passord" placeholder="Merknad"
                                                           id="<?php echo $vakt->getId(); ?>merk">
                                                    <input class="btn btn-sm btn-warning pull-right" type="button" value="Bytt"
                                                           onclick="doShit(<?php echo $vakt->getId(); ?>)">

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                                        Lukk
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <?php
            }

            visDineVakter();
            ?>
        </div>
        <?php
        if (isset($visBytteListe)) {
            ?>
            <div class="col-md-12"></div>
            <?php
            foreach (range(1, 4) as $vakttype) {
                ?>
                <div class="col-md-3 col-sm-6 col-sx-12">
                    <table class="table table-bordered">
                        <tr>
                            <th><?php echo $vakttype; ?>.&nbsp;vakt</th>
                        </tr>
                        <?php
                        foreach ($vaktbytteListe[$vakttype] as $vaktbytte) {
                            $bruker = $vaktbytte->getVakt()->getBruker();
                            if ($bruker == null) {
                                continue;
                            }
                            $modalId = 'modal-' . date('m-d', strtotime($vaktbytte->getVakt()->getDato())) . '-' . $vaktbytte->getVakt()->getVakttype();
                            ?>
                            <tr>
                            <td>
                                <?php
                                if ($vaktbytte->harPassord()) {
                                    echo "<span class=\"glyphicon glyphicon-lock\"></span>";
                                }
                                if ($vaktbytte->getVakt()->getBrukerId() != $cd->getAktivBruker()->getId()) {
                                    echo '				<input type="button" class="btn btn-sm btn-info pull-right" value="Bytt" data-toggle="modal" data-target="#' . $modalId . '">' . PHP_EOL;
                                } else { ?>
                                    <input class="btn btn-sm btn-danger pull-right" type="button" value="Trekk"
                                           onclick="fjernVaktBytte(<?php echo $vaktbytte->getId(); ?>, <?php echo $vaktbytte->getVaktId(); ?>)">
                                    <?php
                                }
                                echo '				<strong>' . ucfirst(strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato()))) . '</strong>' . PHP_EOL;
                                echo '				<br>' . PHP_EOL;
                                echo $bruker->getPerson()->getFulltNavn();
                                $merknaden = $vaktbytte->getMerknad();
                                if ($merknaden != null) {
                                    echo "<br/>" . $vaktbytte->getMerknad();
                                }
                                ?>
                                <div class="modal fade" id="<?php echo $modalId; ?>" role="dialog">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content panel-primary">
                                            <div class="modal-header panel-heading">
                                                <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title"
                                                    align="center"><?php echo $vaktbytte->getVakt()->getVakttype() . '. vakt ' . strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato())); ?></h4>
                                            </div>
                                            <div class="modal-body" align="center">
                                                <?php if (!$vaktbytte->getGisBort()) { ?>
                                                    <p>Hvilken vakt vil du foreslå å bytte?</p>
                                                    <?php visDineVakter(false);
                                                } else { ?>
                                                    <p>Du ønsker altså å ta denne vakta?</p>
                                                    <input class="btn btn-primary" type="button" value="Ja!"
                                                           onclick="taBortgittVakt(<?php echo $vaktbytte->getId(); ?>,<?php echo $vaktbytte->getVaktId(); ?>)">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                                                        Nei!
                                                    </button><br/><br/>
                                                <?php } ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">
                                                    Lukk
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            </tr><?php
                        }
                        ?>    </table>
                </div>
                <?php
            }
        }
        ?>
    </div>

<?php
require_once('bunn.php');
?>