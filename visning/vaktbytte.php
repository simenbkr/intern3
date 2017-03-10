<?php
require_once ('topp_journal.php');
require_once('topp.php');
?>
    <script>
        function bytte(brukerId) {
            $.ajax({
                type: 'POST',
                url: '?a=journal/vaktbytte',
                data: 'brukerId=' + brukerId,
                method: 'POST',
                success: function (data) {
                    //$(".container").replaceWith($('.container', $(data)));
                },
                error: function (req, stat, err) {
                    alert(err);
                }
            });
            location.reload();
        }
    </script>

    <div class="container" id="container">
      <h1>Journal » Vaktbytte</h1>
      <hr>
      </br>
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1>Nå sitter
                    <?php if ($vakt != null) {
                        echo $vakt->getFulltNavn();
                    } else {
                        echo "TORILD FIVE";
                    } echo " " . $denne_vakta->getVaktNr() . ".";?> vakt.
                </h1>
                <h2> Bytte vakt? Trykk på første bokstav i ditt etternavn </h2>
                <h2>
                    <?php

                    for ($i = 65; $i < 91; $i++) {

                        if ($i == 75 || $i == 84) {
                            echo "<br/><br/>";
                        }
                        echo "[<a href='?a=journal/vaktbytte/" . chr($i) . "'>" . chr($i) . "</a>] ";
                    }
                    echo "[<a href='?a=journal/vaktbytte/Æ'>Æ</a>]";
                    echo "[<a href='?a=journal/vaktbytte/Ø'>Ø</a>]";
                    echo "[<a href='?a=journal/vaktbytte/Å'>Å</a>]";
                    echo "<br/><br/>"; ?>
                    <input class="btn btn-default btn-block" type="submit" value="TORILD FIVE" onclick="bytte(0)">

                    <hr>
                    <a href="javascript:history.back()">TILBAKE</a>
                </h2>

            </div>
        </div>
    </div>

<?php
require_once('bunn.php');
?>
