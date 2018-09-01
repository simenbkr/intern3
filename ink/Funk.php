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

    public static function tidTilTimer($sek){
        $min = (($sek/60)%60) == 0 ? "00" : (($sek/60)%60);
        $timer = floor(($sek/60)/60);
        $min = strlen($min) == 1 ? "0$min" : $min;
        return "$timer:$min";
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

    public static function getLastVinId(){
        $st = DB::getDB()->prepare('SELECT id FROM vin ORDER BY id DESC LIMIT 1');
        $st->execute();
        return $st->fetchColumn();
    }

    public static function generatePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }

    public static function isValidEmail($kandidat){
        //Ref http://www.w3schools.com/php/filter_validate_email.asp
        if (!filter_var($kandidat, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        } else {
            return false;
        }
    }

    public static function generateSemesterString($dato){
        //$dato -> date-objekt.

        $year = date('Y', strtotime($dato));
        $dato_time = strtotime($dato);

        if ($dato_time > strtotime("$year-07-01")){
            return "host-$year";
        } else {
            return "var-$year";
        }
    }

    public static function genNextSemsterStrings(){

        $semestere = array();
        $dato = date('Y-m-d');
        for($i = 1; $i < 5; $i++){
            $semestere[] = self::generateSemesterString($dato);
            $dato = date('Y-m-d', strtotime("+6 months" . $dato));
        }

        return $semestere;
    }

    public static function genReadableSemStr($dato){
        $year = date('Y', strtotime($dato));
        $dato_time = strtotime($dato);

        if ($dato_time > strtotime("$year-07-01")){
            return "Høst $year";
        } else {
            return "Vår $year";
        }
    }

    public static function semStrToReadable($str){
	    if(strpos($str, "var-") !== false){
            return str_replace("var-", "Vår ", $str);
        } else {
	        return str_replace('host-', "Høst ", $str);
        }
    }

    public static function semStrToUnix($str){
	    //str på format semester-år
	    $year = explode('-', $str)[1];
        if(strpos($str, "var-") !== false){
            return strtotime("$year-02-01");
        } else {
            return strtotime("$year-09-01");
        }
    }

    public static function setSuccess($text){
	    $_SESSION['success'] = 1;
	    $_SESSION['msg'] = $text;
    }

    public static function setError($text){
	    $_SESSION['error'] = 1;
	    $_SESSION['msg'] = $text;
    }

    public static function compareFloats($delta, $a, $b){
	    return (abs($a - $b) < $delta);
    }

    public static function getSemesterStart($str){
        $year = explode('-', $str)[1];
	    if(strpos($str, 'host') !== false){
	        return date('Y-m-d', strtotime("$year-08-01"));
        }
        return date('Y-m-d', strtotime("$year-01-01"));
    }

    public static function getSemesterEnd($str){
        $year = explode('-', $str)[1];
        if(strpos($str, 'host') !== false){
            return date('Y-m-d', strtotime("$year-12-31"));
        }
        return date('Y-m-d', strtotime("$year-07-31"));
    }

}

?>