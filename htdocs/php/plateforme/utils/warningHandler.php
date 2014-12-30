<?php

class WarningHandler{
    
    public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext){
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    
}

?>
