<?php
error_reporting(E_ALL);
ini_set("display_errors", 0); // NO MOSTRAMOS LOS ERRORES EN NAVEGADOR
function customErrorHandler($errno, $errstr, $errfile, $errline){
    $message = "- ".date('Y-m-d H:i:s')."Error: [$errno] $errstr - $errfile: $errline ";
    error_log($message . PHP_EOL, 3, __DIR__."/../logs/error_log.txt");
}

function customExceptionHandler($exception){
    $message = "- ".date('Y-m-d H:i:s')." Uncaught Exception: ".$exception->getMessage() . " in ".
    $exception->getFile() . " on line: ". $exception->getLine();
    error_log($message . PHP_EOL, 3, __DIR__."/../logs/error_log.txt");
}
set_error_handler("customErrorHandler");
set_exception_handler("customExceptionHandler");