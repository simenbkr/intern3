<?php
/*
	Fil for å koble opp mot singbasen som ligger i postgressql
*/

$username = 'utvalget';
$password = '***REMOVED***';
$hostspec = 'dev.singsaker.no';
$database = 'singbasen';

pg_connect("host=".$hostspec." dbname=".$database." user=".$username." password=".$password);

?>
