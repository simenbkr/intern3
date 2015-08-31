<?php

$sider = isset($_GET['s']) ? explode('/', $_GET['s']) : array();

require_once('../ink/autolast.php');

$cd = new intern3\CtrlData($sider);
$ctrl = new intern3\HovedCtrl($cd);
$ctrl->bestemHandling();

?>