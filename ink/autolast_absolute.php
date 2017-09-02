<?php

namespace intern3;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(function($klasse) {
    if (strpos($klasse, __NAMESPACE__ . '\\') !== 0) {
        return;
    }
    $navn = substr($klasse, strlen(__NAMESPACE__) + 1);
    foreach (array('modell', 'visning', 'kontroller', 'ink') as $mappe) {
        $sti = '/var/www/intern.singsaker.no' . DS . $mappe . DS . $navn . '.php';
        if (file_exists($sti)) {
            require_once($sti);
            return;
        }
    }
    throw new \Exception('Kan ikke laste ' . $navn);
});

?>