<?php
require_once('topp.php');
?>


<div class="container">
    <div class="row">
        <div class="col-lg-12 text-center">

            <h1> Trykk på første bokstav i ditt etternavn </h1>
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
                echo "<br/><br/>";
                echo "[<a href='?a=journal/vaktbytte/TORILD'>TORILD FIVE</a>]";

                ?>
                <br><br/>
                <a href="javascript:history.back()">TILBAKE</a>
            </h2>

        </div>
    </div>
</div>

<?php
require_once('bunn.php');
?>