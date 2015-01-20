<?php

namespace Lollypop\GearBundle\Entity;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMCCSDownstreamMessage
 *
 * @author seshachalam
 */
class GCMCCSDownstreamMessage implements \JsonSerializable
{

    private $channel;
    private $type;
    private $to;
    private $message_id;
    private $data;
    private $time_to_live;
    private $delay_while_idle;
    private $delivery_receipt_requested;
    private $extras;

    public function __construct($to = null, $message_id = null, $data = array(),
                                $time_to_live = 0, $delay_while_idle = false,
                                $delivery_receipt_requested = true)
    {
        $this->to = $to;
        $this->message_id = $message_id;
        $this->data = $data;
        $this->time_to_live = $time_to_live;
        $this->delay_while_idle = $delay_while_idle;
        $this->delivery_receipt_requested = $delivery_receipt_requested;
        $this->extras = array();
    }

    public function setChannel($channel, $type = null)
    {
        $this->channel = $channel;
        $this->type = $type;

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

    public function setTo($to)
    {
        $this->to = $to;
        return $this;
    }

    public function getTo()
    {
        return $this->to;
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

    public function setData($data = array())
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setTimeToLive($timeToLive = 0)
    {
        $this->time_to_live = $timeToLive;
        return $this;
    }

    public function getTimeToLive()
    {
        return $this->time_to_live;
    }

    public function setDelayWhileIdle($delayWhileIdle = false)
    {
        $this->delay_while_idle = $delayWhileIdle;
        return $this;
    }

    public function getDelayWhileIdle()
    {
        return $this->delay_while_idle;
    }

    public function setDeliveryReceiptRequested($deliveryReceiptRequested = true)
    {
        $this->delivery_receipt_requested = $deliveryReceiptRequested;
        return $this;
    }

    public function getDeliveryReceiptRequested()
    {
        return $this->delivery_receipt_requested;
    }
    
    public function setExtras(array $extras)
    {
        $this->extras = $extras;
        return $this;
    }
    
    public function getExtras()
    {
        return $this->extras;
    }

    public function jsonSerialize()
    {
        $arr = array_merge($this->data, $this->extras);
        
        return array('to' => $this->to, 'message_id' => $this->message_id, 'data' => $arr,
            'time_to_live' => $this->time_to_live, 'delay_while_idle' => $this->delay_while_idle,
            'delivery_receipt_requested' => $this->delivery_receipt_requested);
    }

}
