<?php

namespace Lollypop\GearBundle\Entity;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSReceiptNotificationMessage
 *
 * @author seshachalam
 */
class GCMCCSReceiptNotificationMessage implements \JsonSerializable
{

    private $category;
    private $data; /*
     * “message_status":"MESSAGE_SENT_TO_DEVICE",
      “original_message_id”:”m-1366082849205”
      “device_registration_id”: “REGISTRATION_ID”
     */
    private $message_id;
    private $message_type; //receipt
    private $from;

    public function __construct($category = null, $data = array(),
                                $message_id = null, $message_type = null,
                                $from = null)
    {
        $this->category = $category;
        $this->data = $data;
        $this->message_id = $message_id;
        $this->message_type = $message_type;
        $this->from = $from;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setData($data = array())
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this;
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

    public function setMessageType($messageType = 'receipt')
    {
        $this->message_type = $messageType;
        return $this;
    }

    public function getMessageType()
    {
        return $this->message_type;
    }

    public function setFrom($from = 'gcm.googleapis.com')
    {
        $this->from = $from;
        return $this;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function jsonSerialize()
    {
        return array('category' => $this->category, 'data' => $this->data, 'message_id' => $this->message_id,
            'message_type' => $this->message_type, 'from' => $this->from);
    }

}
