<?php

namespace intern3\kjerne;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(function($klasse) {
	if (strpos($klasse, __NAMESPACE__ . '\\') !== 0) {
		return;
	}
	foreach (array('modell', 'kontroller', 'ressurs') as $mappe) {
		$sti = dirname(__FILE__) . DS . $mappe . DS . $klasse . '.php';
		if (file_exists($sti)) {
			require_once($sti);
			return;
		}
	}
	throw new Exception('Kan ikke laste ' . $klasse);
});

?>