<?php

require_once('topp.php');

?>
<h1>Romskjema (<?php echo $cd->getAktivBruker()->getPerson()->getRomHistorikk()->getAktivtRom()->getNavn(); ?>)</h1>
<?php

require_once('bunn.php');

?>
