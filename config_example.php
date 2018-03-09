<?php

namespace intern3;

define('PATH', __DIR__);
define('DB_USER', "marksome");
define('DB_PW', "123456");
define('DB_DOMAIN', 'localhost');

if($_SERVER['SERVER_NAME'] == 'intern.singsaker.no' || $_SERVER['SERVER_NAME'] == 'dobbel.singsaker.no') {
    define('DB_NAME', 'intern3');
} else {
    define('DB_NAME', 'intern3_dev');
}
