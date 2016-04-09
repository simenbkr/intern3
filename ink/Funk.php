<?php

namespace intern3;

class Funk {
	public static function erDatoGyldigFormat($dato) {
		return preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $dato);
	}
	public static function finsDato($dato) {
		return checkdate(substr($dato, 5, 2), substr($dato, 8, 2), substr($dato, 0, 4));
	}
}

?>