<?php

namespace Lollypop\GearBundle\Entity;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMMessage
 *
 * @author seshachalam
 */
class GCMCCSUpstreamMessage implements \JsonSerializable
{

    private $channel;
    private $type;
    private $category;
    private $data;
    private $message_id;
    private $from;
    
    public function __construct($category = null, $data = array(),
                                $message_id = null, $from = null)
    {
        $this->category = $category;
        $this->data = $data;
        $this->message_id = $message_id;
        $this->from = $from;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function getType()
    {
        return $this->type;
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

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
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

    public function setFrom($from)
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
            'from' => $this->from);
    }

}
