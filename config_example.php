<?php

namespace intern3;

define('PATH', __DIR__);
define('DB_USER', "intern3");
define('DB_PW', "intern3");
define('DB_DOMAIN', 'localhost');

if($_SERVER['SERVER_NAME'] == 'intern.singsaker.no' || $_SERVER['SERVER_NAME'] == 'dobbel.singsaker.no') {
    define('DB_NAME', 'intern3');
} else {
    define('DB_NAME', 'intern3_dev');
}

define('SING_ALLE', 'sing-alle@singsaker.no');
define('SING_SLARV', 'sing-slarv@singsaker.no');
define('SING_JENTER', 'sing-jenter@singsaker.no');
define('SING_GUTTER', 'sing-gutter@singsaker.no');
define('SING_KORR', 'sing-korr@singsaker.no');
define('SING_MISJ', 'sing-misj@singsaker.no');
define('SING_VETERAN', 'sing-veteran@singsaker.no');


define("MAIL_LISTS", array(SING_ALLE, SING_SLARV, SING_JENTER, SING_GUTTER));
define("PUBLIC_MAIL", array(SING_ALLE, SING_SLARV));
define("REGIBILDER","regibilder/");
