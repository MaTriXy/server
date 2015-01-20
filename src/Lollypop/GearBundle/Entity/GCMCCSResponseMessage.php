<?php

namespace Lollypop\GearBundle\Entity;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSResponseMessage
 *
 * @author seshachalam
 */
class GCMCCSResponseMessage implements \JsonSerializable
{

    private $from;
    private $message_id;
    private $message_type; // ack, nack, control
    private $error;
    private $error_description;
    private $control_type; //CONNECTION_DRAINING

    public function __construct($from = null, $message_id = null,
                                $message_type = null)
    {
        $this->from = $from;
        $this->message_id = $message_id;
        $this->message_type = $message_type;
    }

    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setMessageId($messageId)
    {
        $this->message_id = $messageId;
        return $this;
    }

    public function getMessageId()
    {
        return $this->message_id;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setErrorDescription($errorDescription)
    {
        $this->error_description = $errorDescription;
        return $this;
    }

    public function getErrorDescription()
    {
        return $this->error_description;
    }

    public function setControleType($controlType = 'CONNECTION_DRAINING')
    {
        $this->control_type = $controlType;
        return $this;
    }

    public function getControlType()
    {
        return $this->control_type;
    }

    public function jsonSerialize()
    {
        return array('from' => $this->from, 'message_id' => $this->message_id, 'message_type' => $this->message_type);
    }

}
