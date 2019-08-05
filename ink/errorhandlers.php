<?php

namespace intern3;

require_once ('autolast_absolute.php');

function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
{
    $error = "lvl: " . $error_level . " | msg:" . $error_message . " | file:" . $error_file . " | ln:" . $error_line;
    switch ($error_level) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            mylog($error, "fatal");
            break;
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            mylog($error, "error");
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            mylog($error, "warn");
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            mylog($error, "info");
            break;
        case E_STRICT:
            mylog($error, "debug");
            break;
        default:
            mylog($error, "warn");
    }
}

function shutdownHandler() //will be called when php script ends.
{
    $lasterror = error_get_last();
    switch ($lasterror['type']) {
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_PARSE:
            $error = "[SHUTDOWN] lvl:" . $lasterror['type'] . " | msg:" . $lasterror['message'] . " | file:" . $lasterror['file'] . " | ln:" . $lasterror['line'];
            mylog($error, "fatal");
            //$cd = new CtrlData($arg = isset($_GET['a']) ? explode('/', $_GET['a']) : array());
            //$visning = new Visning($cd);
            //$visning->set('skjulMeny',1);
            //$visning->set('url', '?a=feil');
            //$visning->vis('redirect.php');
            exit();
    }
}

set_error_handler("\intern3\\errorHandler", E_ALL | E_STRICT);
register_shutdown_function("\intern3\\shutdownHandler");

function mylog($error, $errlvl)
{

    $mail_content = '';

    if(!is_null(Session::getAktivBruker())) {
        $mail_content .= "BRID: " . Session::get('brid');
        $mail_content .= "\nBEID: " . Session::get('beid');
    }

    $mail_content .= "Request-method: " . $_SERVER['REQUEST_METHOD'];
    $mail_content .= "Request URI: " . $_SERVER['REQUEST_URI'];

    switch ($errlvl) {
        case 'fatal':
            Epost::sendEpost('data@singsaker.no', 'Fatal error på internsida',$mail_content . $error);
            break;
        case 'error':
            Epost::sendEpost('data@singsaker.no', 'Error på internsida',$mail_content . $error);
            break;
    }
}