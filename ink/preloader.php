<?php

namespace intern3;

require_once(__DIR__ . '/../config.php');

foreach (array('modell', 'visning', 'kontroller') as $mappe) {
    $iterator = new \DirectoryIterator(PATH . '/' . $mappe . '/');

    foreach ($iterator as $fileinfo) {
        if($fileinfo->isDot() || $fileinfo->isDir()) {
            continue;
        }
        opcache_compile_file($fileinfo->getPath() . '/' . $fileinfo->getFilename());
    }

}