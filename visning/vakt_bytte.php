<?php
require_once('topp.php');
$aktiv_bruker = \intern3\LogginnCtrl::getAktivBruker();
$rolle = $aktiv_bruker->getPerson()->getRolleId();
$har_vakt = $rolle == 1 || $rolle == 2;
?>
    <script>

        function showElem(id) {
            document.getElementById(id).style.display = 'block';
        }

        function hideElem(id) {
            document.getElementById(id).style.display = 'none';
        }

        <?php
        //Lag en setAction og setPassord funksjon for hver eneste tingetang. Wooohoo.
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
        function doShit(id) {
            var passordet = document.getElementById(id).value;
            var merknad = document.getElementById(id + "merk").value;
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=1&id=' + id + '&action=' + window['action' + id] + "&passord=" + window['passord' + id] + "&passordet=" + passordet + "&merknad=" + merknad,
                method: 'POST',
                success: function (data) {
                    //$('#ledig' + id).('hide');
                    $('.modal-backdrop').hide();
                    //$(".container").replaceWith($('.container', $(data)));
                    $(".container").replaceWith($('.container', $(data)));
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
                    $("#innhold").replaceWith($('#innhold', $(data)));
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
                    $('.modal-backdrop').hide();
                    $("#innhold").replaceWith($('#innhold', $(data)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function taBortgittVaktmedPassord(id, vaktId) {
            var passordet = document.getElementById(vaktId * 100).value;
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=4&id=' + id + '&vaktId=' + vaktId + "&passordet=" + passordet,
                method: 'POST',
                success: function (data) {
                    //$(".container").replaceWith($('.container', $(data)));
                    $('.modal-backdrop').hide();
                    $("#innhold").replaceWith($('#innhold', $(data)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        <?php /*leggTilForslag(<?php echo $vaktbytte->getVaktId(); ?>,<?php echo $vakt->getId();?>*/ ?>
        function leggTilForslag(fraId, tilId, modalId) {
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=5&fraId=' + fraId + '&tilId=' + tilId,
                method: 'POST',
                success: function (data) {
                    $('#' + modalId).modal('hide');
                    //$(".container").replaceWith($('.container', $(data)));
                    $('.modal-backdrop').hide();
                    $("#innhold").replaceWith($('#innhold', $(data)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function leggTilForslagMedPassord(fraId, tilId, modalId) {
            var passordet = document.getElementById(fraId * 10000).value;
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=6&fraId=' + fraId + '&tilId=' + tilId + "&passordet=" + passordet,
                method: 'POST',
                success: function (data) {
                    //$('#' + modalId).modal('hide');
                    //$(".container").replaceWith($('.container', $(data)));
                    $('.modal-backdrop').hide();
                    $("#innhold").replaceWith($('#innhold', $(data)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }

        function byttVakt(fraId, tilId) {
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=7&fraId=' + fraId + '&tilId=' + tilId,
                method: 'POST',
                success: function (data) {
                    $('#' + (306 * fraId)).modal("hide");
                    //$(".container").replaceWith($('.container', $(data)));
                    $('.modal-backdrop').hide();
                    $("#innhold").replaceWith($('#innhold', $(data)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
        }
        <?php /*onclick="fjernForslag(<?php echo $vaktbytte->getId();?>,<?php echo $vakt->getId();?>)">*/?>
        function fjernForslag(vaktbytte, vakt) {
            $.ajax({
                type: 'POST',
                url: '?a=vakt/bytte/',
                data: 'vaktbytte=8&vaktbyttet=' + vaktbytte + '&vakt=' + vakt,
                method: 'POST',
                success: function (data) {
                    //$(".container").replaceWith($('.container', $(data)));
                    $('.modal-backdrop').hide();
                    $("#innhold").replaceWith($('#innhold', $(data)));
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
        <?php if (isset($_SESSION['success']) && isset($_SESSION['msg'])) { ?>

            <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $_SESSION['msg']; ?>
            </div>
            <?php
        } elseif (isset($_SESSION['error']) && isset($_SESSION['msg'])) { ?>
            <div class="alert alert-danger fade in" id="danger" style="display:table; margin: auto; margin-top: 5%">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $_SESSION['msg']; ?>
            </div>
            <?php
        }
        unset($_SESSION['success']);
        unset($_SESSION['error']);
        unset($_SESSION['msg']);
        ?>
        <p></p>
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
                        //$tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
                        $tekst = $vakt->toString();
                        ?>
                        <tr>
                            <?php
                            if ($vakt->erFerdig() || $vakt->getBytte()) {
                                if ($visFerdig) {
                                    ?>
                                    <td class="celle_graa"><?php echo $tekst; ?></td>
                                    <?php
                                }
                            } elseif ($vakt->erForeslatt()) {
                                ?>
                                <td disabled><span title="Foreslått i bytte"
                                                   class="glyphicon glyphicon-refresh"></span> <?php echo $tekst; ?>
                                </td>
                                <?php
                            } else {
                                ?>
                                <td>
                                    <input type="button" class="btn btn-default"
                                           value="<?php echo $tekst ?>" data-toggle="modal"
                                           data-target="#ledig<?php echo $vakt->getId(); ?>"></td>
                                <div class="modal fade" id="ledig<?php echo $vakt->getId(); ?>" role="dialog">
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
                                                    <label><input type="radio"
                                                                  name="optradio<?php echo $vakt->getId(); ?>"
                                                                  id="bytt"
                                                                  onclick="setAction<?php echo $vakt->getId(); ?>(0)"
                                                                  value="0" checked="checked">Byttes</label>
                                                    <label><input type="radio"
                                                                  name="optradio<?php echo $vakt->getId(); ?>"
                                                                  id="gibort"
                                                                  onclick="setAction<?php echo $vakt->getId(); ?>(1)"
                                                                  value="1">Gis bort</label>
                                                </div>
                                                Passord?
                                                <div class="radio">
                                                    <label><input type="radio"
                                                                  name="optradio1<?php echo $vakt->getId(); ?>"
                                                                  id="uten"
                                                                  onclick="setPassord<?php echo $vakt->getId(); ?>(0,<?php echo $vakt->getId(); ?>)"
                                                                  value="1"
                                                                  checked="checked">Uten passord</label>
                                                    <label><input type="radio"
                                                                  name="optradio1<?php echo $vakt->getId(); ?>"
                                                                  id="med"
                                                                  onclick="setPassord<?php echo $vakt->getId(); ?>(1, <?php echo $vakt->getId(); ?>)"
                                                                  value="0">Med passord</label>
                                                </div>
                                                <input type="password" name="passord" placeholder="Passord"
                                                       id="<?php echo $vakt->getId(); ?>"
                                                       style="display:none;">
                                                <input type="text" name="merknad" placeholder="Merknad"
                                                       id="<?php echo $vakt->getId(); ?>merk">
                                                <input class="btn btn-sm btn-warning pull-right" type="button"
                                                       value="Bytt"
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

            if (count(intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId())) > 0) {
                visDineVakter();
            } else {
                ?>
                <p><span class="tekst_dinevakter">Du skal ikke sitte vakt eller så har du ingen vakter å bytte!</span>
                </p>
                <?php
            }
            ?>
        </div>
        <?php
        if (isset($visBytteListe)) { ?>
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
                                //continue;
                            }
                            /* @var \intern3\Vaktbytte $vaktbytte */
                            if(strtotime($vaktbytte->getVakt()->getDato()) < strtotime(date('Y-m-d'))){
                                continue;
                            }

                            $modalId = 'modal-' . date('m-d', strtotime($vaktbytte->getVakt()->getDato())) . '-' . $vaktbytte->getVakt()->getVakttype();
                            ?>
                            <tr>
                            <td>
                                <?php

                                if ($vaktbytte->harPassord()) {
                                    echo "<span title=\"Passordlåst\" class=\"glyphicon glyphicon-lock\"></span>";
                                }
                                if ($vaktbytte->getGisBort()) {
                                    echo "<span title=\"Gis bort\" class=\"glyphicon glyphicon-alert\"></span>";
                                } else {
                                    echo "<span title=\"Byttes\" class=\"glyphicon glyphicon-refresh\"></span>";
                                }
                                if ($vaktbytte->getVakt()->getBrukerId() != $cd->getAktivBruker()->getId() && $vaktbytte->getGisBort()) {
                                    echo '<input type="button" class="btn btn-sm btn-info pull-right" value="Ta vakt" data-toggle="modal" data-target="#' . $modalId . '">' . PHP_EOL;
                                } elseif ($vaktbytte->getVakt()->getBrukerId() != $cd->getAktivBruker()->getId() && !$vaktbytte->getGisBort()) {
                                    echo '<input type="button" class="btn btn-sm btn-info pull-right" value="Bytt" data-toggle="modal" data-target="#' . $modalId . '">' . PHP_EOL;
                                } else { ?>
                                    <input class="btn btn-sm btn-danger pull-right" type="button" value="Trekk"
                                           onclick="fjernVaktBytte(<?php echo $vaktbytte->getId(); ?>, <?php echo $vaktbytte->getVaktId(); ?>)">
                                    <?php
                                    if (!$vaktbytte->getGisBort() && $har_vakt) { ?>
                                        <input class="btn btn-sm btn-warning pull-right" type="button"
                                               value="Se forslag"
                                               data-toggle="modal"
                                               data-target="#<?php echo $vaktbytte->getId() * 306; ?>">
                                        <?php
                                    }
                                }
                                echo '<strong>' . ucfirst(strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato()))) . '</strong>' . PHP_EOL;
                                //echo '<strong>' . $vaktbytte->getVakt()->toString() . '</strong>' . PHP_EOL;
                                echo '<br>' . PHP_EOL;
                                echo ($bruker != null && $bruker->getPerson()) != null ? $bruker->getPerson()->getFulltNavn() : 'Fritz Müller';
                                $merknaden = $vaktbytte->getMerknad();
                                if ($merknaden != null) {
                                    echo "<br/>" . $vaktbytte->getMerknad();
                                }
                                ?>
                                <div class="modal fade" id="<?php echo $vaktbytte->getId() * 306; ?>" role="dialog">
                                    <?php //Dette er modalen for å se forslag til et vaktbytte. ?>
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content panel-primary">
                                            <div class="modal-header panel-heading">
                                                <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title"> <?php echo $vaktbytte->getVakt()->getVakttype() . '. vakt ' . strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato())); ?></h4>
                                            </div>
                                            <div class="modal-body" align="center">
                                                <table class="table table-bordered">
                                                    <?php
                                                    if (sizeof($vaktbytte->getForslagVakter()) > 0 && $vaktbytte->getForslagVakter() != null) {
                                                        foreach ($vaktbytte->getForslagVakter() as $forslag) {
                                                            if ($forslag != null) {
                                                                echo $forslag->getBruker()->getPerson()->getFulltNavn();
                                                                $output = $forslag->getVakttype() . '. vakt ' . strftime('%A %d/%m', strtotime($forslag->getDato()));
                                                                ?>:<br/>
                                                                <input class="btn btn-primary" type="button"
                                                                       value="<?php echo $output; ?>"
                                                                       onclick="byttVakt(<?php echo $vaktbytte->getVaktId(); ?>,<?php echo $forslag->getId(); ?>)">
                                                                <br/>
                                                                <?php
                                                            }
                                                        }
                                                    } elseif (count($vaktbytte->getForslagVakter()) < 0 || $vaktbytte->getForslagVakter() == null) { ?>
                                                        <p>Det ser ikke ut til at du har fått noe bytteforslag
                                                            (enda).</p>
                                                        <?php
                                                    }
                                                    ?>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Lukk
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="<?php echo $modalId;
                                //Dette er modalen for dialogen som dukker opp når man trykker på bytt eller trekk
                                ?>" role="dialog">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content panel-primary">
                                            <div class="modal-header panel-heading">
                                                <button type="button" class="close"
                                                        data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title"
                                                    align="center"><?php echo $vaktbytte->getVakt()->getVakttype() . '. vakt ' . strftime('%A %d/%m', strtotime($vaktbytte->getVakt()->getDato())); ?></h4>
                                            </div>
                                            <div class="modal-body" align="center">
                                                <?php if (!$vaktbytte->getGisBort() && !$vaktbytte->harPassord()) { ?>
                                                    <p>Hvilken vakt vil du foreslå å bytte?</p>
                                                    <table class="table table-bordered">
                                                        <?php //visDineVakter(false);
                                                        if (count(intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId())) > 0) {
                                                            foreach (intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId()) as $vakt) {
                                                                if ($vakt == null || $vakt->erFerdig() || \intern3\Vaktbytte::medVaktId($vakt->getId()) != null/*|| ($vakt->erForeslatt() && $vakt->getVaktbytteDenneErMedIId() != $vaktbytte->getId())*/) {
                                                                    continue;
                                                                }
                                                                $tid = strtotime($vakt->getDato());
                                                                if (in_array($vaktbytte->getId(), $vakt->getVaktbytteDenneErMedIId())) {
                                                                    $tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <input class="btn btn-danger" type="button"
                                                                                   value="Trekk forslag: <?php echo $tekst; ?>"
                                                                                   onclick="fjernForslag(<?php echo $vaktbytte->getId(); ?>,<?php echo $vakt->getId(); ?>)">
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                /*} elseif (false && count($vakt->getVaktbytteDenneErMedIId()) != 0 && !in_array($vaktbytte->getId(),$vakt->getVaktbytteDenneErMedIId())) {
                                                                    //continue;*/
                                                                } else {
                                                                    $tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <input class="btn btn-primary" type="button"
                                                                                   value="<?php echo $tekst; ?>"
                                                                                   onclick="leggTilForslag(<?php echo $vaktbytte->getVaktId(); ?>,<?php echo $vakt->getId(); ?>,'<?php echo $modalId; ?>')">
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            echo "Du har ingen vakter å bytte bort!";
                                                        }
                                                        ?>
                                                    </table>
                                                    <?php
                                                } elseif (!$vaktbytte->getGisBort() && $vaktbytte->harPassord()) { ?>
                                                    <p>Hvilken vakt vil du foreslå å bytte?</p>
                                                    <table class="table table-bordered">
                                                        <?php
                                                        if (count(intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId())) > 0) {
                                                            foreach (intern3\VaktListe::medBrukerId($cd->getAktivBruker()->getId()) as $vakt) {
                                                                if ($vakt == null || $vakt->erFerdig() || \intern3\Vaktbytte::medVaktId($vakt->getId()) != null/*|| ($vakt->erForeslatt() && $vakt->getVaktbytteDenneErMedIId() != $vaktbytte->getId())*/) {
                                                                    continue;
                                                                }
                                                                $tid = strtotime($vakt->getDato());
                                                                $tekst = $vakt->getVakttype() . '. vakt ' . strftime('%A %d/%m', $tid);
                                                                if (in_array($vaktbytte->getId(), $vakt->getVaktbytteDenneErMedIId())) { ?>
                                                                    <tr>
                                                                        <td>
                                                                            <input class="btn btn-danger" type="button"
                                                                                   value="Trekk forslag: <?php echo $tekst; ?>"
                                                                                   onclick="fjernForslag(<?php echo $vaktbytte->getId(); ?>,<?php echo $vakt->getId(); ?>)">
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                } else {
                                                                    ?>

                                                                    <tr>
                                                                        <td>
                                                                            <input class="btn btn-primary" type="button"
                                                                                   value="<?php echo $tekst; ?>"
                                                                                   onclick="leggTilForslagMedPassord(<?php echo $vaktbytte->getVaktId(); ?>,<?php echo $vakt->getId(); ?>,'<?php echo $modalId; ?>')">
                                                                        </td>
                                                                    </tr>
                                                                <?php }
                                                            }
                                                        } else {
                                                            echo "Du har ingen vakter å bytte bort!";
                                                        } ?>
                                                    </table>
                                                    Dette vaktbyttet er passordbeskyttet. Vennligst skriv inn passordet under.
                                                    <input type="password"
                                                           id="<?php echo $vaktbytte->getVaktId() * 10000; ?>"
                                                           name="passord" placeholder="Passord">
                                                    <?php
                                                } elseif ($vaktbytte->getGisBort() && $vaktbytte->harPassord()) { ?>
                                                    <p>Du ønsker altså å ta denne vakta?</p>
                                                    <input class="btn btn-primary" type="button" value="Ja!"
                                                           onclick="taBortgittVaktmedPassord(<?php echo $vaktbytte->getId(); ?>,<?php echo $vaktbytte->getVaktId(); ?>)">
                                                    <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">
                                                        Nei!
                                                    </button><br/><br/>
                                                    <input type="password"
                                                           id="<?php echo $vaktbytte->getVaktId() * 100; ?>"
                                                           name="passord" placeholder="Passord">
                                                <?php } else { ?>
                                                    <p>Du ønsker altså å ta denne vakta?</p>
                                                    <input class="btn btn-primary" type="button" value="Ja!"
                                                           onclick="taBortgittVakt(<?php echo $vaktbytte->getId(); ?>,<?php echo $vaktbytte->getVaktId(); ?>)">
                                                    <button type="button" class="btn btn-danger"
                                                            data-dismiss="modal">
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
                            </tr><?php } ?>
                    </table>
                </div>
                <?php
            }
        }
        ?>
    </div>
<?php
require_once('bunn.php');
?>