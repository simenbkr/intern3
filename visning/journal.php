<?php

require_once('topp.php');

if (isset($success)) {
    ?>
    <div class="alert alert-success fade in" id="success" style="display:table; margin: auto; margin-top: 5%">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Du er nå logget inn på journalen og ut av din egen bruker.
    </div>
    <?php
    unset($success);
}
unset($success);
?>

<div class="col-sm-6 col-xs-12" style="margin: auto; margin-top: 5%">
	<h1>Journal</h1>

    <ul>
        <li><a href="?a=journal/krysseliste">Krysseliste</a></li>
        <li><a href="?a=journal/signering">Signering</a></li>
        <li><a href="?a=journal/pafyll">Påfyll</a></li>
        <li><a href="?a=journal/vaktbytte">Vaktbytte</a></li>
    </ul>
<?php

require_once('bunn.php');

?>
