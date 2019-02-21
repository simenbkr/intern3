<?php

/*
 * POST-skript for å håndtere søknader fra studenterhjem.singsaker.no
 */

define('SHARED_SECRET', 'test');
$url = 'http://localhost/intern3/www/?a=extern/soknad';
$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$auth_arr = array('secret' => SHARED_SECRET);

$postStr = http_build_query(array_merge($post, $auth_arr));
$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postStr
    )
);

$streamContext  = stream_context_create($options);
$result = file_get_contents($url, false, $streamContext);


//var_dump($result);