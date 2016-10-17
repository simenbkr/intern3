<?php
require_once('topp.php');
?>
<div class="container">
    <div class="row">
<?php
foreach($aktuelle as $beboer){
    echo "<h2>";
    echo $beboer->getFulltNavn() . "<br/>";
    echo "</h2>";
}
?>
</div>
</div>



<?php
require_once('bunn.php');
?>
