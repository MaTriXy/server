<?php

namespace Lollypop\GearBundle\Event;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GCMMessageEvent
 *
 * @author seshachalam
 */
use Symfony\Component\EventDispatcher\Event;
use Lollypop\GearBundle\Entity\GCMCCSUpstreamMessage;

class GCMCCSUpstreamEvent extends Event implements GCMCCSUpstreamInterface
{

    protected $GCMCCSUpstreamMessage;

    public function __construct(GCMCCSUpstreamMessage $GCMCCSUpstreamMessage)
    {
        $this->GCMCCSUpstreamMessage = $GCMCCSUpstreamMessage;
    }

    public function getGCMCCSUpstreamMessage()
    {
        return $this->GCMCCSUpstreamMessage;
    }

}
