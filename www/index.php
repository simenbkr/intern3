<?php

$arg = isset($_GET['a']) ? explode('/', $_GET['a']) : array();

require_once('../ink/autolast.php');

$cd = new intern3\CtrlData($arg);
$ctrl = new intern3\HovedCtrl($cd);
$ctrl->bestemHandling();

?>