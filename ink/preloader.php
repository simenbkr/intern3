<?php

/*
 * In the appropriate PHP.ini, add: opcache.preload=/var/www/intern.singsaker.no/ink/preloader.php
 */

namespace intern3;

require_once(__DIR__ . '/../config.php');

foreach (array('modell', 'kontroller') as $mappe) {
    $iterator = new \DirectoryIterator(PATH . '/' . $mappe . '/');

    foreach ($iterator as $fileinfo) {
        if($fileinfo->isDot() || $fileinfo->isDir()) {
            continue;
        }
        opcache_compile_file($fileinfo->getPath() . '/' . $fileinfo->getFilename());
    }

}