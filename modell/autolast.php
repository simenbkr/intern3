<?php

namespace intern3\modell;

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

spl_autoload_register(function($klasse) {
	if (strpos($klasse, __NAMESPACE__ . '\\') !== 0) {
		return;
	}
	$sti = dirname(__FILE__) . DS . $klasse . '.php';
	if (!file_exists($sti)) {
		throw new Exception('Kan ikke laste ' . $klasse);
	}
	require_once($sti);
});

?>