<?php

namespace Lollypop\GearBundle\Exception;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestException
 *
 * @author seshachalam
 */
class PopException extends \RuntimeException implements ExceptionInterface
{

    private $statusCode;

    public function __construct($statusCode, $message = null,
                                \Exception $previous = null, $code = 0)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}
