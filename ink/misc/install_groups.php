<?php

namespace intern3;


require '../../vendor/autoload.php';

/* Gotta install client_secrets and shit. */

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

mkdir(__DIR__ . '/../../vendor/simenbkr/groupmanage/src/credentials');

$a = new \Group\GroupManage();
$b = $a->listGroup("sing-korr@singsaker.no");

var_dump($b);

print "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!\n
Be sure to add client_secrets to the correct relative path -
the same folder as GroupManage runs from - usually /vendor/groupmanage/src/\n
!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
\n";

