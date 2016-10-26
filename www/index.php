<?php

setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');
ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);
$arg = isset($_GET['a']) ? explode('/', $_GET['a']) : array();

require_once('../ink/autolast.php');

header('Content-Type: text/html; charset=utf-8');

$cd = new intern3\CtrlData($arg);
$ctrl = new intern3\HovedCtrl($cd);
$ctrl->bestemHandling();

?>
