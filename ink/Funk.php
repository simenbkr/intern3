<?php

namespace intern3;

class Funk {
	public static function erDatoGyldigFormat($dato) {
		return preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $dato);
	}
	public static function finsDato($dato) {
		return checkdate(substr($dato, 5, 2), substr($dato, 8, 2), substr($dato, 0, 4));
	}
	public static function timerTilTidForm ($timer) {
		$fortegn = $timer == 0 ? 1 : $timer / abs($timer);
		$minutter = abs($timer);
		$timer = floor(abs($timer));
		$minutter -= $timer;
		$minutter *= 60;
		$minutter = round($minutter);
		return $fortegn * $timer . ($minutter > 0 ? ':' . $minutter : '');
		}
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function getLastBrukerId(){
        $st = DB::getDB()->prepare('SELECT id FROM bruker ORDER BY id DESC LIMIT 1');
        $st->execute();
        return $st->fetchColumn();
    }

    public static function generatePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzæøåABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789æøå';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

}

?>