<?php
namespace intern3;
require_once("../ink/autolast.php");

// Vakt::lagVakt('2','2017-01-10');

$modalId ='modal-2017-01-10-1';
$vakttype = substr($modalId,-1);
$dato = substr($modalId,6,-2);
echo $vakttype;
echo '<br/>';
echo $dato;

?>
