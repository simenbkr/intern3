<?php
namespace intern3;
require_once('../../ink/DB.php');
require_once('../../ink/autolast.php');
?>
<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
<div class="container">
<div class="row">
<div class="col-lg-12 text-center">
<?php

if(isset($_GET['char'])){
    $char = $_GET['char'];

    $st = DB::getDB()->prepare('SELECT id, fornavn, mellomnavn, etternavn FROM beboer WHERE (rolle_id=1 OR rolle_id=2) AND etternavn like :ch ORDER BY fornavn, etternavn ASC');
    $st->bindParam(':ch',$char);
    $st->execute();

    $rader = $st->fetchAll();

    foreach($rader as $rad){
        echo "test";
        echo "$rad[fornavn] $rad[etternavn]";
    }



}
else{ ?>
    <h1> Trykk på første bokstav i ditt etternavn </h1>
    <h2>
        <?php

        for ($i = 65; $i < 91; $i++) {

            if ($i == 75 || $i == 84) {
                echo "<br/><br/>";
            }
            echo "[<a href='vaktbytte.php?char=" . chr($i) . "'>" . chr($i) . "</a>] ";
        }
        echo "[<a href='vaktbytte.php?char=Æ'>Æ</a>]";
        echo "[<a href='vaktbytte.php?char=Å'>Ø</a>]";
        echo "[<a href='vaktbytte.php?char=Å'>Å</a>]";
        echo "<br/><br/>";
        echo "[<a href='vaktbytte.php?char=torild'>TORILD FIVE</a>]";

        }

?>
    <br><br/>
        <a href="javascript:history.back()">TILBAKE</a>
    </h2>

</div>
</div>
</div>