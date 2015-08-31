<?php

namespace intern3;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(function($klasse) {
	if (strpos($klasse, __NAMESPACE__ . '\\') !== 0) {
		return;
	}
	$klasse = substr($klasse, strlen(__NAMESPACE__) + 1);
	foreach (array('modell', 'visning', 'kontroller', 'ink') as $mappe) {
		$sti = '..' . DS . $mappe . DS . $klasse . '.php';
		if (file_exists($sti)) {
			require_once($sti);
			return;
		}
	}
	throw new \Exception('Kan ikke laste ' . $klasse);
});

?>