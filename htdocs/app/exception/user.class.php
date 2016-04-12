<?php

/**
 * User exception
 *
 * It can be printed to user
 */
class UserException extends Exception {

    public function __construct($message = null, $code = 0, Exception $previous = null) {

        // Add a default message
        if (!isset($message)) $message = 'Une erreur est survenue';
        
        // Call parent constructor
        parent::__construct($message, $code, $previous);

    }

}
