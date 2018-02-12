<?php

setlocale(LC_ALL, 'nb_NO.utf-8');
date_default_timezone_set('Europe/Oslo');
ini_set('memory_limit', '256M');
ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);
ini_set('session.gc_maxlifetime', 36000000);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1000);

$arg = isset($_GET['a']) ? explode('/', $_GET['a']) : array();

//For egne klasser.
require_once('../ink/errorhandlers.php');
require_once ('../ink/minimizer.php');
require_once('../ink/autolast.php');

//Composer-klasser.
require '../vendor/autoload.php';

/* Egen mappe for sesjoner for at denne filen skal bestemme konfigurasjoner
 * og ikke lokale miljøer (f.eks Debian og Ubuntu har Cronjobs som rengjør
 * "utgåtte" sesjoner, noe som vil påvirke krysselista).
*/
session_save_path('../sessions');
session_set_cookie_params(3600*24*100*100,"/");

header('Content-Type: text/html; charset=utf-8');

session_start();
$cd = new intern3\CtrlData($arg);
$ctrl = new intern3\HovedCtrl($cd);
$ctrl->bestemHandling();

?>